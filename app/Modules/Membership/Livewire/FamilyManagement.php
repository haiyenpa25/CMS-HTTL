<?php

namespace App\Modules\Membership\Livewire;

use App\Modules\Membership\Models\Family;
use Livewire\Component;
use Livewire\WithPagination;

class FamilyManagement extends Component
{
    use WithPagination;

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $families = Family::with('members')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->withCount('members')
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.family-management', [
            'families' => $families,
        ])->layout('layouts.app');
    }
}
