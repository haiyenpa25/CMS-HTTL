<?php

namespace App\Modules\Reports;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ReportsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register Livewire components
        Livewire::component('reports.department-report-dashboard', \App\Modules\Reports\Livewire\DepartmentReportDashboard::class);
        
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'reports');
    }
}
