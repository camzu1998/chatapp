<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Database\Factories\RoomFactory;

class Room extends Model
{
    use HasFactory;

    protected $tables = ['room', 'user_room'];

    protected $table = 'room';

    protected $attributes = [
        'admin_id' => 0,
        'room_name' => 'default_room_name',
        'room_img' => 'no_image.jpg',
        'created_at' => '1998-07-14 07:00:00'
    ];

    public $timestamps = false;

    /**
     * Statuses = [
     *  0 => 'invite',
     *  1 => 'accepted'
     * ]
     */
    public function get(int $room_id){
        return DB::table($this->tables[0])->where('id', '=', $room_id)->first();
    }
    public function get_user_rooms($user_id = null){
        if(empty($user_id))
            return false;

        return DB::table($this->tables[1])->where('user_id', '=', $user_id)->orderBy('status', 'asc')->get();
    }

    public function save_room($room_name = null,$user_id = null){
        if(empty($user_id) || empty($room_name))
            return false;

        $Date = date('Y-m-d H:i:s');
        $id = DB::table($this->tables[0])->insertGetId([
            'admin_id'   => $user_id,
            'room_name'  => $room_name,
            'room_img'   => 'no_image.jpg',
            'created_at' => $Date,
        ]);
        DB::table($this->tables[1])->insert([
            'room_id'        => $id,
            'user_id'        => $user_id,
            'status'         => 1,
            'last_msg_id'    => 0,
            'last_notify_id' => 0,
            'nickname'       => '',
            'created_at'     => $Date,
        ]);
        return $id;
    }

    public function add_user($room_id = null, $user_id = null){
        if(empty($user_id) || empty($room_id))
            return false;

        $Date = date('Y-m-d H:i:s');
        DB::table($this->tables[1])->insert([
            'room_id'        => $room_id,
            'user_id'        => $user_id,
            'status'         => 0,
            'last_msg_id'    => 0,
            'last_notify_id' => 0,
            'nickname'       => '',
            'created_at'     => $Date,
        ]);
        return true;
    }

    public function check($user_id  = null, $room_id = null){
        if(empty($user_id) || empty($room_id))
            return false;

            return DB::table($this->tables[1])->where('user_id', '=', $user_id)->where('room_id', '=', $room_id)->first();
    }

    public function check_admin($admin_id  = null, $room_id = null){
        if(empty($admin_id) || empty($room_id))
            return false;

            return DB::table($this->tables[0])->where('admin_id', '=', $admin_id)->where('id', '=', $room_id)->first();
    }

    public function update($user_id = null, $room_id = null, $status = 0){
        if(empty($user_id) || empty($room_id))
            return false;
        
        return DB::table($this->tables[1])->where('room_id', '=', $room_id)->where('user_id', '=', $user_id)->update(['status' => $status]);
    }

    public function delete_room($admin_id = null, $room_id = null){
        if(empty($admin_id) || empty($room_id))
            return false;

        $tmp = $this->check_admin($admin_id, $room_id);
        if(empty($tmp))
            return false;

        DB::table($this->tables[1])->where('room_id', '=', $room_id)->delete();
        DB::table('messages')->where('room_id', '=', $room_id)->delete(); //Run Message Controller to delete messages & attachments

        return DB::table($this->tables[0])->where('admin_id', '=', $admin_id)->where('id', '=', $room_id)->delete();;
    }

    public function update_img($room_id = null, $filename = null){
        if(empty($room_id) || empty($filename))
            return false;

        return DB::table($this->tables[0])->where('id', '=', $room_id)->update(['room_img' => $filename]);
    }
    public function update_room($room_id = null, $room_name = null){
        if(empty($room_id) || empty($room_name))
            return false;

        return DB::table($this->tables[0])->where('id', '=', $room_id)->update(['room_name' => $room_name]);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return RoomFactory::new();
    }
}
