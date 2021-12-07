<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    public function get_user_friends($switch_response = 'json'){
        if (!Auth::check()) {
            // The user is not logged in...
            return redirect('/');
        }
        
        $user_id = Auth::id();
        $friends_data = array();
        $banned_friends_data = array();
        
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

            $friend_data = $userModel->get_user_data($friend_id);
            $friends_data[$friend_id]['nick'] = $friend_data->nick;
            $friends_data[$friend_id]['profile_img'] = $friend_data->profile_img; 
            $friends_data[$friend_id]['status'] = $friend->status;
            $friends_data[$friend_id]['by_who'] = $friend->by_who;
            $friends_data[$friend_id]['invite'] = $invite;
        }

        if($switch_response == 'json'){
            return response()->json([
                'friends_data' => $friends_data
            ]);
        }else if($switch_response == 'array'){
            return $friends_data;
        }
    }

    public function save_friendship(Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return redirect('/');
        }

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
        if (!Auth::check()) {
            // The user is not logged in...
            return redirect('/');
        }

        $friendsModel = new \App\Models\Friendship();
        $userModel = new \App\Models\User();
        $user_id = Auth::id();
        $actions = array('acceptInvite', 'decelineInvite', 'cancelInvite', 'deleteFriendship', 'blockFriendship');

        //Valid status
        if(!in_array($request->button, $actions)){
            return response()->json([
                'msg' => 'Invalid action'
            ]);
        }
        //Valid friendship
        $res = $friendsModel->check($user_id, $friend_id);
        if(empty($res[0])){
            return response()->json([
                'msg' => 'Friendship never exist'
            ]); 
        }
        switch($request->button){
            case 'acceptInvite':
                $status = $friendsModel->update($user_id, $friend_id, 1);
            break;
            case 'decelineInvite':
                $status = $friendsModel->update($user_id, $friend_id, 2);
            break;
            case 'cancelInvite':
                $status = $friendsModel->delete($user_id, $friend_id);
            break;
            case 'deleteFriendship':
                $status = $friendsModel->delete($user_id, $friend_id);
            break;
            case 'blockFriendship':
                $status = $friendsModel->update($user_id, $friend_id, 2);
            break;
        }
        //Update friendship status
        return $status;
    }
}
