<?php

namespace App\Livewire\Report;


use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportDetail extends Component
{
    public Report $report;

    public function mount(Report $report)
    {
        $this->report = $report;
        $this->authorize('view', $report);
    }

    public function render()
    {
        return view('livewire.report.report-detail')->layout('layouts.app'); 
        // Or a specific print layout if needed, but app layout is fine if we use print media queries
    }
}
