<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserSettings extends Model
{
    use HasFactory;

    protected $table = 'user_settings';

    protected $inputs = [
        'sounds' => 0,
        'notifications' => 0,
        'press_on_enter' => 0
    ];

    protected $attributes = [
        'user_id' => false,
        'name' => 'sounds',
        'value' => 0,
        'created_at' => false,
        'updated_at' => false
    ];

    public $incrementing = true;

    /**
     * Scope a query to only include user
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUser($query, int $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope a query to only include user
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeName($query, string $name)
    {
        return $query->where('name', $name);
    }
}
