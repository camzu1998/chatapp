<?php

namespace App\Http\Controllers;

use App\Repositories\RoomRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Str;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Mail;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function load($content = 'main', $data = array())
    {
        if (!Auth::check()) {
            // The user is not logged in...
            return back();
        }
        $data['user'] = Auth::user();
        $data['content'] = view($content, $data);

        return view('layout_old', $data);
    }

    public function init()
    {
        return view('login');
    }

    public function dashboard()
    {
        $friendship = new FriendshipController();
        $room = new RoomController(new RoomRepository());
        $UserSettingsController = new UserSettingsController();

        $data['friends_data'] = $friendship->get_user_friends('array');
        $data['rooms_data'] = $room->get_user_rooms();
        $data['user_settings'] = $UserSettingsController->load_user_settings();
        $data['roommates_data'] = [];
        $data['room_id'] = 0;

        return view('main', $data);
    }
}
