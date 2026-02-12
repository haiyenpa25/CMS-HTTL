<?php

namespace App\Livewire;

use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\MemberVisit;
use App\Modules\Membership\Models\VisitCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QuickVisitModal extends Component
{
    public $showModal = false;
    public $visitType = 'emergency'; // emergency, suggested, location
    
    // Form fields
    public $member_id;
    public $scheduled_date;
    public $category_id;
    public $priority = 'high';
    public $purpose;
    public $notes;

    protected $listeners = ['openQuickVisit' => 'open'];

    public function open($type = 'emergency')
    {
        $this->visitType = $type;
        $this->showModal = true;
        $this->scheduled_date = now()->format('Y-m-d');
        $this->priority = $type === 'emergency' ? 'high' : 'normal';
    }

    public function close()
    {
        $this->showModal = false;
        $this->reset(['member_id', 'scheduled_date', 'category_id', 'purpose', 'notes']);
    }

    public function saveVisit()
    {
        $this->validate([
            'member_id' => 'required|exists:members,id',
            'scheduled_date' => 'required|date',
            'category_id' => 'nullable|exists:visit_categories,id',
            'purpose' => 'required|string',
        ]);

        $member = Member::find($this->member_id);
        $departmentId = $member->departments()->first()->id ?? Auth::user()->assignments()->first()->department_id;

        MemberVisit::create([
            'member_id' => $this->member_id,
            'department_id' => $departmentId,
            'created_by' => Auth::id(),
            'category_id' => $this->category_id,
            'visit_date' => $this->scheduled_date,
            'scheduled_date' => $this->scheduled_date,
            'status' => 'planned',
            'visit_type' => $this->visitType,
            'priority' => $this->priority,
            'purpose' => $this->purpose,
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Đã tạo lịch thăm thành công!');
        
        $this->close();
        $this->dispatch('visit-created');
        
        return redirect()->route('visits.members');
    }

    public function getMembersProperty()
    {
        $departmentId = Auth::user()->assignments()->first()->department_id ?? null;
        
        if (!$departmentId) {
            return collect();
        }

        return Member::whereHas('departments', function($q) use ($departmentId) {
                $q->where('departments.id', $departmentId);
            })
            ->orderBy('full_name')
            ->get();
    }

    public function getCategoriesProperty()
    {
        return VisitCategory::active()->get();
    }

    public function render()
    {
        return view('livewire.quick-visit-modal');
    }
}
