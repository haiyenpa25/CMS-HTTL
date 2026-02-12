<?php

namespace App\Modules\Membership\Livewire;

use App\Modules\Membership\Models\Family;
use App\Modules\Organization\Models\Group;
use App\Modules\Membership\Models\Member;
use App\Modules\Membership\Models\Title;
use App\Modules\Membership\Models\SpiritualGrowth;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MemberManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public $search = '';
    public $statusFilter = '';
    public $groupFilter = '';
    public $typeFilter = ''; // 'member' or 'guest'

    // Modal State
    public $isModalOpen = false;
    public $isEditing = false;
    public $memberId = null;

    // Form Fields
    // Personal
    // Personal
    public $full_name;
    public $avatar; // Store object for upload
    public $avatar_url; // Store url for preview/edit
    public $phone;
    public $gender = 'Nam';
    public $birthday;
    public $job = []; // Array for tags
    public $is_married = false;
    public $identity_card;
    public $email;
    public $note;

    // Family
    public $family_id;
    public $family_role;
    public $familySearch = ''; // for searchable select
    // Inline Family Creation
    public $isCreatingFamily = false;
    public $new_family_name;
    public $new_family_address;
    public $new_family_ward;

    // Spiritual
    public $title_id;
    public $status = 'active';
    public $date_faith;
    public $date_baptism;
    public $is_baptized = false; // Toggle UI
    public $referred_by;
    public $spiritual_gifts = []; // Array for tags

    // Groups
    public $selectedGroups = [];
    public $roles = [];
    public $sub_groups = [];

    // Baptism Confirmation
    public $isConfirmBaptismModalOpen = false;
    public $confirmingMemberId;
    public $confirmBaptismDate;
    public $confirmBaptismBy;
    public $confirmBaptismPlace;
    public $confirmBaptismNote;
    
    // ...

    public function openBaptismConfirmation($memberId)
    {
        $this->confirmingMemberId = $memberId;
        $this->confirmBaptismDate = Carbon::now()->format('Y-m-d');
        $this->confirmBaptismBy = 'Mục sư Quản nhiệm'; // Default
        $this->confirmBaptismPlace = 'Tại Hội Thánh'; // Default
        $this->confirmBaptismNote = '';
        $this->isConfirmBaptismModalOpen = true;
    }

    public function confirmBaptism()
    {
        $this->validate([
            'confirmBaptismDate' => 'required|date',
            'confirmBaptismBy' => 'required|string|max:255',
            'confirmBaptismPlace' => 'required|string|max:255',
            'confirmBaptismNote' => 'nullable|string|max:500',
        ]);

        $member = Member::findOrFail($this->confirmingMemberId);
        
        // 1. Transaction to update member and log
        DB::transaction(function () use ($member) {
            
            // Find 'Tín hữu' title id.
            $memberTitleId = Title::where('name', 'like', '%Tín hữu%')->where('name', 'not like', '%Tín hữu mới%')->value('id');
            // If strictly 'Tín hữu chính thức' is needed, adjust query. Assuming 'Tín hữu' is the target.
            
            $updateData = [
                'date_baptism' => $this->confirmBaptismDate,
                'baptized_by' => $this->confirmBaptismBy,
                'baptism_place' => $this->confirmBaptismPlace,
                'status' => 'active', 
            ];
    
            if ($memberTitleId) {
                $updateData['title_id'] = $memberTitleId;
            }
    
            $member->update($updateData);
    
            // 2. Record Spiritual Growth Event
            SpiritualGrowth::create([
                'member_id' => $member->id,
                'type' => 'baptism',
                'event_date' => $this->confirmBaptismDate,
                'details' => "Bởi: {$this->confirmBaptismBy} tại {$this->confirmBaptismPlace}. " . $this->confirmBaptismNote,
            ]);

            // 3. Create Dashboard Notification Log
            \App\Models\ActivityLog::create([
                'type' => 'success',
                'message' => 'Hội thánh có thêm 1 chi thể mới vừa báp-tem!',
                'payload' => ['member_id' => $member->id, 'name' => $member->full_name],
            ]);
        });

        $this->isConfirmBaptismModalOpen = false;
        $this->reset(['confirmingMemberId', 'confirmBaptismDate', 'confirmBaptismBy', 'confirmBaptismPlace', 'confirmBaptismNote']);
        
        session()->flash('message', 'Đã xác nhận Báp-tem và chuyển thành Tín hữu thành công!');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->full_name = '';
        $this->avatar = null;
        $this->avatar_url = null;
        $this->phone = '';
        $this->gender = 'Nam';
        $this->birthday = null;
        $this->job = [];
        $this->is_married = false;
        $this->identity_card = '';
        $this->email = '';
        $this->note = '';
        $this->family_id = null;
        $this->family_role = '';
        $this->familySearch = '';
        $this->isCreatingFamily = false;
        $this->new_family_name = '';
        $this->new_family_address = '';
        $this->new_family_ward = '';
        $this->title_id = '';
        $this->status = 'active';
        $this->date_faith = null;
        $this->date_baptism = null;
        $this->is_baptized = false;
        $this->referred_by = '';
        $this->spiritual_gifts = [];
        $this->selectedGroups = [];
        $this->roles = [];
        $this->sub_groups = [];
        $this->memberId = null;
        $this->isEditing = false;
    }

    public function store()
    {
        $this->validate([
            'full_name' => 'required',
            'family_id' => 'required_without:new_family_name',
            'new_family_name' => 'required_without:family_id',
            'title_id' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable|numeric',
        ]);

        DB::transaction(function () {
             // Handle Family
            if ($this->isCreatingFamily && $this->new_family_name) {
                $family = Family::create([
                    'name' => $this->new_family_name,
                    'address' => $this->new_family_address,
                    // 'ward' => $this->new_family_ward, 
                ]);
                $this->family_id = $family->id;
            }

            // Handle File Upload
            if ($this->avatar) {
                $avatarPath = $this->avatar->store('avatars', 'public');
            }

            $member = Member::create([
                'full_name' => $this->full_name,
                'family_id' => $this->family_id,
                'family_role' => $this->family_role,
                'title_id' => $this->title_id,
                'avatar' => $avatarPath ?? null,
                'identity_card' => $this->identity_card,
                'email' => $this->email,
                'phone' => $this->phone,
                'gender' => $this->gender,
                'birthday' => $this->birthday,
                'job' => $this->job,
                'is_married' => $this->is_married,
                'note' => $this->note,
                'status' => $this->status,
                'date_faith' => $this->date_faith,
                'date_baptism' => $this->date_baptism,
                'referred_by' => $this->referred_by,
                'spiritual_gifts' => $this->spiritual_gifts,
            ]);

            $this->syncGroups($member);
        });

        session()->flash('message', 'Thêm mới tín hữu thành công.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $member = Member::findOrFail($id);
        $this->memberId = $id;
        $this->full_name = $member->full_name;
        $this->family_id = $member->family_id;
        $this->family_role = $member->family_role;
        $this->title_id = $member->title_id;
        $this->avatar_url = $member->avatar ? Storage::url($member->avatar) : null;
        $this->identity_card = $member->identity_card;
        $this->email = $member->email;
        $this->phone = $member->phone;
        $this->gender = $member->gender;
        $this->birthday = $member->birthday ? $member->birthday->format('Y-m-d') : null;
        $this->job = $member->job ?? [];
        $this->is_married = $member->is_married;
        $this->note = $member->note;
        $this->status = $member->status;
        $this->date_faith = $member->date_faith ? $member->date_faith->format('Y-m-d') : null;
        $this->date_baptism = $member->date_baptism ? $member->date_baptism->format('Y-m-d') : null;
        $this->is_baptized = !empty($member->date_baptism);
        $this->referred_by = $member->referred_by;
        $this->spiritual_gifts = $member->spiritual_gifts ?? [];
        
        // Load Groups
        $this->selectedGroups = $member->groups->pluck('id')->toArray();
        foreach ($member->groups as $group) {
            $this->roles[$group->id] = $group->pivot->role;
            $this->sub_groups[$group->id] = $group->pivot->sub_group;
        }

        $this->isEditing = true;
        $this->isModalOpen = true;
    }

    public function update()
    {
        $this->validate([
            'full_name' => 'required',
            'family_id' => 'required',
            'title_id' => 'required',
            'email' => 'nullable|email',
        ]);

        if ($this->memberId) {
            $member = Member::find($this->memberId);
            
            // Handle File Upload
            $avatarPath = $member->avatar;
            if ($this->avatar) {
                $avatarPath = $this->avatar->store('avatars', 'public');
            }

            $member->update([
                'full_name' => $this->full_name,
                'family_id' => $this->family_id,
                'family_role' => $this->family_role,
                'title_id' => $this->title_id,
                'avatar' => $avatarPath,
                'identity_card' => $this->identity_card,
                'email' => $this->email,
                'phone' => $this->phone,
                'gender' => $this->gender,
                'birthday' => $this->birthday,
                'job' => $this->job,
                'is_married' => $this->is_married,
                'note' => $this->note,
                'status' => $this->status,
                'date_faith' => $this->date_faith,
                'date_baptism' => $this->date_baptism,
                'referred_by' => $this->referred_by,
                'spiritual_gifts' => $this->spiritual_gifts,
            ]);

            $this->syncGroups($member);

            session()->flash('message', 'Cập nhật thông tin thành công.');
            $this->closeModal();
        }
    }

    private function syncGroups(Member $member)
    {
        $syncData = [];
        foreach ($this->selectedGroups as $groupId) {
            $syncData[$groupId] = [
                'role' => $this->roles[$groupId] ?? 'thành viên',
                'sub_group' => $this->sub_groups[$groupId] ?? null,
            ];
        }
        $member->groups()->sync($syncData);
    }
    public function render()
    {
        $query = Member::with(['family', 'title', 'groups'])
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->groupFilter, function($q) {
                $q->whereHas('groups', fn($g) => $g->where('groups.id', $this->groupFilter));
            });

        // SCOPED ACCESS LOGIC
        if (auth()->check() && !auth()->user()->hasRole('super-admin') && !auth()->user()->hasRole('admin') && !auth()->user()->hasRole('secretary')) {
            $myDeptIds = auth()->user()->getManageableDepartmentIds();
            // Filter members who belong to these departments
            $query->whereHas('groups', function($q) use ($myDeptIds) {
                $q->whereIn('groups.id', $myDeptIds);
            });
        }

        return view('livewire.member-management', [
            'members' => $query->latest()->paginate(10),
            'families' => Family::orderBy('name')->get(),
            'titles' => Title::all(),
            'groups' => Group::all(),
        ])->layout('layouts.app');
    }
}
