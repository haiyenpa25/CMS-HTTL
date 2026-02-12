<?php

namespace App\Livewire;

use App\Modules\Membership\Models\Family;
use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\Title;
use App\Modules\Membership\Models\Visit;
use Livewire\Component;

class FamilyDetail extends Component
{
    public $family;
    public $familyId;

    // Visit Form
    public $visit_date;
    public $visitors; // String for simplicity in this version
    public $visit_notes;

    protected $rules = [
        'visit_date' => 'required|date',
        'visitors' => 'required|string',
        'visit_notes' => 'nullable|string',
    ];

    public function mount($familyId)
    {
        $this->familyId = $familyId;
        $this->loadFamily();
        $this->visit_date = now()->format('Y-m-d\TH:i');
    }

    // Add Member Logic
    public $searchMemberQuery = '';
    public $searchResults = [];
    public $isAddMemberModalOpen = false;

    public function updatedSearchMemberQuery()
    {
        if (strlen($this->searchMemberQuery) >= 2) {
            $this->searchResults = Member::where('full_name', 'like', '%' . $this->searchMemberQuery . '%')
                ->whereNull('family_id') // Only finding members without family
                ->take(5)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function addMemberToFamily($memberId)
    {
        $member = Member::find($memberId);
        if ($member) {
            $member->update([
                'family_id' => $this->familyId,
                'address' => $this->family->address, // Auto sync address on add? Maybe optional, but good default
                'ward' => $this->family->ward,
                // 'district' => $this->family->district, // If district exists in member
                // 'province' => $this->family->province, // If province exists in member
            ]);
            
            // Log if needed
            session()->flash('message', 'Đã thêm thành viên vào hộ gia đình!');
        }
        
        $this->reset(['searchMemberQuery', 'searchResults', 'isAddMemberModalOpen']);
        $this->loadFamily();
    }

    public function removeMember($memberId)
    {
        $member = Member::find($memberId);
        if ($member && $member->family_id == $this->familyId) {
             // Do not allow removing Head? Logic to convert head to member before removing?
             // For now simple removal
             $member->update(['family_id' => null]);
             session()->flash('message', 'Đã xóa thành viên khỏi hộ gia đình.');
             $this->loadFamily();
        }
    }

    // Sync Address Logic
    public function syncAddressToMembers()
    {
        // Require family address to be set
        if (!$this->family->address) {
            session()->flash('error', 'Hộ gia đình chưa có địa chỉ để đồng bộ.');
            return;
        }

        Member::where('family_id', $this->familyId)->update([
            'address' => $this->family->address,
            'ward' => $this->family->ward,
            // Add other fields if Member model has them
        ]);

        session()->flash('message', 'Đã cập nhật địa chỉ cho tất cả thành viên trong hộ!');
    }

    public function loadFamily()
    {
        $this->family = Family::with(['members.title', 'members.groups', 'visits'])->findOrFail($this->familyId);
    }

    public function saveVisit()
    {
        $this->validate();

        Visit::create([
            'family_id' => $this->familyId,
            'visit_date' => $this->visit_date,
            'visitors' => $this->visitors,
            'notes' => $this->visit_notes,
        ]);

        $this->loadFamily(); // Refresh data
        $this->reset(['visitors', 'visit_notes']);
        
        // Update visit_date to now or keep? Let's reset to now
        $this->visit_date = now()->format('Y-m-d\TH:i');

        session()->flash('message', 'Đã lưu lại lịch trình thăm viếng!');
    }

    public function render()
    {
        return view('livewire.family-detail')->layout('layouts.app');
    }
}
