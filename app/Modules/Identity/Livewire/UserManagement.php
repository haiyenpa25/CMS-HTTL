<?php

namespace App\Modules\Identity\Livewire;

use Livewire\Component;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Models\Role;
use App\Modules\Identity\Models\Permission;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $selectedRole = [];

    // Assignment Props
    public $showAssignmentModal = false;
    public $managingUser = null;
    public $departments = [];
    public $subGroups = [];
    public $selectedAssignmentType = 'department'; // department | subgroup
    public $startAssignId;
    public $selectedPermissions = []; // New: checkbox state
    public $currentAssignments = [];

    // Create User Props
    public $showCreateModal = false;
    public $name, $email, $password, $selectedMemberId;
    public $unlinkedMembers = [];
    
    public function mount()
    {
        // Initial load not strictly needed if we load on modal open, 
        // but helps if we want to show count etc.
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'email', 'password', 'selectedMemberId']);
        $this->unlinkedMembers = \App\Models\Member::doesntHave('user')->orderBy('full_name')->get();
        $this->showCreateModal = true;
    }

    public function createUser()
    {
        // Only Admin
        if (!auth()->user()->isSecretary()) {
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'selectedMemberId' => 'required|exists:members,id|unique:users,member_id',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'member_id' => $this->selectedMemberId,
        ]);

        // Default Role: Staff or just empty?
        // Let's assign 'staff' by default so they have some base identify
        $staffRole = Role::where('slug', 'staff')->first();
        if ($staffRole) {
            $user->roles()->attach($staffRole->id);
        }

        $this->showCreateModal = false;
        session()->flash('success', 'Đã tạo tài khoản thành công.');
    }

    public function openAssignmentModal($userId)
    {
        $this->managingUser = User::find($userId);
        if (!$this->managingUser) return;
        
        $this->departments = \App\Modules\Organization\Models\Department::orderBy('name')->get();
        $this->subGroups = \App\Modules\Organization\Models\SubGroup::with('department')->orderBy('name')->get();
        $this->selectedPermissions = []; // Reset
        
        $this->loadAssignments();
        $this->showAssignmentModal = true;
    }

    public function loadAssignments()
    {
        $this->currentAssignments = $this->managingUser->assignments()
            ->with(['department', 'subGroup'])
            ->get();
    }

    public function updatedStartAssignId()
    {
        // When department is selected, we could potentially load default permissions
        // But for now, we leave checkboxes empty or checked by default? 
        // Let's default to check 'attendance' if available.
        $this->selectedPermissions = ['attendance' => true]; 
    }

    public function addAssignment()
    {
        if (!auth()->user()->isSecretary()) return;

        $this->validate([
            'startAssignId' => 'required',
            'selectedAssignmentType' => 'required|in:department,subgroup'
        ]);

        // Check if exists
        $exists = \App\Modules\Identity\Models\UserAssignment::where('user_id', $this->managingUser->id)
            ->where(function($q) {
                if ($this->selectedAssignmentType == 'department') {
                    $q->where('department_id', $this->startAssignId);
                } else {
                    $q->where('sub_group_id', $this->startAssignId);
                }
            })->exists();

        if (!$exists) {
            \App\Modules\Identity\Models\UserAssignment::create([
                'user_id' => $this->managingUser->id,
                'department_id' => $this->selectedAssignmentType == 'department' ? $this->startAssignId : null,
                'sub_group_id' => $this->selectedAssignmentType == 'subgroup' ? $this->startAssignId : null,
                'permissions' => $this->selectedPermissions,
            ]);
            
            $this->loadAssignments();
            session()->flash('success', 'Đã thêm phạm vi quản lý.');
        } else {
             session()->flash('error', 'Đơn vị này đã được phân quyền rồi.');
        }
        
        $this->reset(['startAssignId', 'selectedPermissions']);
    }

    public function removeAssignment($assignmentId)
    {
        if (!auth()->user()->isSecretary()) return;
        
        \App\Modules\Identity\Models\UserAssignment::destroy($assignmentId);
        $this->loadAssignments();
    }

    public function toggleRole($userId, $roleSlug)
    {
        // Only Super Admin or Admin can do this
        if (!auth()->user()->isSecretary()) { // Simplified check, ideally strict 'manage-users' permission
            return; 
        }

        $user = User::find($userId);
        $role = Role::where('slug', $roleSlug)->first();

        if ($user && $role) {
            if ($user->hasRole($roleSlug)) {
                $user->roles()->detach($role->id);
            } else {
                $user->roles()->attach($role->id);
            }
        }
    }

    public function render()
    {
        return view('livewire.user-management', [
            'users' => User::with('roles', 'member')->paginate(10),
            'roles' => Role::all(),
        ])->layout('layouts.app');
    }
}
