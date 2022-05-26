<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    use HasFactory;

    protected $table = 'room_members';

    protected $attributes = [
        'room_id' => 0,
        'user_id' => 0,
        'status' => 0,
        'last_msg_id' => 0,
        'last_notify_id' => 0,
        'nickname' => '',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
