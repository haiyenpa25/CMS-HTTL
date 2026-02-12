<?php

namespace App\Livewire;

use App\Modules\Organization\Models\Department;
use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\MemberVisit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class VisitDashboard extends Component
{
    public $selectedDepartmentId;

    public function mount()
    {
        // Check permission
        $hasPermission = Auth::user()->assignments()
            ->whereJsonContains('allowed_features', 'visits')
            ->exists();
        
        if (!$hasPermission && !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super-admin')) {
            abort(403, 'Bạn không có quyền truy cập tính năng này');
        }
        
        if ($this->departments->isNotEmpty()) {
            $this->selectedDepartmentId = $this->departments->first()->id;
        }
    }

    // Computed properties
    public function getDepartmentsProperty()
    {
        if (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('admin')) {
            return Department::where('status', 'active')->get();
        }
        
        $assignmentIds = Auth::user()->assignments()
            ->whereJsonContains('allowed_features', 'visits')
            ->pluck('department_id')
            ->filter();
        
        return Department::whereIn('id', $assignmentIds)
            ->where('status', 'active')
            ->get();
    }

    public function getStatsProperty()
    {
        if (!$this->selectedDepartmentId) {
            return [
                'total' => 0,
                'completed' => 0,
                'pending' => 0,
                'overdue' => 0,
            ];
        }

        return [
            'total' => MemberVisit::forDepartment($this->selectedDepartmentId)->count(),
            'completed' => MemberVisit::forDepartment($this->selectedDepartmentId)->completed()->count(),
            'pending' => MemberVisit::forDepartment($this->selectedDepartmentId)->planned()->count(),
            'overdue' => $this->getOverdueCount(),
        ];
    }

    public function getUpcomingVisitsProperty()
    {
        if (!$this->selectedDepartmentId) {
            return collect();
        }

        return MemberVisit::forDepartment($this->selectedDepartmentId)
            ->planned()
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(7))
            ->with(['member', 'category'])
            ->orderBy('scheduled_date')
            ->limit(10)
            ->get();
    }

    public function getOverdueMembersProperty()
    {
        if (!$this->selectedDepartmentId) {
            return collect();
        }

        $sixMonthsAgo = now()->subMonths(6);

        return Member::whereHas('departments', function($q) {
                $q->where('departments.id', $this->selectedDepartmentId);
            })
            ->whereDoesntHave('memberVisits', function($q) use ($sixMonthsAgo) {
                $q->where('status', 'completed')
                    ->where('visit_date', '>=', $sixMonthsAgo);
            })
            ->with(['memberVisits' => function($q) {
                $q->where('status', 'completed')
                    ->orderBy('visit_date', 'desc')
                    ->limit(1);
            }])
            ->limit(20)
            ->get()
            ->map(function($member) {
                $lastVisit = $member->memberVisits->first();
                return [
                    'member' => $member,
                    'last_visit' => $lastVisit,
                    'days_since' => $lastVisit ? $lastVisit->visit_date->diffInDays(now()) : null,
                ];
            });
    }

    public function getVisitTrendsProperty()
    {
        if (!$this->selectedDepartmentId) {
            return [
                'labels' => [],
                'data' => [],
            ];
        }

        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i));
        }

        $data = $months->map(function($month) {
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            return MemberVisit::forDepartment($this->selectedDepartmentId)
                ->completed()
                ->whereBetween('visit_date', [$startOfMonth, $endOfMonth])
                ->count();
        });

        return [
            'labels' => $months->map(fn($m) => $m->format('M Y'))->toArray(),
            'data' => $data->toArray(),
        ];
    }

    public function getCompletionRateProperty()
    {
        if (!$this->selectedDepartmentId) {
            return [
                'completed' => 0,
                'pending' => 0,
                'cancelled' => 0,
            ];
        }

        return [
            'completed' => MemberVisit::forDepartment($this->selectedDepartmentId)->where('status', 'completed')->count(),
            'pending' => MemberVisit::forDepartment($this->selectedDepartmentId)->where('status', 'planned')->count(),
            'cancelled' => MemberVisit::forDepartment($this->selectedDepartmentId)->where('status', 'cancelled')->count(),
        ];
    }

    private function getOverdueCount()
    {
        $sixMonthsAgo = now()->subMonths(6);

        return Member::whereHas('departments', function($q) {
                $q->where('departments.id', $this->selectedDepartmentId);
            })
            ->whereDoesntHave('memberVisits', function($q) use ($sixMonthsAgo) {
                $q->where('status', 'completed')
                    ->where('visit_date', '>=', $sixMonthsAgo);
            })
            ->count();
    }

    // Quick Actions
    public function createEmergencyVisit()
    {
        $this->dispatch('openQuickVisit', type: 'emergency');
    }

    public function viewSuggestedVisits()
    {
        return redirect()->route('visits.members', ['statusFilter' => 'critical']);
    }

    public function viewLocationPlanning()
    {
        return redirect()->route('visits.members', ['view' => 'location']);
    }

    public function render()
    {
        return view('livewire.visit-dashboard')->layout('layouts.app');
    }
}
