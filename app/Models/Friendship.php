<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Friendship extends Model
{
    use HasFactory;
    protected $table = 'friendship';

    public function get($user_id = null){
        if(empty($user_id))
            return false;

        return DB::table($this->table)->where('user_id', '=', $user_id)->orWhere('user2_id', '=', $user_id)->orderBy('status', 'asc')->get();
    }

    public function save($user_id = null,$friend_id = null){
        if(empty($user_id) || empty($friend_id))
            return false;

        $Date = date('Y-m-d H:i:s');
        DB::table($this->table)->insert([
            'user_id'    => $user_id,
            'user2_id'   => $friend_id,
            'status'     => 0,
            'created_at' => $Date,
        ]);
        return true;
    }

    public function check($user_id  = null,$friend_id = null){
        if(empty($user_id) || empty($friend_id))
            return false;

        return DB::table($this->table)->select()->where(DB::raw('(`user_id` = '.$user_id.' AND `user2_id` = '.$friend_id.') OR (`user_id` = '.$friend_id.' AND `user2_id` = '.$user_id.')'))->get();
    }

    public function update($user_id = null, $friend_id = null, $status = 0){
        if(empty($user_id) || empty($friend_id))
            return false;
        
        return DB::table($this->table)->where(DB::raw('(`user_id` = '.$user_id.' AND `user2_id` = '.$friend_id.') OR (`user_id` = '.$friend_id.' AND `user2_id` = '.$user_id.')'))->update(['status' => $status]);
    }
}
