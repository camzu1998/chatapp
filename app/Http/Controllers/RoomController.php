<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friendship;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function get_user_rooms($switch_response = 'json'){
        $user_id = Auth::id();
        $rooms_data = array();
        
        $roomModel = new \App\Models\Room();
        $userModel = new \App\Models\User();

        $user_rooms = $roomModel->get_user_rooms($user_id);
        if(empty($user_rooms[0])){
            //User has no rooms
            if($switch_response == 'json'){
                return response()->json([
                    'rooms_data' => []
                ]);
            }else if($switch_response == 'array'){
                return [];
            }
        }
        foreach($user_rooms as $user_room){
            //Get info aboout room & save to array
            $rooms_data[$user_room->room_id] = $roomModel->get($user_room->room_id);
            $user_data = $userModel->get_user_data($rooms_data[$user_room->room_id]->admin_id);
            $rooms_data[$user_room->room_id]->admin_img = $user_data->profile_img;
            $rooms_data[$user_room->room_id]->status = $user_room->status;
            $rooms_data[$user_room->room_id]->nickname = $user_room->nickname;
        }

        if($switch_response == 'json'){
            return response()->json([
                'rooms_data' => $rooms_data
            ]);
        }else if($switch_response == 'array'){
            return $rooms_data;
        }
    }

    public function save_room(Request $request){
        $roomModel = new \App\Models\Room();
        $userModel = new \App\Models\User();
        $friendsModel = new \App\Models\Friendship();
        $user = Auth::user();
        $user_id = Auth::id();

        if(empty($request->add_friend)){
            return response()->json([
                'msg' => 'Please add some friends to room'
            ]);
        }

        $room_name = $request->room_name;
        if(empty($room_name)){
            $room_name = $user->nick.'_room';
        }
        $room_id = $roomModel->save($room_name, $user_id);

        //Add invited friends to room
        foreach($request->add_friend as $friend_id){
            //Check friendship
            $res = $friendsModel->check($user_id, $friend_id);
            if(empty($res[0])){
                continue; 
            }
            $roomModel->add_user($room_id, $friend_id);
        }

        return true;
    }

    public function update_room_status(Request $request, $room_id){
        $roomModel = new \App\Models\Room();
        $userModel = new \App\Models\User();
        $user_id = Auth::id();
        $actions = array('acceptInvite', 'decelineInvite', 'outRoom', 'blockRoom', 'deleteRoom');

        //Valid action
        if(!in_array($request->button, $actions)){
            return response()->json([
                'msg' => 'Invalid action'
            ]);
        }
        //Valid status
        $res = $roomModel->check($user_id, $room_id);
        if(empty($res->created_at)){
            return response()->json([
                'msg' => 'Friendship never exist'
            ]); 
        }
        switch($request->button){
            case 'acceptInvite':
                $status = $roomModel->update($user_id, $room_id, 1);
            break;
            case 'decelineInvite':
                $status = $roomModel->update($user_id, $room_id, 2);
            break;
            case 'outRoom':
                $status = $roomModel->delete_user($user_id, $room_id);
            break;
            case 'blockRoom':
                $status = $roomModel->update($user_id, $room_id, 2);
            break;
            case 'deleteRoom':
                $status = $roomModel->delete($user_id, $room_id);
            break;
        }
        //Update room status
        return $status;
    }
}
