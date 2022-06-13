<?php

namespace App\Observers;

use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomObserver
{
    public function created(Room $room)
    {
        //Todo: change to room member controller
        $room_member = $room->roomMembers()->create([
            'user_id' => $room->admin_id,
            'status' => 1
        ]);
        Log::debug('Added room owner as member: '.$room->admin_id);
    }

    public function updated(Room $room)
    {
        //
    }

    public function deleted(Room $room)
    {
//        $room->roomMembers()->delete();
    }

    public function restored(Room $room)
    {
        //
    }

    public function forceDeleted(Room $room)
    {
        //
    }
}
