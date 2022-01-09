<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Friendship;
use App\Models\Room;
use App\Models\User;
use App\Models\Messages;

class RoomController extends Controller
{
    protected $profile_ext = array('png', 'jpeg', 'jpg');

    public function get_user_rooms($switch_response = 'json'){
        $user_id = Auth::id();
        $rooms_data = array();
        
        $roomModel = new Room();
        $userModel = new User();
        $msgsModel = new Messages();

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
            $rooms_data[$user_room->room_id]->unreaded = 0;
            //Unread messages
            if($user_room->status == 1){
                $res = $msgsModel->get_difference($user_room->room_id, $user_room->last_msg_id);
                $rooms_data[$user_room->room_id]->unreaded = $res->unreaded;
            }
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

    public function update_room_status(Request $request, int $room_id){
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
    /**
     * Upload room profile image
     */
    public function upload_room_profile(int $room_id, Request $request){
        if(empty($room_id) || !$request->hasFile('room_profile'))
            return response()->json([
                'err' => '1',
            ]);

        $user_id = Auth::id();

        $file = $request->room_profile;
        $filename = $file->getClientOriginalName();

        $roomModel = new \App\Models\Room();
        //Compare user and admin ids
        $tmp = $roomModel->check_admin($user_id, $room_id);
        if(empty($tmp))
            return response()->json([
                'err' => '2',
            ]);
        
        //Check extension & weight
        if(!in_array($file->extension(), $this->profile_ext)){
            //Extension didn't pass
            return response()->json([
                'err' => '3',
            ]);
        }
        if($file->getSize() > (1024 * (1024 * 25))){
            //File is oversized
            return response()->json([
                'err' => '4',
            ]);
        }
        //Check if need to delete previous image
        if($tmp->room_img != 'no_image.jpg'){
            //Delete old profile image
            Storage::delete('room_miniatures/'.$tmp->room_img);
        }
        //Store image
        $path = $file->storeAs('room_miniatures', $filename);
        //Change img in db
        $roomModel->update_img($room_id, $filename);

        return $path;
    }
    /**
     *  Update room data
     */
    public function update(Request $request, int $room_id){
        if(empty($room_id) || empty($request->input('update_room_name')))
            return response()->json([
                'err' => '1',
            ]);

        $user_id = Auth::id();
        $userRoomModel = new \App\Models\UserRoom();
        $roomModel = new \App\Models\Room();
        //Check user admin
        $tmp = $roomModel->check_admin($user_id, $room_id);
        if(empty($tmp))
            return response()->json([
                'err' => '2',
            ]);

        //Delete retrieved roommates
        if(!empty($request->roommate)){
            foreach($request->roommate as $roommate){
                $userRoomModel->delete_user($roommate, $room_id);
            }
        }
        //Update name
        $roomModel->update_room($room_id, $request->input('update_room_name'));

        return true;
    }
    /**
     *  Get room roommates
     */
    public function get_roommates(int $room_id){
        $roommates_data = array();
        if(empty($room_id))
            return false;

        $userRoomModel = new \App\Models\UserRoom();
        $userModel = new \App\Models\User();
        $roommates = $userRoomModel->get_roommates($room_id);

        foreach($roommates as $roommate){
            //Retrieve user data
            $user = $userModel->get_user_data($roommate->user_id);
            $roommates_data[$roommate->user_id]['nick'] = $user->nick;
            $roommates_data[$roommate->user_id]['status'] = $roommate->status;
            $roommates_data[$roommate->user_id]['room_id'] = $roommate->room_id;
            $roommates_data[$roommate->user_id]['nickname'] = $roommate->nickname;
            $roommates_data[$roommate->user_id]['profile_img'] = $user->profile_img;
        }

        return $roommates_data;
    }
    /**
     * Delete room and connected data
     */
    public function delete_room(int $room_id){
        if(empty($room_id))
            return false;
        
        $user_id = Auth::id();
        //Delete room image
        $roomModel = new Room();
        $tmp = $roomModel->check_admin($user_id, $room_id);
        Storage::delete('room_miniatures/'.$tmp->room_img);
        //Delete room
        $roomModel->delete_room($user_id, $room_id);
        return true;
    }
    /**
     * Send invites to friends
     */
    public function invite(int $room_id, Request $request){
        $roomModel = new \App\Models\Room();
        $friendsModel = new \App\Models\Friendship();
        $user_id = Auth::id();

        if(empty($request->add_friend)){
            return response()->json([
                'msg' => 'Please add some friends to room'
            ]);
        }

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
    /**
     *  Get room data
     */
    public function get_room(int $room_id){        
        $roomModel = new \App\Models\Room();
        $user_id = Auth::id();

        //Check if user is in room

        return $roomModel->get($room_id);
    }
}
