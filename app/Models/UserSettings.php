<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserSettings extends Model
{
    use HasFactory;
    protected $table = 'user_settings';

    public function get($user_id = 0, $name = ''){
        if(empty($name) || empty($user_id))
            return false;

        return DB::table($this->table)->select('value')->where('user_id', '=', $user_id)->where('name', 'LIKE', $name)->get();
    }

    public function set($user_id = 0, $name = '', $value = ''){
        if(empty($name) || empty($user_id))
            return false;

        if(empty($value)){
            $value = 0;
        }

        DB::table($this->table)->upsert([
            ['user_id' => $user_id, 'name' => $name, 'value' => $value]
        ],  ['user_id', 'name'], ['value']);
        
        return true;
    }
}
