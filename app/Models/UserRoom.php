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

    
    //LEGACY
    public function delete_user(int $user_id, int $room_id){
        if(empty($user_id) || empty($room_id))
            return false;

        return DB::table($this->table)->where('room_id', '=', $room_id)->where('user_id', '=', $user_id)->delete();  
    }

    public function get_roommates(int $room_id){
        if(empty($room_id))
            return false;

        return DB::table($this->table)->where('room_id', '=', $room_id)->get();
    }

    public function set_user_msg(int $room_id, int $user_id, int $msg_id){
        return DB::table($this->table)->where('room_id', $room_id)->where('user_id', $user_id)->update(['last_msg_id' => $msg_id]);
    }

    public function set_user_notify(int $room_id, int $user_id, int $notify_id){
        return DB::table($this->table)->where('room_id', $room_id)->where('user_id', $user_id)->update(['last_notify_id' => $notify_id]);
    }
}
