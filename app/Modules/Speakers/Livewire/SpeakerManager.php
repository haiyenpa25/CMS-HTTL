<?php

declare(strict_types=1);

namespace App\Modules\Speakers\Livewire;

use App\Modules\Speakers\Models\Speaker;
use App\Modules\Attendance\Models\AttendanceSession;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SpeakerManager extends Component
{
    public $speakers;
    
    // Form fields
    public $speakerId;
    public $name;
    public $title;
    public $phone;
    public $email;
    public $organization; // Using existing field name
    public $bio;
    public $avatar_url;
    
    // UI state
    public $isModalOpen = false;
    public $confirmingDeletion = false;
    public $idToDelete;
    public $search = '';
    public $filterStatus = '';
    public $activeTab = 'personal'; // personal, church, history

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'avatar_url' => 'nullable|url',
        ];
    }

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $query = Speaker::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhere('organization', 'like', '%' . $this->search . '%');
            });
        }

        $this->speakers = $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        // Calculate statistics
        $totalSpeakers = $this->speakers->count();
        $activeSpeakers = $this->speakers->count(); // All are active for now
        
        // Try to get session statistics, handle if speaker_id column doesn't exist yet
        try {
            $totalSessions = AttendanceSession::whereNotNull('speaker_id')->count();
            
            // Get top speaker (most sessions)
            $topSpeaker = AttendanceSession::selectRaw('speaker_id, COUNT(*) as session_count')
                ->whereNotNull('speaker_id')
                ->groupBy('speaker_id')
                ->orderByDesc('session_count')
                ->with('speaker')
                ->first();
        } catch (\Exception $e) {
            // Column doesn't exist yet, set defaults
            $totalSessions = 0;
            $topSpeaker = null;
        }

        return view('livewire.speakers.manager', [
            'totalSpeakers' => $totalSpeakers,
            'activeSpeakers' => $activeSpeakers,
            'totalSessions' => $totalSessions,
            'topSpeaker' => $topSpeaker,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit($id)
    {
        $speaker = Speaker::findOrFail($id);
        $this->speakerId = $id;
        $this->name = $speaker->name;
        $this->title = $speaker->title;
        $this->phone = $speaker->phone;
        $this->email = $speaker->email ?? '';
        $this->organization = $speaker->organization;
        $this->bio = $speaker->bio;
        $this->avatar_url = $speaker->avatar_url;
        
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        Speaker::updateOrCreate(['id' => $this->speakerId], [
            'name' => $this->name,
            'title' => $this->title,
            'phone' => $this->phone,
            'email' => $this->email,
            'organization' => $this->organization,
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
        ]);

        session()->flash('message', $this->speakerId ? 'Cập nhật diễn giả thành công.' : 'Thêm diễn giả thành công.');
        $this->closeModal();
        $this->loadData();
    }

    public function delete($id)
    {
        $this->idToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function destroy()
    {
        if ($this->idToDelete) {
            Speaker::find($this->idToDelete)->delete();
            session()->flash('message', 'Đã xóa diễn giả.');
            $this->loadData();
        }
        $this->confirmingDeletion = false;
        $this->idToDelete = null;
    }

    public function openModal()
    {
        $this->isModalOpen = true;
        $this->activeTab = 'personal';
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }
    
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    private function resetInputFields()
    {
        $this->speakerId = null;
        $this->name = '';
        $this->title = '';
        $this->phone = '';
        $this->email = '';
        $this->organization = '';
        $this->bio = '';
        $this->avatar_url = '';
        $this->activeTab = 'personal';
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    public function getSessionHistory($speakerId)
    {
        try {
            return AttendanceSession::where('speaker_id', $speakerId)
                ->with('department')
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            // Column doesn't exist yet, return empty collection
            return collect([]);
        }
    }
}
