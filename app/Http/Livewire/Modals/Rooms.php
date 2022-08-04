<?php

namespace App\Http\Livewire\Modals;

use Livewire\Component;

class Rooms extends Component
{
    public $roomsMember = [];
    public $search = '';

    public function mount()
    {

    }

    protected $rules = [
        'search' => 'string|max:255|nullable',
    ];

    public function render()
    {
        $searchQuery = '%' . $this->search . '%';

        $this->roomsMember = auth()->user()->roomMember->load(['room' => function ($query) use ($searchQuery) {
            $query->where('room_name', 'like', $searchQuery);
        }]);
        $this->roomsMember = $this->roomsMember->whereNotNull('room');

        return view('livewire.modals.rooms');
    }
}
