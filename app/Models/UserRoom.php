<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserRoom extends Model
{
    use HasFactory;

    protected $table = 'user_room';

    protected $attributes = [
        'room_id' => 0,
        'user_id' => 0,
        'status' => 0,
        'last_msg_id' => 0,
        'last_notify_id' => 0,
        'nickname' => '',
        'created_at' => '1998-07-14 07:00:00'
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'user_id';

    /**
     * Scope a query to only include user
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUser($query, int $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope a query to only include room
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoom($query, int $room_id)
    {
        return $query->where('room_id', $room_id);
    }
}
