<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public const PROFILE_PATH = 'room_miniatures';

    protected $fillable = [
        'admin_id',
        'room_name',
        'profile_img',
        'created_at'
    ];

    protected $attributes = [
        'admin_id' => false,
        'room_name' => 'default_room_name',
        'profile_img' => 'no_image.jpg',
        'created_at' => false
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
