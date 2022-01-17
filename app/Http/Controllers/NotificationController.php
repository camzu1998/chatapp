<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\Room;
use App\Models\User;
use App\Models\UserRoom;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function notify_room_message(int $room_id, string $response = "JSON"){
        $room = new Room();
        //Check if user has 
        $room_status = $room->check(Auth::id(), $room_id);
        if(empty($room_status->created_at) || $room_status->status != 1){
            if($response == "ARRAY"){
                return [
                    'status' => false
                ];
            }
            return response()->json([
                'status' => false
            ]);
        }

        $msgM = new Messages();
        $msgs = $msgM->get_last($room_id);
        if(empty($msgs->id)){
            if($response == "ARRAY"){
                return [
                    'status' => false
                ];
            }
            return response()->json([
                'status' => false
            ]);
        }
        $res = $msgM->get_difference($room_id, $room_status->last_msg_id);
        $unreaded = $res->unreaded;
        //Check if user seen this message
        if($room_status->last_msg_id == $msgs->id || $msgs->user_id == Auth::id() || $room_status->last_notify_id == $msgs->id){
            if($response == "ARRAY"){
                return [
                    'status' => false,
                    'unreaded' => $unreaded
                ];
            }
            return response()->json([
                'status' => false,
                'unreaded' => $unreaded
            ]);
        }
        //Get user & room data
        $user_room = $room->check($msgs->user_id, $room_id);
        $nickname = $user_room->nickname;
        if(empty($nickname)){
            //Get user data by id
            $userModel = new User();
            $user = $userModel->get_user_data($msgs->user_id);
            $nickname = $user->nick;
        }
        $room_data = $room->get($room_id);
        $room_name = $room_data->room_name;
        //Update user_room table
        $userRoomModel = new UserRoom();
        $userRoomModel->set_user_notify($room_id, Auth::id(), $msgs->id);
        //Responses
        if($response == "ARRAY"){
            return [
                'status' => true,
                'room_id' => $room_id,
                'room' => $room_name,
                'user' => $nickname,
                'content' => $msgs->content,
                'unreaded' => $unreaded
            ];
        }
        return response()->json([
            'status' => true,
            'room_id' => $room_id,
            'room' => $room_name,
            'user' => $nickname,
            'content' => $msgs->content,
            'unreaded' => $unreaded
        ]);
    }

    public function check_messages(){
        $notify = [];
        $notify['sum_unreaded'] = 0;
        $roomModel = new Room();
        $user_rooms = $roomModel->get_user_rooms(Auth::id());
        foreach($user_rooms as $user_room){
            $result = $this->notify_room_message($user_room->room_id, "ARRAY");
            if($result['status'] == false){
                $notify['sum_unreaded'] += $result['unreaded'];
                continue;
            }
            $notify[] = $result;
            $notify['sum_unreaded'] += $result['unreaded'];
        }
        
        return response()->json($notify);
    }
}
