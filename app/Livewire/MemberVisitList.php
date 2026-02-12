<?php

namespace App\Livewire;

use App\Modules\Organization\Models\Department;
use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\MemberVisit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MemberVisitList extends Component
{
    use WithPagination;

    public $selectedDepartmentId;
    public $search = '';
    public $statusFilter = 'all'; // all, recent, due_soon, overdue, critical, never
    public $sortField = 'full_name';
    public $sortDirection = 'asc';
    public $perPage = 25;

    public $showRecordModal = false;
    public $selectedMemberId;

    protected $queryString = ['search', 'statusFilter', 'sortField', 'sortDirection'];

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

        // Handle URL parameters
        if (request()->has('statusFilter')) {
            $this->statusFilter = request('statusFilter');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

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

    public function getMembersProperty()
    {
        if (!$this->selectedDepartmentId) {
            return collect();
        }

        $query = Member::whereHas('departments', function($q) {
                $q->where('departments.id', $this->selectedDepartmentId);
            })
            ->with(['memberVisits' => function($q) {
                $q->where('status', 'completed')
                    ->orderBy('visit_date', 'desc')
                    ->limit(1);
            }]);

        // Search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('full_name', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $members = $query->paginate($this->perPage);

        // Add visit status to each member
        $members->getCollection()->transform(function($member) {
            $lastVisit = $member->memberVisits->first();
            $daysSinceVisit = $lastVisit ? $lastVisit->visit_date->diffInDays(now()) : null;
            
            $member->last_visit = $lastVisit;
            $member->days_since_visit = $daysSinceVisit;
            $member->visit_status = $this->getVisitStatus($daysSinceVisit);
            $member->status_class = $this->getStatusClass($daysSinceVisit);
            $member->status_label = $this->getStatusLabel($daysSinceVisit);
            
            return $member;
        });

        // Filter by status
        if ($this->statusFilter !== 'all') {
            $members->setCollection(
                $members->getCollection()->filter(function($member) {
                    return $member->visit_status === $this->statusFilter;
                })
            );
        }

        return $members;
    }

    private function getVisitStatus($days)
    {
        if (is_null($days)) return 'never';
        if ($days < 30) return 'recent';
        if ($days < 90) return 'due_soon';
        if ($days < 180) return 'overdue';
        return 'critical';
    }

    private function getStatusClass($days)
    {
        if (is_null($days)) return 'bg-red-100 text-red-800 border-red-200';
        if ($days < 30) return 'bg-green-100 text-green-800 border-green-200';
        if ($days < 90) return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        if ($days < 180) return 'bg-orange-100 text-orange-800 border-orange-200';
        return 'bg-red-100 text-red-800 border-red-200';
    }

    private function getStatusLabel($days)
    {
        if (is_null($days)) return '⚠️ Chưa thăm';
        if ($days < 30) return '✅ Gần đây';
        if ($days < 90) return '⏰ Sắp đến hạn';
        if ($days < 180) return '⚠️ Quá hạn';
        return '🔴 Rất cần thăm';
    }

    public function openRecordModal($memberId)
    {
        $this->selectedMemberId = $memberId;
        $this->showRecordModal = true;
        $this->dispatch('open-record-visit-modal', memberId: $memberId);
    }

    public function render()
    {
        return view('livewire.member-visit-list')->layout('layouts.app');
    }
}
