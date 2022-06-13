<?php

namespace App\Repositories;

use App\Events\RoomMemberProcessed;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\RoomController;
use App\Models\Friendship;
use App\Models\Room;
use App\Models\RoomMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomRepository
{
    private $model;

    public function __construct()
    {
        $this->model = new Room();
    }

    public function create($data): Room
    {
        $user = Auth::user();

        $room_name = !empty($data['room_name']) ? $data['room_name'] : $user->nick.'_room';

        $room = $user->adminRoom()->create([
            'room_name' => $room_name
        ]);

        return $room;
    }

    public function inviteFriends($data, Room $room): void
    {
        foreach ($data['add_friend'] as $friend_id) {
            //Check friendship
            $res = Friendship::check(Auth::id(), $friend_id);
            if (empty($res[0])) {
                continue;
            }

            $room->roomMembers()->create([
                'user_id' => $friend_id,
            ]);
        }
    }

    public function getRoomData(int $room_id): array
    {
        $friendship = new FriendshipController();
        $messages = new MessagesController();
        $UserSettingsController = new UserSettingsController();
        $RoomController = new RoomController($this);

        $tmp = $messages->get_array($room_id);

        $room = $this->model->findOrFail($room_id);
        $room_member = RoomMember::userID(Auth::id())->roomID($room_id)->first();
        if (empty($room_member->created_at) || $room_member->status !== 1) {
            return [];
        }

        $data['user_settings'] = $UserSettingsController->load_user_settings(); //Todo: I think this should be higher
        $data['friends_data'] = $friendship->get_user_friends('array'); //Todo: I think this should be higher
        $data['rooms_data'] = $RoomController->get_user_rooms('array');
        $data['room'] = $RoomController->get_room($room_id);
        $data['roommates_data'] = $RoomController->get_roommates($room);
        $data['messages'] = $tmp['messages'];
        $data['msg_users'] = $tmp['msg_users'];
        $data['files'] = $tmp['files'];
        $data['newest_msg'] = $tmp['newest_msg'];
        $data['room_id'] = $room_id;
        $data['admin_room_id'] = $room->owner->id;
        $data['img_ext'] = ['png', 'jpg', 'webp', 'gif', 'svg', 'jpeg'];
        $data['content'] = 'chat';

        RoomMemberProcessed::dispatch($room_member, $tmp['newest_msg']);

        return $data;
    }
}
