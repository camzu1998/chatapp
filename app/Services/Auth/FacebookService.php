<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FacebookService implements AuthInterface
{
    public function createOrUpdate($socialUser): User
    {
        $user = User::email($socialUser->email)->first();

        if(empty($user)){
            $user = User::create([
                'nick' => $socialUser->name,
                'email' => $socialUser->email,
                'password' => Hash::make(Str::random(20)),
                'profile_img' => $socialUser->avatar,
                'fb_id' => $socialUser->id
            ]);
        }else{
            $user->google_id = $socialUser->id;
            $user->save();
        }

        return $user;
    }

    public function findUser($socialUser): User|null
    {
        return User::facebookID($socialUser->id)->first();
    }
}