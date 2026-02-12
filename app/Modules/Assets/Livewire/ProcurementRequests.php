<?php

namespace App\Modules\Assets\Livewire;

use Livewire\Component;
use App\Modules\Assets\Models\AssetProcurementRequest;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Organization\Models\Department;
use Illuminate\Support\Facades\Auth;

class ProcurementRequests extends Component
{
    public $requests;
    public $categories;
    public $departments;
    
    // Form fields
    public $requestId;
    public $item_name;
    public $category_id;
    public $quantity = 1;
    public $estimated_price;
    public $justification;
    public $priority = 'Medium';
    public $rejection_reason;
    
    // UI state
    public $isModalOpen = false;
    public $isApprovalModalOpen = false;
    public $selectedRequest;
    public $filterStatus = '';

    protected $rules = [
        'item_name' => 'required|string|max:255',
        'category_id' => 'nullable|exists:asset_categories,id',
        'quantity' => 'required|integer|min:1',
        'estimated_price' => 'required|numeric|min:0',
        'justification' => 'required|string|min:20',
        'priority' => 'required|in:Low,Medium,High,Urgent',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $query = AssetProcurementRequest::with(['requester', 'department', 'category', 'approver']);

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $this->requests = $query->orderBy('created_at', 'desc')->get();
        $this->categories = AssetCategory::orderBy('name')->get();
        $this->departments = Department::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.procurement-requests')->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        AssetProcurementRequest::create([
            'requested_by' => Auth::id(),
            'department_id' => Auth::user()->assignments()->where('status', 'Active')->first()->department_id ?? 1,
            'item_name' => $this->item_name,
            'category_id' => $this->category_id,
            'quantity' => $this->quantity,
            'estimated_price' => $this->estimated_price,
            'justification' => $this->justification,
            'priority' => $this->priority,
            'status' => 'Pending',
        ]);

        session()->flash('message', 'Đã gửi đề xuất mua sắm.');
        $this->closeModal();
        $this->loadData();
    }

    public function showApprovalModal($id)
    {
        $this->selectedRequest = AssetProcurementRequest::findOrFail($id);
        $this->isApprovalModalOpen = true;
    }

    public function approve()
    {
        if ($this->selectedRequest) {
            $this->selectedRequest->approve(Auth::user());
            session()->flash('message', 'Đã phê duyệt đề xuất.');
            $this->isApprovalModalOpen = false;
            $this->loadData();
        }
    }

    public function reject()
    {
        $this->validate(['rejection_reason' => 'required|string|min:10']);
        
        if ($this->selectedRequest) {
            $this->selectedRequest->reject(Auth::user(), $this->rejection_reason);
            session()->flash('message', 'Đã từ chối đề xuất.');
            $this->isApprovalModalOpen = false;
            $this->rejection_reason = '';
            $this->loadData();
        }
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->requestId = null;
        $this->item_name = '';
        $this->category_id = '';
        $this->quantity = 1;
        $this->estimated_price = '';
        $this->justification = '';
        $this->priority = 'Medium';
    }

    public function updatedFilterStatus()
    {
        $this->loadData();
    }
}
