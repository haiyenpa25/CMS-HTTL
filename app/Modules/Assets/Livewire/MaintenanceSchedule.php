<?php

namespace App\Modules\Assets\Livewire;

use Livewire\Component;
use App\Modules\Assets\Models\AssetMaintenance;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Services\MaintenanceScheduler;

class MaintenanceSchedule extends Component
{
    public $maintenances;
    public $overdueAssets;
    public $upcomingAssets;
    public $filterType = '';
    public $filterStatus = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $scheduler = new MaintenanceScheduler();
        
        $query = AssetMaintenance::with(['asset.category', 'reporter']);

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $this->maintenances = $query->orderBy('scheduled_date', 'desc')->get();
        $this->overdueAssets = $scheduler->getAssetsDueForMaintenance();
        $this->upcomingAssets = $scheduler->getUpcomingMaintenance(7);
    }

    public function render()
    {
        return view('livewire.maintenance-schedule')->layout('layouts.app');
    }

    public function markCompleted($id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);
        $maintenance->update([
            'status' => 'Completed',
            'completion_date' => now(),
        ]);

        // Update asset status back to Active if it was Repairing
        if ($maintenance->asset->status === 'Repairing') {
            $maintenance->asset->update(['status' => 'Active']);
        }

        // Recalculate next maintenance date
        $scheduler = new MaintenanceScheduler();
        $nextDate = $scheduler->calculateNextMaintenanceDate($maintenance->asset);
        $maintenance->asset->update(['next_maintenance_date' => $nextDate]);

        session()->flash('message', 'Đã đánh dấu hoàn thành bảo trì.');
        $this->loadData();
    }

    public function updatedFilterType()
    {
        $this->loadData();
    }

    public function updatedFilterStatus()
    {
        $this->loadData();
    }
}
