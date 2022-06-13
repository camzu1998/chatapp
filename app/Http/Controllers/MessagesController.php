<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Models\RoomMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Messages;
use App\Models\Files;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessagesController extends Controller
{
    public function get_newest_id(int $room_id)
    {
        //Check if user is in the room
        $room = Room::with('messages')->find($room_id);
        $room_member = Auth::user()->roomMember()->RoomID($room_id)->first();
        if (empty($room_member->created_at) || $room_member->status != 1) {
            return response()->json([
                'status' => 2,
                'msg'    => 'Brak użytkownika w pokoju'
            ]);
        }

        $msgs = $room->messages()->latest()->orderByDesc('id')->first();
        if (empty($msgs->id)) {
            return 0;
        }

        return $msgs->id;
    }

    public function send(int $room_id, SendMessageRequest $request)
    {
        $data = $request->validated();

        //Check if user is in the room
        $room_member = Auth::user()->roomMember()->RoomID($room_id)->first();

        //Get messages
        DB::transaction(function () use ($room_member, $data, $room_id) {
            $msg = Messages::create([
                'user_id' => Auth::id(),
                'room_id' => $room_id,
                'file_id' => 0,
                'content' => $data['content'],
                'created_at'  => date('Y-m-d H:i:s')
            ]);
            //Set user message
            $room_member->last_msg_id = $msg->id;
            $room_member->save();
        }, 5);

        return $this->get($room_id);
    }
    public function upload(int $room_id, Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'status' => false,
                'step'   => 0
            ]);
        };

        //Check if user is in the room
        $room_member = Auth::user()->roomMember()->RoomID($room_id)->first();
        if (empty($room_member->created_at) || $room_member->status != 1) {
            return response()->json([
                'status' => false,
                'step'   => 1
            ]);
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
        $room_member->last_msg_id = $msg->id;
        $room_member->save();

        return $this->get($room_id);
    }

    public function get(int $room_id)
    {
        $users_array = [];
        $file_array = [];

        $room_member = Auth::user()->roomMember()->RoomID($room_id)->first();
        if (empty($room_member->created_at) || $room_member->status != 1) {
            return response()->json([
                'status' => false,
                'step'   => 0,
                'data' => $room_member
            ]);
        };

        $room = Room::with('messages')->find($room_id);
        $msgs = $room->messages;
        foreach ($msgs as $k => $msg) {
            //Check file data
            if ($msg->file_id != 0) {
                $file_array[$msg->file_id] = Files::find($msg->file_id);
            }
            //Check user data
            $msg_user = $msg->user;
            $users_array[$msg_user->id] = [
                'nick' => $msg_user->nick,
                'profile_img' => $msg_user->profile_img
            ];
        }
        $newest_id = $this->get_newest_id($room_id);
        //Set user message
        $room_member->last_msg_id = $newest_id;
        $room_member->save();

        return response()->json([
            'messages'   => $msgs,
            'msg_users'  => $users_array,
            'newest_msg' => $newest_id,
            'files'      => $file_array,
        ]);
    }

    public function get_array($room_id = null): array
    {
        $users_array = [];
        $file_array = [];

        $room_status = Auth::user()->roomMember()->roomID($room_id)->first();
        if (empty($room_status->created_at) || $room_status->status != 1) {
            return [];
        }

        $room = Room::with('messages')->find($room_id);
        $msgs = $room->messages;
        if ($msgs) {
            foreach ($msgs as $k => $msg) {
                //Check file data
                if ($msg->file_id != 0) {
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
