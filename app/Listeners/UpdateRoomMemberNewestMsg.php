<?php

namespace App\Listeners;

use App\Events\RoomMemberProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateRoomMemberNewestMsg
{
    public function __construct()
    {
        //
    }

    public function handle(RoomMemberProcessed $event): void
    {
        $event->room_member->last_msg_id = $event->newest_msg_id;
        DB::transaction(function() use ($event) {
            $event->room_member->save();
        }, 5);
    }
}
