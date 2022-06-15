<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\Room;
use App\Models\User;
use App\Models\RoomMember;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function notify_room_message(int $roomId, string $response = "JSON")
    {
        //Check if user has
        $roomMember = Auth::user()->roomMember()->roomID($roomId)->first();
        if (empty($roomMember->created_at) || $roomMember->status != 1) {
            if ($response == "ARRAY") {
                return [
                    'status' => false
                ];
            }
            return response()->json([
                'status' => false
            ]);
        }

        $msgs = Messages::roomID($roomId)->latest()->first();
        if (empty($msgs->id)) {
            if ($response == "ARRAY") {
                return [
                    'status' => false
                ];
            }
            return response()->json([
                'status' => false
            ]);
        }

        $diff = Messages::get_difference($roomId, $roomMember->last_msg_id);
        $unreaded = $diff->unreaded;
        //Check if user seen this message
        if ($roomMember->last_msg_id == $msgs->id || $msgs->user_id == Auth::id() || $roomMember->last_notify_id == $msgs->id) {
            if ($response == "ARRAY") {
                return [
                    'status' => true,
                    'unreaded' => $unreaded
                ];
            }
            return response()->json([
                'status' => true,
                'unreaded' => $unreaded
            ]);
        }
        //Get user & room data
        $user_room = RoomMember::Room($roomId)->User($msgs->user_id)->first();
        $nickname = $user_room->nickname;
        if (empty($nickname)) {
            //Get user data by id
            $user = User::find($msgs->user_id);
            $nickname = $user->nick;
        }
        $room_data = Room::find($roomId);
        $room_name = $room_data->room_name;
        //Update user_room table
        $roomMember->last_notify_id = $msgs->id;
        $roomMember->save();
        //Responses
        if ($response == "ARRAY") {
            return [
                'status' => true,
                'room_id' => $roomId,
                'room' => $room_name,
                'user' => $nickname,
                'content' => $msgs->content,
                'unreaded' => $unreaded
            ];
        }
        return response()->json([
            'status' => true,
            'room_id' => $roomId,
            'room' => $room_name,
            'user' => $nickname,
            'content' => $msgs->content,
            'unreaded' => $unreaded
        ]);
    }

    public function check_messages()
    {
        $notify = [];
        $notify['sum_unreaded'] = 0;

        $user_rooms = Auth::user()->roomMember()->get();
        foreach ($user_rooms as $roomMember) {
            $result = $this->notify_room_message($roomMember->room_id, "ARRAY");
            if ($result['status'] !== false) {
                $notify['sum_unreaded'] += $result['unreaded'];
                continue;
            }
            $notify[] = $result;
        }

        return response()->json($notify);
    }
}
