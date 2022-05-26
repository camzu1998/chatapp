<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    use HasFactory;

    public const USER_SETTINGS_TYPES = [
        'sounds',
        'notifications',
        'send_on_enter'
    ];

    protected $table = 'user_settings';

    protected $attributes = [
        'user_id' => false,
        'name' => 'sounds',
        'value' => 0,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeName($query, string $name)
    {
        return $query->where('name', $name);
    }
}
