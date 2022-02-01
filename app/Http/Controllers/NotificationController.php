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
        //Check if user has 
        $room_status = UserRoom::Room($room_id)->User(Auth::id())->first();
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

        $msgs = Messages::Room($room_id)->latest()->first();
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
        
        $diff = Messages::get_difference($room_id, $room_status->last_msg_id);
        $unreaded = $diff->unreaded;
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
        $user_room = UserRoom::Room($room_id)->User($msgs->user_id)->first();
        $nickname = $user_room->nickname;
        if(empty($nickname)){
            //Get user data by id
            $user = User::find($msgs->user_id);
            $nickname = $user->nick;
        }
        $room_data = Room::find($room_id);
        $room_name = $room_data->room_name;
        //Update user_room table
        UserRoom::Room($room_id)->User(Auth::id())->update(['last_notify_id' => $msgs->id]);
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

        $user_rooms = UserRoom::User(Auth::id())->get();
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
