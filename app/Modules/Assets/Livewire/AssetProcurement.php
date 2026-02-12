<?php

namespace App\Modules\Assets\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Modules\Assets\Models\AssetProcurement as AssetProcurementModel;
use App\Modules\Assets\Models\AssetProcurementItem;
use App\Modules\Assets\Models\AssetProcurementQuote;
use App\Modules\Organization\Models\Department;
use App\Modules\Identity\Models\User;
use Illuminate\Support\Facades\Auth;

class AssetProcurement extends Component
{
    use WithFileUploads;

    public $procurements;
    public $departments;
    
    // Filters & Search
    public $search = '';
    public $filterStatus = '';

    // Slide-over State
    public $isModalOpen = false;
    public $editingId = null;
    public $activeTab = 'general'; // general, items, quotes

    // Form Fields - General
    public $title;
    public $code;
    public $department_id;
    public $reason;
    public $status = 'draft';
    public $total_estimated_cost = 0;

    // Form Fields - Items
    public $items = []; // Array of arrays: ['name', 'specs', 'qty', 'price', 'url']

    // Form Fields - Quotes
    public $quotes = []; // Array of arrays: ['supplier', 'price', 'file_url', 'is_selected', 'notes']
    public $newQuoteFile; 

    protected $listeners = ['refreshProcurement' => '$refresh'];

    public function mount()
    {
        $this->departments = Department::orderBy('name')->get();
        $this->loadData();
    }

    public function loadData()
    {
        $query = AssetProcurementModel::with(['requester', 'department', 'items'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%')
                  ->orWhere('code', 'like', '%'.$this->search.'%');
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $this->procurements = $query->get();
    }

    public function create()
    {
        $this->resetForm();
        $this->code = 'PR-' . date('Ymd') . '-' . rand(100, 999);
        $this->addItem(); // Add one empty item row by default
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->editingId = $id;
        $procurement = AssetProcurementModel::with(['items', 'quotes'])->findOrFail($id);

        $this->title = $procurement->title;
        $this->code = $procurement->code;
        $this->department_id = $procurement->department_id;
        $this->reason = $procurement->reason;
        $this->status = $procurement->status;
        $this->total_estimated_cost = $procurement->total_estimated_cost;

        // Load Items
        foreach ($procurement->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'name' => $item->item_name,
                'specs' => $item->specifications,
                'qty' => $item->quantity,
                'price' => $item->unit_price_estimate,
                'url' => $item->supplier_url,
            ];
        }

        // Load Quotes
        foreach ($procurement->quotes as $quote) {
            $this->quotes[] = [
                'id' => $quote->id,
                'supplier' => $quote->supplier_name,
                'price' => $quote->total_price,
                'file_url' => $quote->file_url,
                'is_selected' => $quote->is_selected,
                'notes' => $quote->notes,
            ];
        }

        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $data = [
            'title' => $this->title,
            'code' => $this->code,
            'department_id' => $this->department_id,
            'reason' => $this->reason,
            'status' => $this->status,
            'total_estimated_cost' => $this->calculateTotal(),
        ];

        if (!$this->editingId) {
            $data['requester_id'] = Auth::id() ?? 1;
            $procurement = AssetProcurementModel::create($data);
        } else {
            $procurement = AssetProcurementModel::find($this->editingId);
            $procurement->update($data);
            
            // Delete existing relations to simple sync (or handle smarter updates if needed)
            $procurement->items()->delete();
            $procurement->quotes()->delete();
        }

        // Save Items
        foreach ($this->items as $item) {
            AssetProcurementItem::create([
                'procurement_id' => $procurement->id,
                'item_name' => $item['name'],
                'specifications' => $item['specs'] ?? null,
                'quantity' => $item['qty'],
                'unit_price_estimate' => $item['price'] ?? 0,
                'supplier_url' => $item['url'] ?? null,
            ]);
        }

        // Save Quotes
        foreach ($this->quotes as $quote) {
            AssetProcurementQuote::create([
                'procurement_id' => $procurement->id,
                'supplier_name' => $quote['supplier'],
                'total_price' => $quote['price'] ?? 0,
                'file_url' => $quote['file_url'] ?? null, // File upload logic needs separate handling
                'is_selected' => $quote['is_selected'] ?? false,
                'notes' => $quote['notes'] ?? null,
            ]);
        }

        $this->isModalOpen = false;
        $this->loadData();
        session()->flash('message', 'Đã lưu đề xuất mua sắm.');
    }

    // Helper: Items Management
    public function addItem()
    {
        $this->items[] = ['name' => '', 'specs' => '', 'qty' => 1, 'price' => 0, 'url' => ''];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += ($item['qty'] * ($item['price'] ?? 0));
        }
        return $total;
    }

    // Helper: Quotes Management
    public function addQuote()
    {
        $this->quotes[] = ['supplier' => '', 'price' => 0, 'file_url' => '', 'is_selected' => false, 'notes' => ''];
    }

    public function removeQuote($index)
    {
        unset($this->quotes[$index]);
        $this->quotes = array_values($this->quotes);
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->title = '';
        $this->code = '';
        $this->department_id = '';
        $this->reason = '';
        $this->status = 'draft';
        $this->total_estimated_cost = 0;
        $this->items = [];
        $this->quotes = [];
        $this->activeTab = 'general';
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updatedSearch() { $this->loadData(); }
    public function updatedFilterStatus() { $this->loadData(); }

    public function render()
    {
        return view('livewire.asset-procurement')->layout('layouts.app');
    }
}
