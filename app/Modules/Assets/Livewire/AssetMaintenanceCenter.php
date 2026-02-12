<?php

namespace App\Modules\Assets\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Modules\Assets\Models\Asset;
use App\Modules\Assets\Models\AssetTicket;
use App\Modules\Assets\Models\AssetMaintenanceSchedule;
use App\Modules\Identity\Models\User;
use Illuminate\Support\Facades\Auth;

class AssetMaintenanceCenter extends Component
{
    use WithFileUploads;

    // Data Collections
    public $tickets;
    public $schedules;
    public $technicians;
    public $assets; // Simple list for dropdowns

    // UI State
    public $activeTab = 'tickets'; // tickets, schedules
    public $filterStatus = '';
    public $filterPriority = '';
    public $search = '';

    // Ticket Slide-over State
    public $isTicketModalOpen = false;
    public $editingTicketId = null;
    
    // Ticket Form Fields
    public $ticket_asset_id;
    public $ticket_title; // Derived or auto-generated
    public $ticket_description;
    public $ticket_priority = 'medium';
    public $ticket_status = 'new';
    public $ticket_assigned_to;
    public $ticket_cost;
    public $ticket_images = []; // For upload
    public $existing_images = []; // For display

    // Schedule Slide-over State
    public $isScheduleModalOpen = false;
    public $editingScheduleId = null;
    
    // Schedule Form Fields
    public $schedule_asset_id;
    public $schedule_frequency_type = 'month';
    public $schedule_interval = 1;
    public $schedule_status = 'active';
    public $schedule_notes;

    protected $listeners = ['refreshMaintenance' => '$refresh'];

    public function mount()
    {
        $this->technicians = User::all(); // Should filter by role/permission in real app
        $this->loadAssets();
        $this->loadData();
    }

    public function loadAssets()
    {
        $this->assets = Asset::select('id', 'name', 'code')->orderBy('name')->get();
    }

    public function loadData()
    {
        // Load Tickets
        $ticketQuery = AssetTicket::with(['asset', 'reporter', 'assignee'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $ticketQuery->whereHas('asset', function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('code', 'like', '%'.$this->search.'%');
            })->orWhere('code', 'like', '%'.$this->search.'%');
        }

        if ($this->filterStatus) {
            $ticketQuery->where('status', $this->filterStatus);
        }
        
        if ($this->filterPriority) {
            $ticketQuery->where('priority', $this->filterPriority);
        }

        $this->tickets = $ticketQuery->get();

        // Load Schedules
        $this->schedules = AssetMaintenanceSchedule::with('asset')
            ->orderBy('next_due_at', 'asc')
            ->get();
    }

    public function updatedSearch() { $this->loadData(); }
    public function updatedFilterStatus() { $this->loadData(); }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // --- Ticket Actions ---

    public function createTicket()
    {
        $this->resetTicketForm();
        $this->isTicketModalOpen = true;
    }

    public function editTicket($id)
    {
        $this->resetTicketForm();
        $this->editingTicketId = $id;
        $ticket = AssetTicket::findOrFail($id);
        
        $this->ticket_asset_id = $ticket->asset_id;
        $this->ticket_description = $ticket->issue_description;
        $this->ticket_priority = $ticket->priority;
        $this->ticket_status = $ticket->status;
        $this->ticket_assigned_to = $ticket->assigned_to;
        $this->ticket_cost = $ticket->cost;
        $this->existing_images = $ticket->images ?? [];
        
        $this->isTicketModalOpen = true;
    }

    public function saveTicket()
    {
        $this->validate([
            'ticket_asset_id' => 'required|exists:assets,id',
            'ticket_description' => 'required|string',
            'ticket_priority' => 'required|in:low,medium,high,critical',
        ]);

        // Handle Image Upload (Placeholder logic)
        $imagePaths = $this->existing_images;
        // if ($this->ticket_images) { store images and add to $imagePaths }

        // Generate Code if new
        $code = $this->editingTicketId ? null : 'TK-' . date('Ymd') . '-' . rand(100, 999);

        $data = [
            'asset_id' => $this->ticket_asset_id,
            'issue_description' => $this->ticket_description,
            'priority' => $this->ticket_priority,
            'status' => $this->ticket_status,
            'assigned_to' => $this->ticket_assigned_to,
            'cost' => $this->ticket_cost ?? 0,
            'type' => 'repair', // Defaulting to repair for now
            'images' => $imagePaths,
        ];

        if (!$this->editingTicketId) {
            $data['code'] = $code;
            $data['reported_by'] = Auth::id() ?? 1; // Fallback for dev
            AssetTicket::create($data);
        } else {
            AssetTicket::where('id', $this->editingTicketId)->update($data);
        }

        $this->isTicketModalOpen = false;
        $this->loadData();
        session()->flash('message', 'Đã lưu phiếu hỗ trợ.');
    }

    public function resetTicketForm()
    {
        $this->editingTicketId = null;
        $this->ticket_asset_id = '';
        $this->ticket_description = '';
        $this->ticket_priority = 'medium';
        $this->ticket_status = 'new';
        $this->ticket_assigned_to = '';
        $this->ticket_cost = '';
        $this->ticket_images = [];
        $this->existing_images = [];
    }

    // --- Schedule Actions ---

    public function createSchedule()
    {
        $this->resetScheduleForm();
        $this->isScheduleModalOpen = true;
    }

    public function editSchedule($id)
    {
        $this->editingScheduleId = $id;
        $schedule = AssetMaintenanceSchedule::findOrFail($id);
        
        $this->schedule_asset_id = $schedule->asset_id;
        $this->schedule_frequency_type = $schedule->frequency_type;
        $this->schedule_interval = $schedule->interval;
        $this->schedule_status = $schedule->status;
        $this->schedule_notes = $schedule->notes;

        $this->isScheduleModalOpen = true;
    }

    public function saveSchedule()
    {
        $this->validate([
            'schedule_asset_id' => 'required|exists:assets,id',
            'schedule_interval' => 'required|integer|min:1',
        ]);

        $data = [
            'asset_id' => $this->schedule_asset_id,
            'frequency_type' => $this->schedule_frequency_type,
            'interval' => $this->schedule_interval,
            'status' => $this->schedule_status,
            'notes' => $this->schedule_notes,
        ];

        // Ensure next_due_at is calculated if new or updated logic needed
        // For simplicity, if creating, set next due based on last performed or now
        if (!$this->editingScheduleId) {
            $data['next_due_at'] = now()->addMonths($this->schedule_interval); // Rough calc
            AssetMaintenanceSchedule::create($data);
        } else {
             AssetMaintenanceSchedule::where('id', $this->editingScheduleId)->update($data);
        }

        $this->isScheduleModalOpen = false;
        $this->loadData();
    }

    public function resetScheduleForm()
    {
        $this->editingScheduleId = null;
        $this->schedule_asset_id = '';
        $this->schedule_frequency_type = 'month';
        $this->schedule_interval = 1;
        $this->schedule_status = 'active';
        $this->schedule_notes = '';
    }

    public function render()
    {
        return view('livewire.asset-maintenance-center')->layout('layouts.app');
    }
}
