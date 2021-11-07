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
        $userModel = new \App\Models\User();

        $friends = $friendsModel->get($user_id);
        foreach($friends as $friend){
            $invite = 0;
            if($friend->user_id == $user_id){
                //Send Invite
                $friend_id = $friend->user2_id;
            }else{
                //Received invite
                $friend_id = $friend->user_id;
                $invite = 1;
            }

            $user_friends[]['id'] = $friend_id;
            $friend_data = $userModel->get_user_data($friend_id);
            $friends_data[$friend_id]['nick'] = $friend_data->nick;
            $friends_data[$friend_id]['profile_img'] = $friend_data->profile_img; 
            $friends_data[$friend_id]['status'] = $friend->status;
            $friends_data[$friend_id]['invite'] = $invite;
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

    public function save_friendship(Request $request){
        $friendsModel = new \App\Models\Friendship();
        $userModel = new \App\Models\User();
        $user_id = Auth::id();

        if(empty($request->nickname)){
            return response()->json([
                'msg' => 'Friend id not defined'
            ]);
        }
        $friend_id = $userModel->get_user_id($request->nickname);

        $res = $friendsModel->check($user_id, $friend_id->id);
        if(!empty($res[0])){
            return response()->json([
                'msg' => 'Friend already exist'
            ]); 
        }


        if($friendsModel->save($user_id, $friend_id->id)){
            return response()->json([
                'msg' => 'Friend added'
            ]); 
        }

        return response()->json([
            'msg' => 'Friend isnt added but not your fault :)'
        ]);
    }

    public function update_friendship_status(Request $request, $friend_id){
        $friendsModel = new \App\Models\Friendship();
        $userModel = new \App\Models\User();
        $user_id = Auth::id();
        $statuses = array(1,2); // 1 - Friendship confirmed 2 - Friendship blocked

        //Valid status
        if(!in_array($request->status, $statuses)){
            return response()->json([
                'msg' => 'Invalid status'
            ]);
        }
        //Valid friendship
        $res = $friendsModel->check($user_id, $friend_id);
        if(empty($res[0])){
            return response()->json([
                'msg' => 'Friendship never exist'
            ]); 
        }

        //Update friendship status
        return $friendsModel->update($user_id, $friend_id, $request->status);

    }
}
