<?php

namespace App\Events;

use App\Models\RoomMember;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomMemberProcessed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $room_member;
    public $newest_msg_id;

    public function __construct(RoomMember $room_member, int $newest_msg_id)
    {
        $this->room_member = $room_member;
        $this->newest_msg_id = $newest_msg_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
