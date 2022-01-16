<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\Files;
use App\Models\Room;
use App\Models\User;
use App\Models\UserRoom;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function get_newest_id($room_id = null){        
        // Check if isset room_id
        if(empty($room_id) || !is_numeric($room_id))
            return false;

        $room = new Room();
        $room_status = $room->check(Auth::id(), $room_id);
        if(empty($room_status->created_at) && $room_status->status != 1)
            return false;

        $msgM = new Messages();
        $msgs = $msgM->get_last($room_id);
        if(empty($msgs->id)){
            return false;
        }

        return $msgs->id;
    }

    public function send(int $room_id, Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return redirect('/');
        }

        $content = $request->input('content');
        if(empty($content))
            return false;

        // Check if isset room_id
        if(!empty($room_id)){
            $room = new Room();
            $room_status = $room->check(Auth::id(), $room_id);
            if(empty($room_status->created_at) && $room_status->status != 1){
                return false;
            }
        }
        //Check if user is in the room
        $msg = new Messages();
        $UserRoomModel = new UserRoom();

        $msg_id = $msg->create($room_id, $content, 0, Auth::id());
        $UserRoomModel->set_user_msg($room_id, Auth::id(), $msg_id);

        return $this->get($room_id);
    }
    public function upload(int $room_id, Request $request){
        if(!$request->hasFile('file'))
            return response()->json([
                'status' => false,
                'step'   => 0
            ]);;

        // Check if isset room_id
        if(!empty($room_id)){
            $room = new Room();
            //Check if user is in the room
            $room_status = $room->check(Auth::id(), $room_id);
            if(empty($room_status->created_at) || $room_status->status != 1){
                return response()->json([
                    'status' => false,
                    'step'   => 1
                ]);;
            }
        }
        $files_con = new FilesController();
        $msg = new Messages();
        $UserRoomModel = new UserRoom();
        //Store file
        $file = $files_con->store($request);
        //Add message
        $msg_id = $msg->create($room_id, '', $file['file_id'], Auth::id());
        $UserRoomModel->set_user_msg($room_id, Auth::id(), $msg_id);
        return $this->get($room_id);
    }

    public function get($room_id = null){
        $users_array = array();
        $file_array = array();
        
        $msgM = new \App\Models\Messages;
        $files_model = new Files();
        $user_model = new User();
        $room_model = new Room();
        
        $tmp = $room_model->check(Auth::id(), $room_id);
        if(empty($tmp) || $tmp->status != 1)
            return response()->json([
                'status' => false,
                'step'   => 0
            ]);;

        $msgs = $msgM->get($room_id, 10);

        foreach($msgs as $k => $msg){
            //Check file data
            if($msg->file_id != 0){
                $file_array[$msg->file_id] = $files_model->get($msg->file_id);
            }
            //Check user data
            $msg_user = $user_model->get_user_data($msg->user_id);
            $users_array[$msg->user_id] = [
                'nick' => $msg_user->nick,
                'profile_img' => $msg_user->profile_img
            ];
        }
        $UserRoomModel = new UserRoom();
        $newest_id = $this->get_newest_id($room_id);
        $UserRoomModel->set_user_msg($room_id, Auth::id(), $newest_id);

        return response()->json([
            'messages'   => $msgs,
            'msg_users'  => $users_array,
            'newest_msg' => $newest_id,
            'files'      => $file_array,
        ]);
    }

    public function get_array($room_id = null){
        $users_array = array();
        $file_array = array();
        
        $msgM = new Messages();
        $files_model = new Files();
        $user_model = new User();
        $room_model = new Room();
        
        $tmp = $room_model->check(Auth::id(), $room_id);
        if(empty($tmp) || $tmp->status != 1)
            return false;
        
        $msgs = $msgM->get($room_id, 10);

        foreach($msgs as $k => $msg){
            //Check file data
            if($msg->file_id != 0){
                $file_array[$msg->file_id] = $files_model->get($msg->file_id);
            }
            //Check user data
            $msg_user = $user_model->get_user_data($msg->user_id);
            $users_array[$msg->user_id] = [
                'nick' => $msg_user->nick,
                'profile_img' => $msg_user->profile_img
            ];
        }

        return [
            'messages'   => $msgs,
            'msg_users'  => $users_array,
            'newest_msg' => $this->get_newest_id($room_id),
            'files'      => $file_array,
        ];
    }
}
