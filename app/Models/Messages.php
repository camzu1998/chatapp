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

    protected $fillable = [
        'user_id',
        'room_id',
        'file_id',
        'content',
    ];

    public $timestamps = false;
    public $incrementing = true;
    protected $primaryKey = 'id';

    /**
     * Scope a query to only include room messages
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoom($query, int $room_id)
    {
        return $query->where('room_id', $room_id)->orderBy('id', 'desc');
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

    public static function get_difference(int $room_id, int $last_msg){

        return  DB::table('messages')->selectRaw('COUNT(id) as unreaded')->where('room_id', '=', $room_id)->where('id', '>', $last_msg)->first();
    }
}
