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
        'user_id' => false,
        'room_id' => false,
        'file_id' => false,
        'content' => '',
        'created_at'  => '1998-07-14 14:00:00'
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'user_id';

    /**
     * Scope a query to only include room messages
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoom($query, int $room_id)
    {
        return $query->where('room_id', $room_id)->latest();
    }


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return MessagesFactory::new();
    }


    //LEGACY
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
