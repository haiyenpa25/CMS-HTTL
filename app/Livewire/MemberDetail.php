<?php

namespace App\Livewire;

use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\Visit;
use Livewire\Component;

class MemberDetail extends Component
{
    public $member;
    public $family;
    public $visitations;
    public $activeTab = 'general'; // general, spiritual, care

    public function mount($memberId)
    {
        $this->member = Member::with(['family', 'title', 'groups'])->findOrFail($memberId);
        $this->family = $this->member->family;
        
        // Fetch visitations for the family, ordered by date descending
        if ($this->family) {
            $this->visitations = Visit::where('family_id', $this->family->id)
                ->orderBy('visit_date', 'desc')
                ->get();
        } else {
            $this->visitations = collect([]);
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.member-detail')->layout('layouts.app');
    }
}
