<?php

namespace App\Http\Controllers;

use App\Repositories\RoomRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Friendship;
use App\Models\Room;
use App\Models\User;
use App\Models\Messages;
use App\Models\RoomMember;

use App\Http\Requests\SaveRoomRequest;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public $repository;

    public function __construct(RoomRepository $repository)
    {
        $this->repository = $repository;
    }

    public function get_user_rooms(): array
    {
        $user = Auth::user();
        $rooms_data = [];

        $user_rooms = $user
            ->roomMember()
            ->with('room')
            ->orderBy('status', 'asc')
            ->get();
        if(empty($user_rooms[0]))
        {
            //User has no rooms
            return [];
        }

        foreach($user_rooms as $user_room)
        {
            //Get info about room & save to array
            $room = $user_room->room;
            $rooms_data[$room->id] = $room;

            $admin_data = $room->owner;
            $rooms_data[$room->id]->admin_img = $admin_data->profile_img;

            $rooms_data[$room->id]->status = $user_room->status;
            $rooms_data[$room->id]->nickname = $user_room->nickname;
            $rooms_data[$room->id]->unreaded = 0;

            //Unread messages
            if($user_room->status == 1)
            {
                $res = Messages::get_difference($user_room->room_id, $user_room->last_msg_id);
                $rooms_data[$user_room->room_id]->unreaded = $res->unreaded;

                $last_msg = $room->messages()->latest()->first();
                if(!empty($last_msg))
                {
                    $rooms_data[$room->id]->last_msg_user = __('app.you');
                    $rooms_data[$room->id]->last_msg_user_img = $user->profile_img;

                    if($last_msg->user_id != Auth::id())
                    {
                        $last_user_data = $last_msg->user;
                        $rooms_data[$user_room->room_id]->last_msg_user = $last_user_data->nick;
                        $rooms_data[$user_room->room_id]->last_msg_user_img = $last_user_data->profile_img;
                    }

                    $content = Str::limit($last_msg->content, 20);
                    if($last_msg->file_id != 0){
                        $content = __('app.send_attachment');
                    }

                    $rooms_data[$user_room->room_id]->last_msg_content = $content;
                }
                else
                {
                    $rooms_data[$room->id]->last_msg_user = __('app.empty');
                    $rooms_data[$room->id]->last_msg_user_img = $room->room_img;
                    $rooms_data[$user_room->room_id]->last_msg_content = __('app.send_first_msg');
                }
            }
        }

        return $rooms_data;
    }

    public function save_room(SaveRoomRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $room = $this->repository->create($data);

        $this->repository->inviteFriends($data, $room);

        return response()->json([
            'status' => 0,
            'msg'    => __('app.saved').' '.__('app.room')
        ]);
    }

    public function update_room_status(Request $request, int $room_id): \Illuminate\Http\JsonResponse
    {
        $deleted = false;

        //check if request button is properly
        $operation = $request->button;
        if(!in_array($operation, RoomMember::ROOM_MEMBER_OPERATIONS, true))
        {
            return response()->json([
                'status' => 2,
                'msg' => __('auth.invalid_operation')
            ]);
        }

        $room_member = Auth::user()->roomMember()->with('room')->where('room_id', $room_id)->first();
        if(empty($room_member->created_at))
        {
            return response()->json([
                'status' => 2,
                'msg' => __('auth.no_auth')
            ]);
        }

        switch($operation){
            case 'acceptInvite':
                if($room_member->status == 0)
                {
                    $room_member->status = 1;
                }
                break;
            case 'blockRoom':
                $room_member->status = 2; //Todo: unlock/unblock room
                break;
            case 'declineInvite':
            case 'outRoom':
                $room_member->delete();
                $deleted = true;
                break;
            case 'deleteRoom':
                return $this->delete_room($room_id);
                break;
        }

        if(!$deleted && $room_member->isDirty())
        {
            $room_member->save();
        }

        return response()->json([
            'status' => 0,
            'msg'    => __('app.success')
        ]);
    }

    /**
     * Todo: move to UploadProfile class or something [Models/User, Models/Room]
     * Upload room profile image
     */
    public function upload_room_profile(Request $request, int $room_id): string
    {
        $file = $request->room_profile;
        $filename = $file->getClientOriginalName();

        //Compare user and admin ids
        $room = Auth::user()->adminRoom()->where('id', $room_id)->first();
        if(empty($room->created_at))
        {
            return response()->json([
                'status' => 2,
                'msg'    => __('auth.no_auth')
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
    public function update(Request $request, int $room_id): \Illuminate\Http\JsonResponse
    {
        $room = Auth::user()->adminRoom()->where('id', $room_id)->first();
        if(empty($room->created_at))
        {
            return response()->json([
                'status' => 2,
                'msg'    => __('auth.no_auth')
            ]);
        }
        //Delete retrieved roommates
        //Todo: this code needs to be rethought
        if(!empty($request->roommate))
        {
            foreach($request->roommate as $roommate)
            {
                $room->roomMembers()->where('user_id', $roommate)->delete();
            }
        }
        //Update name
        $room->room_name = $request->update_room_name;
        if($room->isDirty()){
            $room->save();
        }

        return response()->json([
            'status' => 0,
            'msg'    => __('app.success')
        ]);
    }
    /**
     *  Get room roommates
     */
    public function get_roommates(Room $room): array
    {
        $roommates_data = [];

        $roommates = $room->roomMembers()->where('user_id', '!=', Auth::id())->get();
        foreach($roommates as $roommate)
        {
            //Retrieve user data
            $user = $roommate->user;
            $roommates_data[$user->id]['nick'] = $user->nick;
            $roommates_data[$user->id]['status'] = $roommate->status;
            $roommates_data[$user->id]['room_id'] = $roommate->room_id;
            $roommates_data[$user->id]['nickname'] = $roommate->nickname;
            $roommates_data[$user->id]['profile_img'] = $user->profile_img;
        }

        return $roommates_data;
    }

    /**
     * Delete room and connected data
     */
    public function delete_room(int $room_id): \Illuminate\Http\JsonResponse
    {
        //Get room model
        $room = Auth::user()->adminRoom()->where('id', $room_id)->first();
        if(empty($room->created_at))
        {
            return response()->json([
                'status' => 2,
                'msg'    => __('auth.no_auth')
            ]);
        }

        if($room->room_img != 'no_image.jpg')
        {
            Storage::delete('room_miniatures/' . $room->room_img);
        }
        //Delete room
        try
        {
            $room->delete();
        }
        catch(\Exception $e)
        {
            return response()->json([
                'status' => 3,
                'msg'    => __('app.error')
            ]);
        }

        return response()->json([
            'status' => 0,
            'msg'    => __('app.success')
        ]);

    }

    /**
     * Send invites to friends
     */
    public function invite(Request $request, int $room_id): \Illuminate\Http\JsonResponse
    {
        $room = Auth::user()->adminRoom()->where('id', $room_id)->first();
        if(empty($room->created_at))
        {
            return response()->json([
                'status' => 2,
                'msg'    => __('auth.no_auth')
            ]);
        }
        $data = ['add_friend' => $request->add_friend];
        $this->repository->inviteFriends($data, $room);

        return response()->json([
            'status' => 0,
            'msg'    =>  __('app.success')
        ]);
    }

    /**
     *  Get room data
     */
    public function get_room(int $room_id): RoomMember|bool //Todo: name is invalid
    {        
        //Check if user is in room
        $room_member = Auth::user()->roomMember()->where('room_id', $room_id)->where('status', 1)->first();
        if(empty($room_member->created_at)){
            return false;
        }
        return $room_member;
    }
    /**
     * Return room view
     */
    public function load_room(Request $request, int $room_id): mixed
    {
        $data = $this->repository->getRoomData($room_id);

        if($request->expectsJson())//Todo: post middleware to convert to json(?)
        {
            return response()->json($data);
        }

        return $data ? $this->load('chat', $data) : redirect('/main');
    }
}