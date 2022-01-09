<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserRoom extends Model
{
    use HasFactory;

    protected $table = 'user_room';

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
}
