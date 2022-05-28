<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Models\User;
use App\Mail\ResetPassword;

class UserRepository {

    private $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function create(array $payload): bool
    {
        $pass = Hash::make($payload['pass']);

        DB::transaction(function () use ($pass, $payload) {
            $this->model->create([
                'nick'     => $payload['nick'],
                'email'    => $payload['email'],
                'password' => $pass
            ]);
        }, 5);
        
        return true;
    }

    public function create_reset_password(array $payload): bool
    {
        $user = $this->model->where('email', $payload['email'])->first();
        if (!empty($user->created_at)){
            $token = Str::random(40);
            $user->reset_token = $token;
            $user->save();
            //Send email
            if (config('app.env') == 'production')
            {
                Mail::to($user->email)->send(new ResetPassword($token));
            }

            return true;
        }

        return false;
    }

    public function store_new_password(array $payload, string $token): bool
    {
        $user = User::where('reset_token', $token)->first();
        if(!empty($user->created_at)){

            $pass = Hash::make($payload['pass']);
            $user->password = $pass;
            $user->save();
            
            return true;
        }

        return false;
    }
}