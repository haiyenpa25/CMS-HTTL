<?php

namespace App\Livewire;

use App\Modules\Membership\Models\Member;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GpsCapture extends Component
{
    public $memberId;
    public $latitude;
    public $longitude;
    public $accuracy;
    public $capturing = false;
    public $saved = false;

    public function mount($memberId)
    {
        $this->memberId = $memberId;
        
        // Load existing location if available
        $member = Member::find($memberId);
        if ($member && $member->hasLocation()) {
            $this->latitude = $member->latitude;
            $this->longitude = $member->longitude;
            $this->saved = true;
        }
    }

    public function captureLocation()
    {
        $this->capturing = true;
        $this->dispatch('capture-gps');
    }

    public function updateLocationFromBrowser($lat, $lng, $accuracy)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->accuracy = $accuracy;
        $this->capturing = false;

        $this->saveLocation();
    }

    public function saveLocation()
    {
        $member = Member::find($this->memberId);
        
        if ($member) {
            $member->update([
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'location_updated_at' => now(),
                'location_updated_by' => Auth::id(),
            ]);

            $this->saved = true;
            session()->flash('message', 'Đã lưu vị trí GPS');
        }
    }

    public function render()
    {
        return view('livewire.gps-capture');
    }
}
