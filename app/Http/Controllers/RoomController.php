<?php

namespace App\Http\Controllers;

use App\Repositories\RoomRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Friendship;
use App\Models\Room;
use App\Models\User;
use App\Models\Messages;
use App\Models\RoomMember;

use App\Http\Requests\SaveRoomRequest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\UserSettingsController;

class RoomController extends Controller
{
    protected $profile_ext = array('png', 'jpeg', 'jpg');

    public $repository;

    public function __construct(RoomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get_user_rooms(string $switch_response = 'json'): mixed
    {
        $user_id = Auth::id();
        $rooms_data = array();

        $user_rooms = RoomMember::where('user_id', '=', $user_id)->orderBy('status', 'asc')->get();
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
            $room = $user_room->room;
            $rooms_data[$room->id] = $room;

            $user_data = User::find($room->admin_id);
            $rooms_data[$room->id]->admin_img = $user_data->profile_img;

            $rooms_data[$room->id]->status = $user_room->status;
            $rooms_data[$room->id]->nickname = $user_room->nickname;
            $rooms_data[$room->id]->unreaded = 0;
            //Unread messages
            if($user_room->status == 1){
                $res = Messages::get_difference($user_room->room_id, $user_room->last_msg_id);
                $rooms_data[$user_room->room_id]->unreaded = $res->unreaded ?: 0;

                if($res !== null)
                {
                    $last_msg = $room->messages()->latest()->first();
                    $rooms_data[$room->id]->last_msg_user = "Ty";
                    $rooms_data[$room->id]->last_msg_user_img = $user_room->user->profile_img;
                    if(!empty($last_msg->user_id))
                    {
                        if($last_msg->user_id != Auth::id()){
                            $last_user_data = User::find($last_msg->user_id);
                            $rooms_data[$user_room->room_id]->last_msg_user = $last_user_data->nick;
                            $rooms_data[$user_room->room_id]->last_msg_user_img = $last_user_data->profile_img;
                        }

                        $content = Str::limit($last_msg->content, 20);
                        if($last_msg->file_id != 0){
                            $content = "Wysłał załącznik";
                        }

                        $rooms_data[$user_room->room_id]->last_msg_content = $content;
                    }
                }
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

    public function save_room(SaveRoomRequest $request)
    {
        $data = $request->validated();

        $room = $this->repository->create($data);

        $this->repository->inviteFriends($data, $room);

        return response()->json([
            'status' => 0,
            'msg'    => 'Saved room'
        ]);
    }

    public function update_room_status(Request $request, int $room_id): mixed
    {
        //Valid status
        $user_room = RoomMember::where('user_id', Auth::id())->where('room_id', $room_id)->first();
        if(empty($user_room->created_at)){
            return response()->json([
                'status' => 2,
                'msg' => 'Brak autoryzacji'
            ]); 
        }
        switch($request->button){
            case 'acceptInvite':
                $user_room->status = 1;
                break;
            case 'declineInvite':
                $user_room->status = 2;
                break;
            case 'outRoom':
                $status = $user_room->delete();
                break;
            case 'blockRoom':
                $user_room->status = 2;
                break;
            case 'deleteRoom':
                return $this->delete_room($room_id);
                break;
        }
        if($user_room->isDirty()){
            $user_room->save();
        }

        return response()->json([
            'status' => 0,
            'msg'    => 'Success'
        ]);
    }
    /**
     * Upload room profile image
     */
    public function upload_room_profile(Request $request, int $room_id): string
    {
        $file = $request->room_profile;
        $filename = $file->getClientOriginalName();

        //Compare user and admin ids
        $room = Room::where('admin_id', Auth::id())->where('id', $room_id)->first();
        if(empty($room->created_at)){
            return response()->json([
                'status' => 2,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
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
        $room->room_img = $filename;
        if($room->room_img != 'no_image.jpg' && $room->isDirty()){
            //Delete old profile image
            Storage::delete('room_miniatures/'.$room->room_img);
            //Store image
            $path = $file->storeAs('room_miniatures', $filename);
            //Change img in db
            $room->save();
        }
        return $path;
    }
    /**
     *  Update room data
     */
    public function update(Request $request, int $room_id): mixed
    {
        $room = Room::where('admin_id', Auth::id())->where('id', $room_id)->first();
        if(empty($room->created_at)){
            return response()->json([
                'status' => 2,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
        //Delete retrieved roommates
        if(!empty($request->roommate)){
            foreach($request->roommate as $roommate){
                RoomMember::where('room_id', $room_id)->where('user_id', $roommate)->delete();
            }
        }
        //Update name
        $room->room_name = $request->update_room_name;
        if($room->isDirty()){
            $room->save();
        }

        return response()->json([
            'status' => 0,
            'msg'    => 'Success'
        ]);
    }
    /**
     *  Get room roommates
     */
    public function get_roommates(int $room_id): array
    {
        $roommates_data = array();
        if(empty($room_id))
            return false;

        $roommates = RoomMember::where('room_id', $room_id)->where('user_id', '!=', Auth::id())->get();
        foreach($roommates as $roommate){
            //Retrieve user data
            $user = User::find($roommate->user_id);
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
    public function delete_room(int $room_id): mixed
    {
        //Get room model
        $room = Room::where('admin_id', Auth::id())->where('id', $room_id)->first();
        if(empty($room->created_at)){
            return response()->json([
                'status' => 2,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
        if($room->room_img != 'no_image.jpg')
            Storage::delete('room_miniatures/'.$room->room_img);
        //Delete room
        if($room->delete() != 0){
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
    /**
     * Send invites to friends
     */
    public function invite(Request $request, int $room_id): mixed
    {
        $room = Room::where('admin_id', Auth::id())->where('id', $room_id)->first();
        if(empty($room->created_at)){
            return response()->json([
                'status' => 2,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
        //Add invited friends to room
        foreach($request->add_friend as $friend_id){
            //Check friendship
            $res = Friendship::check(Auth::id(), $friend_id);
            if(empty($res[0])){
                continue; 
            }
            RoomMember::factory()->create([
                'room_id' => $room_id,
                'user_id' => $friend_id
            ]);
        }

        return response()->json([
            'status' => 0,
            'msg'    => 'Success'
        ]);
    }
    /**
     *  Get room data
     */
    public function get_room(int $room_id): mixed
    {        
        //Check if user is in room
        $room = RoomMember::where('user_id', Auth::id())->where('room_id', $room_id)->where('status', 1)->first();
        if(empty($room->created_at)){
            return false;
        }
        return $room;
    }
    /**
     * Return room view
     */
    public function load_room(Request $request, int $room_id): mixed
    {
        $data = $this->repository->getRoomData($room_id);

        if($request->expectsJson())
        {
            return response()->json($data);
        }

        return $data ? $this->load('chat', $data) : redirect('/main');
    }
}