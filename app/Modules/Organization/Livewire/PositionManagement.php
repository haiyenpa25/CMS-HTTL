<?php

namespace App\Modules\Organization\Livewire;

use Livewire\Component;
use App\Modules\Organization\Models\Position;
use Illuminate\Validation\Rule;

class PositionManagement extends Component
{
    public $positions;
    public $positionId;
    public $name;
    public $slug;
    public $description;
    public $level = 0;
    public $isModalOpen = false;
    public $confirmingDeletion = false;
    public $idToDelete;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('positions')->ignore($this->positionId)],
            'description' => 'nullable|string',
            'level' => 'required|integer|min:0',
        ];
    }

    public function mount()
    {
        $this->loadPositions();
    }

    public function loadPositions()
    {
        $this->positions = Position::orderBy('level')->get();
    }

    public function render()
    {
        return view('livewire.position-management')->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function edit($id)
    {
        $position = Position::findOrFail($id);
        $this->positionId = $id;
        $this->name = $position->name;
        $this->slug = $position->slug;
        $this->description = $position->description;
        $this->level = $position->level;
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        Position::updateOrCreate(['id' => $this->positionId], [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'level' => $this->level,
        ]);

        session()->flash('message', $this->positionId ? 'Cập nhật chức vụ thành công.' : 'Thêm chức vụ thành công.');
        $this->closeModal();
        $this->loadPositions();
    }

    public function delete($id)
    {
        $this->idToDelete = $id;
        $this->confirmingDeletion = true;
    }

    public function destroy()
    {
        if ($this->idToDelete) {
            $position = Position::find($this->idToDelete);
            if ($position) {
                // Check if position is in use
                if ($position->assignments()->count() > 0) {
                    session()->flash('error', 'Không thể xóa chức vụ đang được sử dụng.');
                } else {
                    $position->delete();
                    session()->flash('message', 'Đã xóa chức vụ.');
                }
            }
            $this->loadPositions();
        }
        $this->confirmingDeletion = false;
        $this->idToDelete = null;
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
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->level = 0;
        $this->positionId = null;
    }
}
