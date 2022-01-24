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
            if($switch_response == 'json'){
                return response()->json([
                    'status' => 9,
                    'msg'    => 'Brak autoryzacji',
                    'friends_data' => []
                ]);
            }else{
                return [
                    'status' => 9,
                    'msg'    => 'Brak autoryzacji',
                    'friends_data' => []
                ];
            }
        }

        $user_id = Auth::id();
        $friends_data = array();
        $banned_friends_data = array();
        
        $friendsModel = new Friendship();
        $userModel = new User();

        $friends = Friendship::where('user_id', '=', $user_id)->orWhere('user2_id', '=', $user_id)->orderBy('status', 'asc')->get();
        if(empty($friends[0]->created_at)){
            if($switch_response == 'json'){
                return response()->json([
                    'status' => 1,
                    'msg'    => 'No friends',
                    'friends_data' => $friends_data
                ]);
            }else if($switch_response == 'array'){
                return $friends_data;
            }
        }
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
                'status' => 0,
                'friends_data' => $friends_data
            ]);
        }else if($switch_response == 'array'){
            return $friends_data;
        }
    }

    public function save_friendship(Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        if(empty($request->nickname)){
            return response()->json([
                'status' => 1,
                'msg'    => 'Friend id not defined'
            ]);
        }

        $friendsModel = new Friendship();
        $userModel = new User();
        $user_id = Auth::id();

        $friend_id = User::select('id')->where('nick', 'LIKE', $request->nickname)->first();

        $res = $friendsModel->check($user_id, $friend_id->id);
        if(!empty($res[0]->created_at)){
            return response()->json([
                'status' => 2,
                'msg'    => 'Friend already exist'
            ]); 
        }

        $friendship = Friendship::factory()->create([
            'user_id' => $user_id,
            'user2_id' => $friend_id->id,
            'by_who' => $user_id
        ]);
        if($friendship->user_id == $user_id){
            return response()->json([
                'status' => 0,
                'msg'    => 'Friend added'
            ]); 
        }

        return response()->json([
            'status' => 3,
            'msg'    => 'Friend isnt added but not your fault :)'
        ]);
    }

    public function update_friendship_status(Request $request, $friend_id){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
        
        $friendsModel = new \App\Models\Friendship();
        $userModel = new \App\Models\User();
        $user_id = Auth::id();
        $actions = array('acceptInvite', 'decelineInvite', 'cancelInvite', 'deleteFriendship', 'blockFriendship');

        //Valid status
        if(!in_array($request->button, $actions)){
            return response()->json([
                'status' => 1,
                'msg'    => 'Invalid action'
            ]);
        }
        //Valid friendship
        $res = $friendsModel->check($user_id, $friend_id);
        if(empty($res[0])){
            return response()->json([
                'status' => 2,
                'msg'    => 'Friendship never exist'
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
        if($status != 0){
            return response()->json([
                'status' => 0,
                'msg'    => 'Success'
            ]);
        }
        return response()->json([
            'status' => 3,
            'msg'    => 'Error'
        ]);
    }
}
