<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    public function get_user_friends($switch_response = 'json'){
        $user_id = Auth::id();
        $user_friends = array();
        $friends_data = array();
        
        $friendsModel = new \App\Models\Friendship();

        $friends = $friendsModel->get($user_id);
        foreach($friends as $key => $friend){
            if($key == $user_id){
                $data = $this->save_user($friend);
            }else{
                $data = $this->save_user($key);
            }
            $user_friends[] = $data['user_friends'];
            $friends_data[$key] = $data['friends_data'];
        }

        if($switch_response == 'json'){
            return response()->json([
                'friends' => $user_friends,
                'friends_data' => $friends_data,
            ]);
        }else if($switch_response == 'array'){
            return [
                'friends' => $user_friends,
                'friends_data' => $friends_data,
            ];
        }
    }

    private function save_user($user_id){
        $data = array();

        $userModel = new \App\Models\User();

        $user_friends['id'] = $user_id;
        $friend_data = $userModel->get_user_data($user_id);
        $friends_data['nick'] = $friend_data['nick'];
        $friends_data['profile_img'] = $friend_data['profile_img']; 

         $data['friends_data'] = $friends_data;
         $data['user_friends'] = $user_friends;
         return $data;
    }

    public function save_friendship(Request $request){
        $friendsModel = new \App\Models\Friendship();
        $user_id = Auth::id();

        if(empty($request->friend_id) || !is_numeric($request->friend_id)){
            return response()->json([
                'msg' => 'Friend id not defined'
            ]);
        }

        $friend_id = $request->friend_id;
        if($friendsModel->save($user_id, $friend_id)){
            return response()->json([
                'msg' => 'Friend added'
            ]); 
        }

        return response()->json([
            'msg' => 'Friend isnt added but not your fault :)'
        ]);
    }
}
