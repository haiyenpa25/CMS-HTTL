<?php

namespace App\Livewire;

use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\MemberVisit;
use App\Modules\Identity\Models\User;
use App\Modules\Membership\Models\VisitCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RecordVisit extends Component
{
    public $memberId;
    public $member;
    
    // Form fields
    public $visit_date;
    public $scheduled_date;
    public $category_id;
    public $visit_type = 'regular';
    public $priority = 'normal';
    public $purpose;
    public $notes;
    public $prayer_requests;
    public $participants = [];
    
    // For completing visit
    public $isCompleting = false;
    public $outcome;
    public $duration_minutes;

    public function mount($memberId)
    {
        $this->memberId = $memberId;
        $this->member = Member::findOrFail($memberId);
        $this->visit_date = now()->format('Y-m-d');
        $this->scheduled_date = now()->format('Y-m-d');
    }

    public function saveVisit()
    {
        $this->validate([
            'visit_date' => 'required|date',
            'category_id' => 'nullable|exists:visit_categories,id',
            'visit_type' => 'required|in:regular,emergency,follow_up',
            'priority' => 'required|in:high,normal,low',
            'purpose' => 'nullable|string',
            'notes' => 'nullable|string',
            'prayer_requests' => 'nullable|string',
            'outcome' => $this->isCompleting ? 'required|string' : 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
        ]);

        $visit = MemberVisit::create([
            'member_id' => $this->memberId,
            'department_id' => Auth::user()->assignments()->first()->department_id ?? 1,
            'created_by' => Auth::id(),
            'category_id' => $this->category_id,
            'visit_date' => $this->visit_date,
            'scheduled_date' => $this->scheduled_date,
            'status' => $this->isCompleting ? 'completed' : 'planned',
            'visit_type' => $this->visit_type,
            'priority' => $this->priority,
            'purpose' => $this->purpose,
            'notes' => $this->notes,
            'prayer_requests' => $this->prayer_requests,
            'outcome' => $this->outcome,
            'duration_minutes' => $this->duration_minutes,
            'completed_at' => $this->isCompleting ? now() : null,
        ]);

        // Attach participants
        if (!empty($this->participants)) {
            foreach ($this->participants as $userId) {
                $visit->participants()->attach($userId, ['role' => 'member']);
            }
        }

        session()->flash('message', 'Đã ghi nhận thăm viếng thành công!');
        
        $this->dispatch('visit-recorded');
        $this->dispatch('close-modal');
        
        return redirect()->route('visits.members');
    }

    public function getCategoriesProperty()
    {
        return VisitCategory::active()->get();
    }

    public function getAvailableParticipantsProperty()
    {
        return User::whereHas('assignments', function($q) {
            $q->whereJsonContains('allowed_features', 'visits');
        })->get();
    }

    public function render()
    {
        return view('livewire.record-visit');
    }
}
