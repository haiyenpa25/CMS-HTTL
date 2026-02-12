<?php

namespace App\Modules\Assets\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetCategory;
use App\Modules\Organization\Models\Department;
use App\Modules\Identity\Models\User;
use App\Modules\Membership\Models\Member;
use Illuminate\Validation\Rule;

class AssetManagement extends Component
{
    use WithFileUploads;

    public $assets;
    public $categories;
    public $departments;
    public $users;
    public $members;
    
    // Form fields
    public $assetId;
    public $code;
    public $name;
    public $category_id;
    public $brand; // Kept for backward compatibility, mapped to manufacturer logic if needed or just used as brand
    public $model; // New
    public $manufacturer; // New
    public $serial_number; // New
    public $purchase_date;
    public $warranty_expiry;
    public $price;
    public $current_value;
    public $status = 'Active';
    public $location_id;
    public $description;
    
    // New Responsibility Fields
    public $managed_by;
    public $used_by_member_id;
    
    // New Digital Profile Fields
    public $manual_url;
    public $image_url;
    
    // UI state
    public $isModalOpen = false;
    public $confirmingDeletion = false;
    public $idToDelete;
    public $search = '';
    public $filterStatus = '';
    public $filterCategory = '';
    public $activeTab = 'general'; // general, management, finance, files

    protected function rules()
    {
        return [
            'code' => ['required', 'string', Rule::unique('assets')->ignore($this->assetId)],
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date|after:purchase_date',
            'price' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Repairing,Broken,Lost,Disposed',
            'location_id' => 'nullable|exists:departments,id',
            'managed_by' => 'nullable|exists:users,id',
            'used_by_member_id' => 'nullable|exists:members,id',
            'description' => 'nullable|string',
            'manual_url' => 'nullable|url',
            'image_url' => 'nullable|url',
        ];
    }

    public function mount()
    {
        $this->loadData();
        $this->loadReferenceData();
    }

    public function loadReferenceData()
    {
        $this->categories = AssetCategory::orderBy('name')->get();
        $this->departments = Department::orderBy('name')->get();
        // Optimizing loading for dropdowns
        $this->users = User::select('id', 'name')->orderBy('name')->get(); 
        $this->members = Member::select('id', 'full_name')->orderBy('full_name')->limit(500)->get(); // Limiting for now, maybe need search later
    }

    public function loadData()
    {
        $query = Asset::with(['category', 'location', 'manager', 'user']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%')
                  ->orWhere('brand', 'like', '%' . $this->search . '%')
                  ->orWhere('model', 'like', '%' . $this->search . '%')
                  ->orWhere('serial_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        $this->assets = $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.asset-management')->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->generateAssetCode();
        $this->openModal();
    }

    public function generateAssetCode()
    {
        // Auto-generate code: TBT-001, TBT-002...
        $lastAsset = Asset::orderBy('id', 'desc')->first();
        if ($lastAsset && preg_match('/TBT-(\d+)/', $lastAsset->code, $matches)) {
            $number = intval($matches[1]) + 1;
        } else {
            $number = 1;
        }
        $this->code = 'TBT-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $this->assetId = $id;
        $this->code = $asset->code;
        $this->name = $asset->name;
        $this->category_id = $asset->category_id;
        $this->brand = $asset->brand;
        $this->model = $asset->model;
        $this->manufacturer = $asset->manufacturer;
        $this->serial_number = $asset->serial_number;
        $this->purchase_date = $asset->purchase_date?->format('Y-m-d');
        $this->warranty_expiry = $asset->warranty_expiry?->format('Y-m-d');
        $this->price = $asset->price;
        $this->current_value = $asset->current_value;
        $this->status = $asset->status;
        $this->location_id = $asset->location_id;
        $this->managed_by = $asset->managed_by;
        $this->used_by_member_id = $asset->used_by_member_id;
        $this->description = $asset->description;
        $this->manual_url = $asset->manual_url;
        $this->image_url = $asset->image_url;
        
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        $asset = Asset::updateOrCreate(['id' => $this->assetId], [
            'code' => $this->code,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'brand' => $this->brand,
            'model' => $this->model,
            'manufacturer' => $this->manufacturer,
            'serial_number' => $this->serial_number,
            'purchase_date' => $this->purchase_date ?: null,
            'warranty_expiry' => $this->warranty_expiry ?: null,
            'price' => is_numeric($this->price) ? $this->price : 0,
            'current_value' => is_numeric($this->current_value) ? $this->current_value : 0,
            'status' => $this->status,
            'location_id' => $this->location_id ?: null,
            'managed_by' => $this->managed_by ?: null,
            'used_by_member_id' => $this->used_by_member_id ?: null,
            'description' => $this->description,
            'manual_url' => $this->manual_url,
            'image_url' => $this->image_url,
        ]);

        // Calculate next maintenance date for new assets or if date changed
        if ($this->purchase_date) {
            $asset->calculateNextMaintenanceDate();
        }

        session()->flash('message', $this->assetId ? 'Cập nhật tài sản thành công.' : 'Thêm tài sản thành công.');
        $this->closeModal();
        $this->loadData();
    }

    public function delete($id)
    {
        $this->idToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function destroy()
    {
        if ($this->idToDelete) {
            Asset::find($this->idToDelete)->delete();
            session()->flash('message', 'Đã xóa tài sản.');
            $this->loadData();
        }
        $this->confirmingDeletion = false;
        $this->idToDelete = null;
    }

    public function openModal()
    {
        $this->isModalOpen = true;
        $this->activeTab = 'general';
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }
    
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    private function resetInputFields()
    {
        $this->assetId = null;
        $this->code = '';
        $this->name = '';
        $this->category_id = '';
        $this->brand = '';
        $this->model = '';
        $this->manufacturer = '';
        $this->serial_number = '';
        $this->purchase_date = '';
        $this->warranty_expiry = '';
        $this->price = '';
        $this->current_value = '';
        $this->status = 'Active';
        $this->location_id = '';
        $this->managed_by = '';
        $this->used_by_member_id = '';
        $this->description = '';
        $this->manual_url = '';
        $this->image_url = '';
        $this->activeTab = 'general';
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function updatedFilterStatus()
    {
        $this->loadData();
    }

    public function updatedFilterCategory()
    {
        $this->loadData();
    }
}
