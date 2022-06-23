<?php

namespace App\Http\Livewire\Modals;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserSettings extends Component
{
    public $userSettings = [];

    public function mount()
    {
        foreach(Auth::user()->userSettings as $userSetting) {
            $this->userSettings[$userSetting->name] = (bool)$userSetting->value;
        }
    }

    protected $rules = [
        'userSettings.sounds' => 'nullable',
        'userSettings.notifications' => 'nullable',
        'userSettings.send_on_enter' => 'nullable',
    ];

    public function render()
    {
        return view('livewire.modals.user-settings');
    }

    public function save()
    {
        $this->validate();

        foreach ($this->userSettings as $name => $value) {
            Auth::user()->userSettings()->name($name)->update(['value' => $value]);
        }

        session()->flash(
            'success',
            'Settings Saved Successfully!!'
        );
    }
}
