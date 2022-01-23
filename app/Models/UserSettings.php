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
        'created_at' => false
    ];

    public function get($user_id = 0, $name = ''){
        if(empty($name) || empty($user_id))
            return false;

        return DB::table($this->table)->select('value')->where('user_id', '=', $user_id)->where('name', 'LIKE', $name)->first();
    }

    public function get_all(int $user_id){
        if(empty($user_id))
            return false;

        return DB::table($this->table)->select()->where('user_id', '=', $user_id)->get();
    }

    public function set(int $user_id, string $name, int $value = 0){
        return DB::table($this->table)->where('user_id', $user_id)->where('name', 'LIKE', $name)->update(['value' => $value, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function add(int $user_id, string $name, int $value){
        if(empty($name) || empty($user_id))
            return false;

        if(empty($value)){
            $value = 0;
        }

        $id = DB::table($this->table)->insertGetId([
            'user_id' => $user_id,
            'name'    => $name,
            'value'   => $value
        ]);
        
        return $id;
    }
}
