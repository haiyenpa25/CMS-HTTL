<?php

namespace App\Modules\Assets\Livewire;

use Livewire\Component;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetCategory;

class AssetDashboard extends Component
{
    public $totalAssets;
    public $activeAssets;
    public $repairingAssets;
    public $disposedAssets;
    public $recentAssets;
    public $statusDistribution;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->totalAssets = Asset::count();
        $this->activeAssets = Asset::where('status', 'Active')->count();
        $this->repairingAssets = Asset::where('status', 'Repairing')->count();
        $this->disposedAssets = Asset::where('status', 'Disposed')->count();
        
        $this->recentAssets = Asset::with(['category', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // For pie chart
        $this->statusDistribution = [
            'Active' => $this->activeAssets,
            'Repairing' => $this->repairingAssets,
            'Broken' => Asset::where('status', 'Broken')->count(),
            'Lost' => Asset::where('status', 'Lost')->count(),
            'Disposed' => $this->disposedAssets,
        ];
    }

    public function render()
    {
        return view('livewire.asset-dashboard', [
            'categories' => AssetCategory::withCount('assets')->get(),
        ])->layout('layouts.app');
    }
}
