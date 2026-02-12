<?php

namespace App\Modules\Assets\Livewire;

use Livewire\Component;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetMaintenance;
use Illuminate\Support\Facades\Auth;

class QuickIncidentReport extends Component
{
    public $asset;
    public $description;
    public $technician_name;
    public $estimated_cost = 0;

    protected $rules = [
        'description' => 'required|string|min:10',
        'technician_name' => 'nullable|string|max:255',
        'estimated_cost' => 'nullable|numeric|min:0',
    ];

    public function mount($asset)
    {
        $this->asset = Asset::findOrFail($asset);
    }

    public function render()
    {
        return view('livewire.quick-incident-report')->layout('layouts.app');
    }

    public function submit()
    {
        $this->validate();

        // Create incident maintenance record
        AssetMaintenance::create([
            'asset_id' => $this->asset->id,
            'type' => 'Incident',
            'description' => $this->description,
            'cost' => $this->estimated_cost,
            'technician_name' => $this->technician_name,
            'scheduled_date' => now(),
            'status' => 'Pending',
            'reported_by' => Auth::id(),
        ]);

        // Update asset status to Repairing
        $this->asset->update(['status' => 'Repairing']);

        session()->flash('message', 'Đã ghi nhận sự cố. Cảm ơn bạn đã báo cáo!');
        
        $this->reset(['description', 'technician_name', 'estimated_cost']);
    }
}
