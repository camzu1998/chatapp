<?php

namespace App\Http\Livewire\Modals;

use App\Repositories\RoomRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateRoom extends Component
{
    public $add_friend;
    public $room_name;

    public function mount()
    {

    }

    public function render(): View
    {
        return view('livewire.modals.create-room');
    }

    protected array $rules = [
        'room_name' => 'nullable',
        'add_friend.*' => 'required'
    ];

    public function store(RoomRepository $roomRepository): void
    {
        $data = $this->validate();

        $roomRepository->create($data);

        session()->flash(
            'success',
            'Room Created Successfully!!'
        );
    }
}
