<?php

namespace App\Modules\Assets\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Modules\Assets\Services\MaintenanceScheduler;
use Illuminate\Support\Facades\Log;

class CheckMaintenanceDue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(MaintenanceScheduler $scheduler): void
    {
        $count = $scheduler->autoScheduleAll();
        
        Log::info("Asset Maintenance Check: Created {$count} routine maintenance records");
    }
}
