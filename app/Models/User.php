<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function save_user($nick, $email, $pass){
        $date = date('Y-m-d H:i:s');
        $user_id = DB::table($this->table)->insertGetId([
            'nick'       => $nick,
            'email'      => $email,
            'password'   => $pass,
            'created_at' => $date,
        ]);

        return $user_id;
    }
    public function check_names( string $nick, string $email){
        return DB::table($this->table)->where('nick', '=', $nick)->orWhere('email', '=', $email)->first();
    }
    public function get_users(){
        return DB::table($this->table)->select('id', 'nick', 'profile_img')->get();
    }
    public function get_user_data($user_id = null){
        if(!$user_id)
            return false;

        return DB::table($this->table)->select('id', 'nick', 'profile_img')->where('id', '=', $user_id)->first();
    }
    public function get_user_id($nickname = ''){
        if(empty($nickname))
            return false;

        return DB::table($this->table)->select('id')->where('nick', 'LIKE', $nickname)->first();
    }
    public function set_profile_image($user_id = null, $filename = 'no_image.jpg'){
        if(!$user_id)
            return false;
        
        $date = date('Y-m-d H:i:s');
        DB::table($this->table)->where('id', '=', $user_id)->update([
            'profile_img' => $filename,
            'updated_at'  => $date,
        ]);

        return true;
    }
    public function update_pass(string $pass, string $token, int $user_id){
        return DB::table($this->table)->where('id', $user_id)->where('reset_token', 'LIKE', $token)->update([
            'password'    => $pass,
            'reset_token' => NULL
        ]);
    }

    public function check_email(string $email, int $user_id = null){
        if(empty($email))
            return false;

        if($user_id != null){
            return DB::table($this->table)->where('email', 'LIKE', $email)->where('id', '!=', $user_id)->first();
        }else{
            return DB::table($this->table)->where('email', 'LIKE', $email)->first();
        }
    }

    public function set_token(int $user_id, string $token = ""){
        $date = date('Y-m-d H:i:s');

        return DB::table($this->table)->where('id', $user_id)->update([
            'reset_token' => $token
        ]);
    }

    public function check_token(string $token){
        if(empty($token))
            return false;

        return DB::table($this->table)->where('reset_token', 'LIKE', $token)->first();
    }
}
