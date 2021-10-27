<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    public function get_user_friends(){
        $user_id = Auth::id();

        $friendsModel = new \App\Models\Friendship();
        $userModel = new \App\Models\User();
        
        $friends = $friendsModel->get($user_id);
        foreach($friends as $key => $friend){

        }

        return response()->json([
            'friends' => $file_array,
        ]);
    }
}
