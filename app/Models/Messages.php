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
        'file_id' => NULL,
        'content' => '',
        'created_at'  => '1998-07-14 14:00:00'
    ];
    protected $fillable = [
        'user_id',
        'room_id',
        'file_id',
        'content',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function file()
    {
        return $this->hasOne(Files::class);
    }

    public function scopeRoomID($query, int $room_id)
    {
        return $query->where('room_id', $room_id)->orderBy('id', 'desc');
    }

    public static function get_difference(int $room_id, int $last_msg){

        return  DB::table('messages')->selectRaw('COUNT(id) as unreaded')->where('room_id', '=', $room_id)->where('id', '>', $last_msg)->first();
    }
}
