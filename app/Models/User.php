<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    public const PROFILE_PATH = 'profiles_miniatures';

    protected $table = 'users';
    protected $fillable = [
        'nick',
        'email',
        'password',
        'profile_img'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $attributes = [
        'nick' => false,
        'email' => false,
        'email_verified_at' => null,
        'password' => false,
        'remember_token' => false,
        'reset_token' => false,
        'profile_img' => 'no_image.jpg',
    ];

    public function userSettings()
    {
        return $this->hasMany(UserSettings::class);
    }

    public function roomMember()
    {
        return $this->hasMany(RoomMember::class);
    }

    public function adminRoom() //TODO: isAdmin($user_id)
    {
        return $this->hasMany(Room::class, 'admin_id');
    }

    public function messages()
    {
        return $this->belongsToMany(Messages::class);
    }

    public function userFriends()
    {
        return $this->friends().$this->isFriendsWith();
    }
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendship', 'user_id', 'user2_id');
    }
    public function isFriendsWith()
    {
        return $this->belongsToMany(User::class, 'friendship', 'user2_id', 'user_id');
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
