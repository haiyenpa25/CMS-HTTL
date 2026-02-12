<?php


namespace App\Livewire\Report;

use App\Models\Report;
use App\Modules\Organization\Models\Department;
use App\Services\ReportStatsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReportDashboard extends Component
{
    use WithPagination;

    public $departmentId;
    public $dateRange = 'month'; // 'month', 'quarter', 'year'
    public $showCreateModal = false;

    public function mount()
    {
        // Default to first department if user has one
        $this->departmentId = Auth::user()->departments->first()?->id;
    }

    public function getListeners()
    {
        return [
            'reportCreated' => '$refresh',
            'refreshDashboard' => '$refresh',
        ];
    }

    public function render(ReportStatsService $statsService)
    {
        $user = Auth::user();
        
        $query = Report::query()
            ->with(['department', 'user'])
            ->latest('reporting_date');

        // Filter by Department
        if ($user->hasRole('admin')) {
            if ($this->departmentId) {
                $query->where('department_id', $this->departmentId);
            }
        } else {
            // User can only see reports from their departments
            $departmentIds = $user->departments->pluck('id')->toArray();
            $query->whereIn('department_id', $departmentIds);
            
            // If selected department is not in their list, reset (security)
            if ($this->departmentId && !in_array($this->departmentId, $departmentIds)) {
                $this->departmentId = reset($departmentIds);
            }
            
            if ($this->departmentId) {
                $query->where('department_id', $this->departmentId);
            }
        }

        $reports = $query->paginate(10);

        // Chart Data
        $chartData = null;
        if ($this->departmentId) {
            $chartData = $statsService->getGrowthChartData($this->departmentId);
        }

        return view('livewire.report.report-dashboard', [
            'reports' => $reports,
            'departments' => $user->hasRole('admin') ? Department::all() : $user->departments,
            'chartData' => $chartData,
        ])->layout('layouts.app');
    }
}
