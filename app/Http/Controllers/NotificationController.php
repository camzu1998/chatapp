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
    public function notify_room_message(int $room_id){
        $room = new Room();
        $room_status = $room->check(Auth::id(), $room_id);
        if(empty($room_status->created_at) && $room_status->status != 1)
            return false;

        $msgM = new Messages();
        $msgs = $msgM->get_last($room_id);
        if(empty($msgs->id)){
            return false;
        }
        //Get user & room data
        $nickname = "";
        if(empty($nickname)){
            //Get user data by id
            $userModel = new User();
            $user = $userModel->get_user_data($msgs->user_id);
            $nickname = $user->nick;
        }
        $room_data = $room->get($room_id);
        $room_name = $room_data->room_name;
        //Responses JSONs
    }

    public function check_messages(){

    }
}
