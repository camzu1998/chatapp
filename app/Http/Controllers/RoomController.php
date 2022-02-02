<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Friendship;
use App\Models\Room;
use App\Models\User;
use App\Models\Messages;
use App\Models\UserRoom;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\UserSettingsController;

class RoomController extends Controller
{
    protected $profile_ext = array('png', 'jpeg', 'jpg');

    public function get_user_rooms($switch_response = 'json'){
        $user_id = Auth::id();
        $rooms_data = array();

        $user_rooms = UserRoom::where('user_id', '=', $user_id)->orderBy('status', 'asc')->get();
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
            $rooms_data[$user_room->room_id] = Room::find($user_room->room_id);
            $user_data = User::find($rooms_data[$user_room->room_id]->admin_id);
            $rooms_data[$user_room->room_id]->admin_img = $user_data->profile_img;
            $rooms_data[$user_room->room_id]->status = $user_room->status;
            $rooms_data[$user_room->room_id]->nickname = $user_room->nickname;
            $rooms_data[$user_room->room_id]->unreaded = 0;
            //Unread messages
            if($user_room->status == 1){
                $res = Messages::get_difference($user_room->room_id, $user_room->last_msg_id);
                $rooms_data[$user_room->room_id]->unreaded = $res->unreaded;
                $last_msg = Messages::Room($user_room->room_id)->latest()->first();
                $rooms_data[$user_room->room_id]->last_msg_user = "Ty";
                $rooms_data[$user_room->room_id]->last_msg_user_img = $user_data->profile_img;
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

        if($switch_response == 'json'){
            return response()->json([
                'rooms_data' => $rooms_data
            ]);
        }else if($switch_response == 'array'){
            return $rooms_data;
        }
    }

    public function save_room(Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        if(empty($request->add_friend)){
            return response()->json([
                'status' => 1,
                'msg'    => 'Please add some friends to room'
            ]);
        }

        $user = Auth::user();

        $room_name = $request->room_name;
        if(empty($room_name)){
            $room_name = $user->nick.'_room';
        }

        $room = Room::factory()->create([
            'admin_id' => Auth::id(),
            'room_name' => $room_name
        ]);
        UserRoom::factory()->create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
            'status'  => 1
        ]);

        foreach($request->add_friend as $friend_id){
            //Check friendship
            $res = Friendship::check(Auth::id(), $friend_id);
            if(empty($res[0])){
                continue; 
            }
            UserRoom::factory()->create([
                'room_id' => $room->id,
                'user_id' => $friend_id,
            ]);
        }

        return response()->json([
            'status' => 0,
            'msg'    => 'Saved room'
        ]);
    }

    public function update_room_status(Request $request, int $room_id){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        //Valid action
        $actions = array('acceptInvite', 'decelineInvite', 'outRoom', 'blockRoom', 'deleteRoom');
        if(!in_array($request->button, $actions)){
            return response()->json([
                'status' => 1,
                'msg' => 'Niedozowolona akcja'
            ]);
        }

        $user_id = Auth::id();
        //Valid status
        $user_room = UserRoom::where('user_id', $user_id)->where('room_id', $room_id)->first();
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
            case 'decelineInvite':
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
    public function upload_room_profile(int $room_id, Request $request){
        if(empty($room_id) || !$request->hasFile('room_profile'))
            return response()->json([
                'err' => '1',
            ]);

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
    public function update(Request $request, int $room_id){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        if(empty($room_id) || empty($request->input('update_room_name')))
            return response()->json([
                'status' => '1',
                'msg'    => 'Brak danych'
            ]);

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
                UserRoom::where('room_id', $room_id)->where('user_id', $roommate)->delete();
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
    public function get_roommates(int $room_id){
        $roommates_data = array();
        if(empty($room_id))
            return false;

        $roommates = UserRoom::where('room_id', $room_id)->where('user_id', '!=', Auth::id())->get();
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
    public function delete_room(int $room_id){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        if(empty($room_id))
            return response()->json([
                'status' => 1,
                'msg'    => 'No data'
            ]);
        
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
    public function invite(int $room_id, Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
        if(empty($request->add_friend)){
            return response()->json([
                'status' => 1,
                'msg'    => 'Please add some friends to room'
            ]);
        }

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
            UserRoom::factory()->create([
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
    public function get_room(int $room_id){        
        //Check if user is in room
        $room = UserRoom::where('user_id', Auth::id())->where('room_id', $room_id)->where('status', 1)->first();
        if(empty($room->created_at)){
            return false;
        }
        return $room;
    }
    /**
     * Return room view
     */
    public function load_room(int $room_id, Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return redirect('/');
        }

        $friendship = new FriendshipController();
        $messages = new MessagesController();
        $UserSettingsController = new UserSettingsController();
        $UserRoomModel = new UserRoom();

        $tmp = $messages->get_array($room_id);
        if($tmp == false){
            return redirect('/main');
        }
        
        $data['user_settings'] = $UserSettingsController->load_user_settings();
        $data['friends_data'] = $friendship->get_user_friends('array');
        $data['rooms_data'] = $this->get_user_rooms('array');
        $data['room'] = $this->get_room($room_id);
        $data['roommates_data'] = $this->get_roommates($room_id);
        $data['messages'] = $tmp['messages'];
        $data['msg_users'] = $tmp['msg_users'];
        $data['files'] = $tmp['files'];
        $data['newest_msg'] = $tmp['newest_msg'];
        $data['room_id'] = $room_id;
        $data['admin_room_id'] = $room_id;
        $data['img_ext'] = ['png', 'jpg', 'webp', 'gif', 'svg', 'jpeg'];
        $data['content'] = 'chat';

        UserRoom::Room($room_id)->User(Auth::id())->update(['last_msg_id' => $tmp['newest_msg']]);

        return $this->load('chat', $data);
    }
}