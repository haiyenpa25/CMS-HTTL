<?php


namespace App\Livewire\Report;

use App\Models\Report;
use App\Modules\Organization\Models\Department;
use App\Services\ReportStatsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateReportForm extends Component
{
    public $showModal = false;
    public $activeTab = 'stats'; // stats, visits, outcome

    // Form Data
    public $department_id;
    public $reporting_date;
    public $type = 'ChuaNhat';
    public $attendance_count = 0;
    
    // Content JSON fields
    public $topic = '';
    public $speaker = '';
    public $strengths = '';
    public $weaknesses = '';
    public $recommendations = '';
    public $prayer_requests = '';

    // Fetched Data
    public $completedVisits = [];
    public $availableDepartments = [];

    protected $rules = [
        'department_id' => 'required|exists:departments,id',
        'reporting_date' => 'required|date',
        'type' => 'required',
        'attendance_count' => 'required|integer|min:0',
    ];

    protected $listeners = ['open-create-report' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
        $this->reset(['topic', 'speaker', 'strengths', 'weaknesses', 'recommendations', 'prayer_requests']);
        $this->activeTab = 'stats';
    }

    public function mount()
    {
        $this->availableDepartments = Auth::user()->departments;
        $this->department_id = $this->availableDepartments->first()?->id;
        $this->reporting_date = now()->format('Y-m-d');
        $this->loadStats();
    }

    public function getListeners()
    {
        return ['open-create-report' => 'open'];
    }

    public function open()
    {
        $this->showModal = true;
        $this->loadStats();
    }

    public function updatedDepartmentId()
    {
        $this->loadStats();
    }

    public function updatedReportingDate()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        if (!$this->department_id || !$this->reporting_date) return;

        $service = app(ReportStatsService::class);
        
        // Assume reporting for the week ending on reporting_date
        $endDate = $this->reporting_date;
        $startDate = \Carbon\Carbon::parse($endDate)->subDays(6)->format('Y-m-d');

        // Auto-fill attendance
        $this->attendance_count = $service->getAttendanceData($this->department_id, $startDate, $endDate);

        // Fetch visits
        $this->completedVisits = $service->getCompletedVisits($this->department_id, $startDate, $endDate);
    }

    public function save()
    {
        $this->validate();

        Report::create([
            'department_id' => $this->department_id,
            'user_id' => Auth::id(),
            'type' => $this->type,
            'reporting_date' => $this->reporting_date,
            'attendance_count' => $this->attendance_count,
            'status' => 'published',
            'content' => [
                'topic' => $this->topic,
                'speaker' => $this->speaker,
                'strengths' => $this->strengths,
                'weaknesses' => $this->weaknesses,
                'recommendations' => $this->recommendations,
                'prayer_requests' => $this->prayer_requests,
            ],
        ]);

        $this->showModal = false;
        $this->dispatch('reportCreated');
        $this->dispatch('refreshDashboard');
        
        // Reset form
        $this->reset(['topic', 'speaker', 'strengths', 'weaknesses', 'recommendations', 'prayer_requests']);
    }

    public function render()
    {
        return view('livewire.report.create-report-form');
    }
}
