<?php

namespace App\Livewire;

use App\Modules\Attendance\Models\Attendance;
use App\Modules\Organization\Models\Department;
use App\Modules\Membership\Models\Family;
use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\Visit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class VisitManagement extends Component
{
    public $selectedDepartmentId;
    public $selectedCategory = 'all'; // all, sos, suggested, location
    public $selectedLocationGroup;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingVisit;

    // Form fields
    public $family_id;
    public $visit_date;
    public $visit_type = 'regular';
    public $priority = 'normal';
    public $reason;
    public $prayer_needs;

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

    public function updatedSelectedDepartmentId()
    {
        // Just trigger re-render
    }

    public function updatedSelectedCategory()
    {
        // Just trigger re-render, computed properties will handle the rest
    }

    // Computed properties to avoid serialization issues
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

    public function getVisitsProperty()
    {
        if (!$this->selectedDepartmentId) {
            return collect();
        }

        return Visit::with(['family', 'department', 'participants'])
            ->where('department_id', $this->selectedDepartmentId)
            ->get();
    }

    public function getSosVisitsProperty()
    {
        if (!$this->selectedDepartmentId) {
            return collect();
        }

        return Visit::where('department_id', $this->selectedDepartmentId)
            ->sos()
            ->with(['family', 'participants'])
            ->get();
    }

    public function getSuggestedVisitsProperty()
    {
        if (!$this->selectedDepartmentId) {
            return collect();
        }

        // Auto-generate suggestions if needed
        $this->generateSuggestedVisits();

        // Return existing suggested visits
        return Visit::where('department_id', $this->selectedDepartmentId)
            ->suggested()
            ->with(['family', 'participants'])
            ->get();
    }

    public function getLocationGroupsProperty()
    {
        if (!$this->selectedDepartmentId) {
            return collect();
        }

        $visits = Visit::where('department_id', $this->selectedDepartmentId)
            ->where('status', 'planned')
            ->with(['family'])
            ->get();

        return $visits->groupBy(function($visit) {
            $address = $visit->family->address ?? '';
            
            // Extract ward/district from address
            if (preg_match('/Phường\s+([^,]+)/', $address, $matches)) {
                return 'Phường ' . trim($matches[1]);
            }
            if (preg_match('/Xã\s+([^,]+)/', $address, $matches)) {
                return 'Xã ' . trim($matches[1]);
            }
            if (preg_match('/Quận\s+([^,]+)/', $address, $matches)) {
                return 'Quận ' . trim($matches[1]);
            }
            
            return 'Khác';
        });
    }

    private function generateSuggestedVisits()
    {
        // Get members absent 3+ weeks
        $absentMembers = $this->getMembersAbsent3Weeks();
        
        foreach ($absentMembers as $memberData) {
            // Check if suggestion already exists
            $exists = Visit::where('family_id', $memberData['family_id'])
                ->where('department_id', $this->selectedDepartmentId)
                ->where('visit_type', 'suggested')
                ->where('status', 'planned')
                ->exists();

            if (!$exists) {
                Visit::create([
                    'family_id' => $memberData['family_id'],
                    'department_id' => $this->selectedDepartmentId,
                    'created_by' => Auth::id(),
                    'visit_date' => now()->addDays(7),
                    'visit_type' => 'suggested',
                    'priority' => 'normal',
                    'status' => 'planned',
                    'reason' => "Vắng nhóm {$memberData['weeks_absent']} tuần",
                    'weeks_absent' => $memberData['weeks_absent'],
                ]);
            }
        }

        // Get families not visited in 6+ months
        $noVisitFamilies = $this->getFamiliesNoVisit6Months();
        
        foreach ($noVisitFamilies as $familyData) {
            $exists = Visit::where('family_id', $familyData['family_id'])
                ->where('department_id', $this->selectedDepartmentId)
                ->where('visit_type', 'suggested')
                ->where('status', 'planned')
                ->exists();

            if (!$exists) {
                Visit::create([
                    'family_id' => $familyData['family_id'],
                    'department_id' => $this->selectedDepartmentId,
                    'created_by' => Auth::id(),
                    'visit_date' => now()->addDays(7),
                    'visit_type' => 'suggested',
                    'priority' => 'normal',
                    'status' => 'planned',
                    'reason' => "{$familyData['months']} tháng chưa được thăm",
                    'months_since_last_visit' => $familyData['months'],
                ]);
            }
        }

    }

    private function getMembersAbsent3Weeks()
    {
        // Get last 3 attendance sessions for this department
        $recentSessions = DB::table('attendance_sessions')
            ->where('department_id', $this->selectedDepartmentId)
            ->where('type', 'department_meeting')
            ->orderBy('date', 'desc')
            ->limit(3)
            ->pluck('id');

        if ($recentSessions->isEmpty()) {
            return collect();
        }

        // Find members who were absent in all 3 sessions
        $absentMembers = Member::whereHas('departments', function($q) {
                $q->where('departments.id', $this->selectedDepartmentId);
            })
            ->whereHas('family')
            ->get()
            ->filter(function($member) use ($recentSessions) {
                $presentCount = Attendance::where('member_id', $member->id)
                    ->whereIn('attendance_session_id', $recentSessions)
                    ->where('is_present', true)
                    ->count();
                
                return $presentCount == 0; // Absent in all sessions
            })
            ->map(function($member) use ($recentSessions) {
                return [
                    'member_id' => $member->id,
                    'family_id' => $member->family_id,
                    'weeks_absent' => $recentSessions->count(),
                ];
            });

        return $absentMembers;
    }

    private function getFamiliesNoVisit6Months()
    {
        $sixMonthsAgo = now()->subMonths(6);

        $families = Family::whereHas('members.departments', function($q) {
                $q->where('departments.id', $this->selectedDepartmentId);
            })
            ->whereDoesntHave('visits', function($q) use ($sixMonthsAgo) {
                $q->where('visit_date', '>=', $sixMonthsAgo)
                    ->where('status', 'completed');
            })
            ->get()
            ->map(function($family) use ($sixMonthsAgo) {
                $lastVisit = Visit::where('family_id', $family->id)
                    ->where('status', 'completed')
                    ->orderBy('visit_date', 'desc')
                    ->first();

                $months = $lastVisit 
                    ? $lastVisit->visit_date->diffInMonths(now())
                    : 12; // Default to 12 if never visited

                return [
                    'family_id' => $family->id,
                    'months' => $months,
                ];
            });

        return $families;
    }


    public function createVisit()
    {
        $this->validate([
            'family_id' => 'required|exists:families,id',
            'visit_date' => 'required|date',
            'reason' => 'nullable|string',
        ]);

        Visit::create([
            'family_id' => $this->family_id,
            'department_id' => $this->selectedDepartmentId,
            'created_by' => Auth::id(),
            'visit_date' => $this->visit_date,
            'visit_type' => $this->visit_type,
            'priority' => $this->priority,
            'status' => 'planned',
            'reason' => $this->reason,
            'prayer_needs' => $this->prayer_needs,
        ]);

        $this->showCreateModal = false;
        $this->reset(['family_id', 'visit_date', 'reason', 'prayer_needs']);

        session()->flash('message', 'Đã tạo lịch thăm viếng');
    }

    public function editVisit($visitId)
    {
        $this->editingVisit = Visit::find($visitId);
        $this->showEditModal = true;
    }

    public function completeVisit($visitId)
    {
        $visit = Visit::find($visitId);
        $visit->update(['status' => 'completed']);
        
        session()->flash('message', 'Đã hoàn thành thăm viếng');
    }

    public function render()
    {
        return view('livewire.visit-management')->layout('layouts.app');
    }
}
