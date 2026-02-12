<?php

namespace App\Modules\Assets\Services;

use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetMaintenance;
use Carbon\Carbon;

class MaintenanceScheduler
{
    /**
     * Calculate next maintenance date for an asset
     */
    public function calculateNextMaintenanceDate(Asset $asset): ?Carbon
    {
        if (!$asset->category || !$asset->purchase_date) {
            return null;
        }

        $intervalDays = $asset->category->maintenance_interval_days;
        
        // Get last completed maintenance
        $lastMaintenance = $asset->maintenances()
            ->where('type', 'Routine')
            ->where('status', 'Completed')
            ->orderBy('completion_date', 'desc')
            ->first();

        if ($lastMaintenance && $lastMaintenance->completion_date) {
            return $lastMaintenance->completion_date->addDays($intervalDays);
        }

        // If no maintenance history, calculate from purchase date
        return $asset->purchase_date->addDays($intervalDays);
    }

    /**
     * Create routine maintenance record for an asset
     */
    public function createRoutineMaintenance(Asset $asset): AssetMaintenance
    {
        $scheduledDate = $this->calculateNextMaintenanceDate($asset);

        $maintenance = AssetMaintenance::create([
            'asset_id' => $asset->id,
            'type' => 'Routine',
            'description' => 'Bảo trì định kỳ theo lịch',
            'scheduled_date' => $scheduledDate,
            'status' => 'Pending',
        ]);

        // Update asset's next maintenance date
        $asset->update(['next_maintenance_date' => $scheduledDate]);

        return $maintenance;
    }

    /**
     * Get assets that need maintenance
     */
    public function getAssetsDueForMaintenance(): \Illuminate\Database\Eloquent\Collection
    {
        return Asset::where('status', 'Active')
            ->whereNotNull('next_maintenance_date')
            ->where('next_maintenance_date', '<=', now())
            ->with('category')
            ->get();
    }

    /**
     * Get upcoming maintenance (within next 7 days)
     */
    public function getUpcomingMaintenance(int $days = 7): \Illuminate\Database\Eloquent\Collection
    {
        return Asset::where('status', 'Active')
            ->whereNotNull('next_maintenance_date')
            ->whereBetween('next_maintenance_date', [now(), now()->addDays($days)])
            ->with('category')
            ->get();
    }

    /**
     * Auto-schedule maintenance for all eligible assets
     */
    public function autoScheduleAll(): int
    {
        $assets = $this->getAssetsDueForMaintenance();
        $count = 0;

        foreach ($assets as $asset) {
            // Check if there's already a pending maintenance
            $hasPending = $asset->maintenances()
                ->where('type', 'Routine')
                ->where('status', 'Pending')
                ->exists();

            if (!$hasPending) {
                $this->createRoutineMaintenance($asset);
                $count++;
            }
        }

        return $count;
    }
}
