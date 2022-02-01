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
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return RoomFactory::new();
    }
}
