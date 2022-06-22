<?php

namespace App\Events;

use App\Models\Messages;
use App\Models\Room;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $user;
    protected $room;
    protected $message;

    public function __construct(User $user, Room $room, Messages $message)
    {
        $this->user = $user;
        $this->room = $room;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel("room.{$this->room->id}"),
            new PrivateChannel("room_member")
        ];
    }

    public function broadcastAs()
    {
        return 'NewMessageEvent';
    }

    public function broadcastWith()
    {
        return array_merge($this->message->toArray(), ['user' => $this->user->only('id', 'nick')]);
    }
}
