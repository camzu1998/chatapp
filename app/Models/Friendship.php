<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Database\Factories\FriendshipFactory;

class Friendship extends Model
{
    use HasFactory;
    protected $table = 'friendship';

    /**
     * Statuses = [
     *  0 => 'invite',
     *  1 => 'friendship',
     *  2 => 'blocked'
     * ]
     */

    protected $attributes = [
        'user_id' => false,
        'user2_id' => false,
        'status' => 0,
        'by_who' => false,
        'created_at' => '1998-07-14 07:00:00'
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'user_id';

    public function get_user($user_id = null){
        if(empty($user_id))
            return false;

        return DB::table($this->table)->where('user_id', '=', $user_id)->orWhere('user2_id', '=', $user_id)->orderBy('status', 'asc')->get();
    }

    // public function save($user_id = null,$friend_id = null){
    //     if(empty($user_id) || empty($friend_id))
    //         return false;

    //     $Date = date('Y-m-d H:i:s');
    //     DB::table($this->table)->insert([
    //         'user_id'    => $user_id,
    //         'user2_id'   => $friend_id,
    //         'status'     => 0,
    //         'created_at' => $Date,
    //     ]);
    //     return true;
    // }

    public function check($user_id  = null,$friend_id = null){
        if(empty($user_id) || empty($friend_id))
            return false;

        return DB::select('select * from friendship where (`user_id` = ? AND `user2_id` = ?) OR (`user_id` = ? AND `user2_id` = ?)', [$user_id, $friend_id, $friend_id, $user_id]);
    }

    public function update($user_id = null, $friend_id = null, $status = 0){
        if(empty($user_id) || empty($friend_id))
            return false;
        
        return DB::update('update friendship set status = ?, by_who = ? where (`user_id` = ? AND `user2_id` = ?) OR (`user_id` = ? AND `user2_id` = ?)',[$status, $user_id, $user_id, $friend_id, $friend_id, $user_id]);
    }

    public function delete($user_id  = null,$friend_id = null){
        return DB::delete('delete from friendship where (`user_id` = ? AND `user2_id` = ?) OR (`user_id` = ? AND `user2_id` = ?)', [$user_id, $friend_id, $friend_id, $user_id]);
    }


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return FriendshipFactory::new();
    }

}
