<?php

use App\Models\Room;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('private-room.{room}', function ($user, Room $room) {
    $room_member = $user->roomMember()->roomID($room->id)->first();
    return $room_member->status == 1;
});
Broadcast::channel('private-room_member', function ($user) {
    return true;
});

Broadcast::channel('channel-test', function () {
    return true;
});
Broadcast::channel('presence-channel-test', function () {
    return true;
});

