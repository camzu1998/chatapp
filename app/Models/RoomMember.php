<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomMember extends Model
{
    use HasFactory;

    public const ROOM_MEMBER_STATUS = [
        'pendingInvite' => 0,
        'acceptInvite' => 1,
        'declineInvite' => 2,
        'blockedRoom' => 2,
        'outRoom' => 2
    ];

    protected $table = 'room_members';

    public $fillable = [
        'room_id',
        'user_id',
        'status',
        'last_msg_id',
        'last_notify_id',
        'nickname',
    ];

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

    public function scopeRoomID($query, int $room_id)
    {
        return $query->where('room_id', $room_id);
    }

    public function scopeUserID($query, int $user_id)
    {
        return $query->where('user_id', $user_id);
    }
}
