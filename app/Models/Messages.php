<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Files;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $form = [
        'user_id',
        'room_id',
        'file_id',
        'content',
        'created_at'
    ];

    public function get($room_id = null, $limiter = null){
        if(!empty($limiter) && is_numeric($limiter)){
            return  DB::table($this->table)->select()->where('room_id', '=', $room_id)->take($limiter)->latest()->get();
        }else{
            return  DB::table($this->table)->select()->where('room_id', '=', $room_id)->latest()->get();   
        }
    }
    public function get_last($room_id = null){
        if(empty($room_id))
            return false;

        return  DB::table($this->table)->select()->where('room_id', '=', $room_id)->latest()->first(); 
    }

    public function save($room_id = null, $content = null, $file_id = null, $user_id = null){
        if(empty($room_id) || empty($content) || empty($user_id))
            return false;

        $Date = date('Y-m-d H:i:s');
        DB::table($this->table)->insert([
            'user_id'    => $user_id,
            'room_id'    => $room_id,
            'file_id'    => $file_id,
            'content'    => $content,
            'created_at' => $Date,
        ]);

        return true;
    }
}
