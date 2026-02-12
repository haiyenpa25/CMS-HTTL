<?php

namespace App\Livewire;

use App\Modules\Membership\Models\Family;
use App\Modules\Organization\Models\Group;
use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\Visit;
use Carbon\Carbon;
use Livewire\Component;

use App\Services\VisitationService;

class DashboardOverview extends Component
{
    public $totalMembers;
    public $newMembersCount;
    public $pendingVisitsCount;
    public $priorityVisitations; // New property
    
    public $totalFamilies;
    public $visitsThisMonth;
    
    public $groupLabels = [];
    public $groupData = [];
    public $genderData = [];
    public $ageData = [];
    
    public $recentActivities;

    public function mount(VisitationService $visitationService)
    {
        // 0. Recent Activities
        $this->recentActivities = \App\Models\ActivityLog::latest()->take(5)->get();

        // 0. Priority Visitations
        $this->priorityVisitations = $visitationService->getPriorityVisitations(5);

        // 1. Total Active Members
        $this->totalMembers = Member::where('status', 'active')->count();

        // 2. New Members this Month
        $this->newMembersCount = Member::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 3. Pending Visits (Logic: Active members not visited in last 3 months)
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        $this->pendingVisitsCount = Member::where('status', 'active')
            ->where(function ($query) use ($threeMonthsAgo) {
                $query->whereNull('last_visited_at')
                      ->orWhere('last_visited_at', '<', $threeMonthsAgo);
            })
            ->count();

        // 4. Group Distribution for Chart
        $groups = Group::withCount('members')->get();
        $this->groupLabels = $groups->pluck('name')->toArray();
        // 4. Group Distribution for Chart
        $groups = Group::withCount('members')->get();
        $this->groupLabels = $groups->pluck('name')->toArray();
        $this->groupData = $groups->pluck('members_count')->toArray();

        // 5. Total Families
        $this->totalFamilies = Family::count();

        // 6. Visits This Month
        $this->visitsThisMonth = Visit::whereMonth('visit_date', Carbon::now()->month)
            ->whereYear('visit_date', Carbon::now()->year)
            ->count();

        // 7. Gender Distribution
        $maleCount = Member::where('gender', 'Nam')->count();
        $femaleCount = Member::where('gender', 'Nữ')->count();
        $this->genderData = [$maleCount, $femaleCount];

        // 8. Age Demographics
        // < 12: Children, 12-18: Teen, 19-40: Youth/Adult, 40-60: Middle Age, > 60: Elder
        $now = Carbon::now();
        $this->ageData = [
            Member::whereDate('birthday', '>', $now->copy()->subYears(12))->count(), // Children
            Member::whereDate('birthday', '<=', $now->copy()->subYears(12))->whereDate('birthday', '>', $now->copy()->subYears(18))->count(), // Teens
            Member::whereDate('birthday', '<=', $now->copy()->subYears(18))->whereDate('birthday', '>', $now->copy()->subYears(40))->count(), // Youth
            Member::whereDate('birthday', '<=', $now->copy()->subYears(40))->whereDate('birthday', '>', $now->copy()->subYears(60))->count(), // Middle
            Member::whereDate('birthday', '<=', $now->copy()->subYears(60))->count(), // Elders
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-overview')->layout('layouts.app');
    }
}
