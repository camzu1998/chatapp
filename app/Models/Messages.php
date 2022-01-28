<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Files;

use Database\Factories\MessagesFactory;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $attributes = [
        'user_id',
        'room_id',
        'file_id',
        'content',
        'created_at'
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'room_id';

    public function get(int $room_id, int $limiter = null){
        if(!empty($limiter) && is_numeric($limiter)){
            return  DB::table($this->table)->select()->where('room_id', '=', $room_id)->take($limiter)->latest()->get();
        }else{
            return  DB::table($this->table)->select()->where('room_id', '=', $room_id)->latest()->get();   
        }
    }
    public function get_last(int $room_id){
        if(empty($room_id))
            return false;

        return  DB::table($this->table)->select()->where('room_id', '=', $room_id)->latest()->first(); 
    }
    public function get_difference(int $room_id, int $last_msg){

        return  DB::table($this->table)->selectRaw('COUNT(id) as unreaded')->where('room_id', '=', $room_id)->where('id', '>', $last_msg)->first();
    }

    public function create(int $room_id, string $content, int $file_id, int $user_id){
        if(empty($room_id) || empty($user_id) || (empty($content) && empty($file_id)))
            return false;

        $date = date('Y-m-d H:i:s');
        return DB::table($this->table)->insertGetId([
            'user_id'    => $user_id,
            'room_id'    => $room_id,
            'file_id'    => $file_id,
            'content'    => $content,
            'created_at' => $date,
        ]);
    }

    public function delete_room(int $room_id){
        if(empty($room_id))
            return false;

        DB::table($this->table)->where('room_id', '=', $room_id)->delete(); 

        return true;
    }
}
