<?php

namespace App\Http\Controllers;

use App\Models\RoomMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Messages;
use App\Models\Files;
use App\Models\Room;
use App\Models\User;

use App\Http\Controllers\FilesController;
use App\Http\Controllers\RoomController;

class MessagesController extends Controller
{
    public function get_newest_id(int $room_id)
    {
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }      
        // Check if isset room_id
        if(empty($room_id) || !is_numeric($room_id))
            return response()->json([
                'status' => 1,
                'msg'    => 'Błedny id pokoju'
            ]);

        //Check if user is in the room
        $room = Room::with('messages')->find($room_id);
        $room_status = Auth::user()->roomMember()->RoomID($room_id)->first();
        if(empty($room_status->created_at) || $room_status->status != 1)
            return response()->json([
                'status' => 2,
                'msg'    => 'Brak użytkownika w pokoju'
            ]);

        $msgs = $room->messages()->latest()->first();
        if(empty($msgs->id)){
            return 0;
        }

        return $msgs->id;
    }

    public function send(int $room_id, Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        $content = $request->input('content');
        if(empty($content))
            return response()->json([
                'status' => 1,
                'msg'    => 'Pusta wiadomość'
            ]);

        // Check if isset room_id
        if(!empty($room_id)){
            //Check if user is in the room
            $room_status = RoomMember::User(Auth::id())->Room($room_id)->first();
            if(empty($room_status->created_at) || $room_status->status != 1){
                return response()->json([
                    'status' => 2,
                    'msg'    => 'Brak użytkownika w pokoju'
                ]);
            }
        }
        
        //Get messages
        $msg = Messages::create([
            'user_id' => Auth::id(),
            'room_id' => $room_id,
            'file_id' => 0,
            'content' => $content,
            'created_at'  => date('Y-m-d H:i:s')
        ]);
        //Set user message
        RoomMember::User(Auth::id())->Room($room_id)->update(['last_msg_id' => $msg->id]);

        return $this->get($room_id);
    }
    public function upload(int $room_id, Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        if(!$request->hasFile('file'))
            return response()->json([
                'status' => false,
                'step'   => 0
            ]);;

        // Check if isset room_id
        if(!empty($room_id)){
            //Check if user is in the room
            $room_status = RoomMember::User(Auth::id())->Room($room_id)->first();
            if(empty($room_status->created_at) || $room_status->status != 1){
                return response()->json([
                    'status' => false,
                    'step'   => 1
                ]);;
            }
        }
        $files_con = new FilesController();
        //Store file
        $file = $files_con->store($request);
        //Add message
        $msg = Messages::create([
            'user_id' => Auth::id(),
            'room_id' => $room_id,
            'file_id' => $file['file_id'],
            'content' => '',
            'created_at'  => date('Y-m-d H:i:s')
        ]);
        //Set user message
        RoomMember::User(Auth::id())->Room($room_id)->update(['last_msg_id' => $msg->id]);

        return $this->get($room_id);
    }

    public function get(int $room_id){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        $users_array = array();
        $file_array = array();
        
        $room_status = RoomMember::User(Auth::id())->Room($room_id)->first();
        if(empty($room_status->created_at) || $room_status->status != 1)
            return response()->json([
                'status' => false,
                'step'   => 0,
                'data' => $room_status
            ]);;

        $msgs = Messages::Room($room_id)->take(10)->get();
        foreach($msgs as $k => $msg){
            //Check file data
            if($msg->file_id != 0){
                $file_array[$msg->file_id] = Files::find($msg->file_id);
            }
            //Check user data
            $msg_user = User::find($msg->user_id);
            $users_array[$msg->user_id] = [
                'nick' => $msg_user->nick,
                'profile_img' => $msg_user->profile_img
            ];
        }
        $newest_id = $this->get_newest_id($room_id);
        //Set user message
        RoomMember::User(Auth::id())->Room($room_id)->update(['last_msg_id' => $newest_id]);

        return response()->json([
            'messages'   => $msgs,
            'msg_users'  => $users_array,
            'newest_msg' => $newest_id,
            'files'      => $file_array,
        ]);
    }

    public function get_array($room_id = null)
    {
        $users_array = array();
        $file_array = array();
                
        $room_status = Auth::user()->roomMember()->roomID($room_id)->first();
        if(empty($room_status->created_at) || $room_status->status != 1)
        {
            return false;
        }

        $room = Room::with('messages')->find($room_id);
        $msgs = $room->messages;
        if($msgs)
        {
            foreach($msgs as $k => $msg){
                //Check file data
                if($msg->file_id != 0){
                    $file_array[$msg->file_id] = Files::find($msg->file_id);
                }
                //Check user data
                $msg_user = User::find($msg->user_id);
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

        return [
            'messages'   => [],
            'msg_users'  => [],
            'newest_msg' => 0,
            'files'      => []
        ];
    }
}
