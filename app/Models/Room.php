<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Room extends Model
{
    use HasFactory;
    protected $table = ['room', 'user_room'];

    /**
     * Statuses = [
     *  0 => 'invite',
     *  1 => 'accepted'
     * ]
     */

    public function get($room_id = null){
        if(empty($room_id))
            return false;

        return DB::table($this->table[0])->where('id', '=', $room_id)->first();
    }
    public function get_user_rooms($user_id = null){
        if(empty($user_id))
            return false;

        return DB::table($this->table[1])->where('user_id', '=', $user_id)->orderBy('status', 'asc')->get();
    }

    public function save($room_name = null,$user_id = null){
        if(empty($user_id) || empty($room_name))
            return false;

        $Date = date('Y-m-d H:i:s');
        $id = DB::table($this->table[0])->insertGetId([
            'admin_id'   => $user_id,
            'room_name'  => $room_name,
            'room_img'   => 'no_image.jpg',
            'created_at' => $Date,
        ]);
        DB::table($this->table[1])->insert([
            'room_id'    => $id,
            'user_id'    => $user_id,
            'status'     => 1,
            'nickname'   => '',
            'created_at' => $Date,
        ]);
        return $id;
    }

    public function add_user($room_id = null, $user_id = null){
        if(empty($user_id) || empty($room_id))
            return false;

        $Date = date('Y-m-d H:i:s');
        DB::table($this->table[1])->insert([
            'room_id'    => $room_id,
            'user_id'    => $user_id,
            'status'     => 0,
            'nickname'   => '',
            'created_at' => $Date,
        ]);
        return true;
    }

    public function check($user_id  = null,$friend_id = null){
        if(empty($user_id) || empty($friend_id))
            return false;

        return DB::select('select * from friendship where (`user_id` = ? AND `user2_id` = ?) OR (`user_id` = ? AND `user2_id` = ?)', [$user_id, $friend_id, $friend_id, $user_id]);
    }

    public function update($user_id = null, $friend_id = null, $status = 0){
        if(empty($user_id) || empty($friend_id))
            return false;
        
        return DB::update('update friendship set status = ?, by_who = ? where (`user_id` = ? AND `user2_id` = ?) OR (`user_id` = ? AND `user2_id` = ?)',[$status, $user_id, $user_id, $friend_id, $friend_id, $user_id]);
    }

    public function delete($user_id  = null,$friend_id = null){
        if(empty($user_id) || empty($friend_id))
            return false;

        return DB::delete('delete from friendship where (`user_id` = ? AND `user2_id` = ?) OR (`user_id` = ? AND `user2_id` = ?)', [$user_id, $friend_id, $friend_id, $user_id]);
    }
}
