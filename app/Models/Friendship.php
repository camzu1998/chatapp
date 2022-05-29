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
    public const FRIENDSHIP_INVITE_STATUS = 0;
    public const FRIENDSHIP_STATUS = 1;
    public const FRIENDSHIP_BLOCKED_STATUS = 2;

    protected $attributes = [
        'user_id' => false,
        'user2_id' => false,
        'status' => 0,
        'by_who' => false,
        'created_at' => '1998-07-14 07:00:00'
    ];

    public $incrementing = false;
    protected $primaryKey = 'user_id';

    /**
     * Scope a query to only include user friendships
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUser($query, $user_id)
    {
        return $query->where('user_id', '=', $user_id)->orWhere('user2_id', '=', $user_id)->orderBy('status', 'asc');
    }

    public static function check($user_id  = null, $friend_id = null){
        if(empty($user_id) || empty($friend_id))
            return false;

        return DB::select('select * from friendship where (`user_id` = ? AND `user2_id` = ?) OR (`user_id` = ? AND `user2_id` = ?)', [$user_id, $friend_id, $friend_id, $user_id]);
    }

    public static function set_status($user_id = null, $friend_id = null, $status = 0){
        if(empty($user_id) || empty($friend_id))
            return false;
        
        return DB::update('update friendship set status = ?, by_who = ? where (`user_id` = ? AND `user2_id` = ?) OR (`user_id` = ? AND `user2_id` = ?)',[$status, $user_id, $user_id, $friend_id, $friend_id, $user_id]);
    }

    public static function delete_friendship($user_id  = null,$friend_id = null){
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
