<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public const ROOM_PROFILE_EXT = ['png', 'jpeg', 'jpg'];

    protected $fillable = [
        'admin_id',
        'room_name',
        'room_img',
        'created_at'
    ];
    
    protected $attributes = [
        'admin_id' => 0,
        'room_name' => 'default_room_name',
        'room_img' => 'no_image.jpg',
        'created_at' => '1998-07-14 07:00:00'
    ];

    public $timestamps = false;

    public function messages()
    {
        return $this->hasMany(Messages::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    public function roomMembers()
    {
        return $this->hasMany(RoomMember::class);
    }

    public function scopeAdmin($query, $admin_id)
    {
        return $query->where('admin_id', $admin_id);
    }
}
