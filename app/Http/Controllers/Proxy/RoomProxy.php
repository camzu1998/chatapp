<?php

namespace App\Http\Controllers\Proxy;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Interfaces\RoomInterface;

use App\Models\UserRoom;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RoomController;

class RoomProxy extends Controller implements RoomInterface
{
    private $roomController;

    public function __construct(RoomController $roomController)
    {
        $this->roomController = $roomController;
    }


    protected $profile_ext = array('png', 'jpeg', 'jpg');

    public function get_user_rooms(string $switch_response = 'json'): mixed
    {
        if (!Auth::check()) 
        {
            if($switch_response == 'json'){
                return response()->json([
                    'rooms_data' => []
                ]);
            }else if($switch_response == 'array'){
                return [];
            }
        }

        return $this->roomController->get_user_rooms($switch_response);
    }

    public function save_room(Request $request): mixed
    {
        if (!Auth::check()) 
        {
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        if(empty($request->add_friend))
        {
            return response()->json([
                'status' => 1,
                'msg'    => 'Please add some friends to room'
            ]);
        }
        
        return $this->roomController->save_room($request);
    }

    public function update_room_status(Request $request, int $room_id): mixed
    {
        if (!Auth::check())
        {
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
        return $this->roomController->update_room_status($request, $room_id);
    }
    /**
     * Upload room profile image
     */
    public function upload_room_profile(Request $request, int $room_id): string
    {
        if (!Auth::check())
        {
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        if(empty($room_id) || !$request->hasFile('room_profile'))
        {
            return response()->json([
                'err' => '1',
            ]);
        }

        return $this->roomController->upload_room_profile($request, $room_id);
    }
    /**
     *  Update room data
     */
    public function update(Request $request, int $room_id): mixed
    {
        if (!Auth::check()) 
        {
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
        if(empty($room_id) || empty($request->input('update_room_name')))
        {
            return response()->json([
                'status' => '1',
                'msg'    => 'Brak danych'
            ]);
        }

        return $this->roomController->update($request, $room_id);
    }
    /**
     *  Get room roommates
     */
    public function get_roommates(int $room_id): array
    {
        if (!Auth::check()) 
        {
            return [];
        }

        return $this->roomController->get_roommates($room_id);
    }
    /**
     * Delete room and connected data
     */
    public function delete_room(int $room_id): mixed
    {
        if (!Auth::check()) 
        {
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        if(empty($room_id))
        {
            return response()->json([
                'status' => 1,
                'msg'    => 'No data'
            ]);
        }

        return $this->roomController->delete_room($room_id);
    }
    /**
     * Send invites to friends
     */
    public function invite(Request $request, int $room_id): mixed
    {
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 9,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
        
        if(empty($request->add_friend))
        {
            return response()->json([
                'status' => 1,
                'msg'    => 'Please add some friends to room'
            ]);
        }
        
        return $this->roomController->invite($request, $room_id);
    }
    /**
     *  Get room data
     */
    public function get_room(int $room_id): mixed
    {        
        if (!Auth::check()) 
        {
            return [];
        }

        return $this->roomController->get_room($room_id);
    }
    /**
     * Return room view
     */
    public function load_room(Request $request, int $room_id): mixed
    {
        if (!Auth::check()) {
            // The user is not logged in...
            return redirect('/');
        }

        return $this->roomController->load_room($request, $room_id);
    }
}