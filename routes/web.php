<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\RoomController;
use App\Models\UserRoom;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Controller::class, 'login_form']);
Route::get('/.well-known/acme-challenge/nY4AcXJSv_Mrjqndf9rr7N53YLNsB2lsS3IbH4yla1o', function(){
    return 'nY4AcXJSv_Mrjqndf9rr7N53YLNsB2lsS3IbH4yla1o.U_4xLF8dgJ1k8O7LJc-iDhIvoxBMmlL84C3ANwg4VEw';
});
Route::get('/register_form', [Controller::class, 'register_form']);
Route::get('/logout', [Controller::class, 'logout']);

Route::get('/main', function(Request $request){
    $con = new App\Http\Controllers\Controller();
    $friendship = new App\Http\Controllers\FriendshipController();
    $room = new App\Http\Controllers\RoomController();
    $UserSettingsController = new App\Http\Controllers\UserSettingsController();
    
    $data['friends_data'] = $friendship->get_user_friends('array');
    $data['rooms_data'] = $room->get_user_rooms('array');
    $data['user_settings'] = $UserSettingsController->load_user_settings();
    $data['roommates_data'] = [];
    $data['room_id'] = 0;
    $data['content'] = 'main';

    return $con->load('main', $data);
});

Route::post('/register', function(Request $request){
    $con = new App\Http\Controllers\Controller();
    return $con->register($request);
});
Route::post('/login', function(Request $request){
    $con = new App\Http\Controllers\Controller();
    return $con->authenticate($request);
});
Route::post('/save_settings', function(Request $request){
    $user_settings = new App\Http\Controllers\UserSettingsController();
    return $user_settings->save_user_settings($request);
});
// Friendship
Route::get('/friendship', [FriendshipController::class, 'get_user_friends']);
Route::post('/friendship', function(Request $request){
    $friendship = new App\Http\Controllers\FriendshipController();
    return $friendship->save_friendship($request);
});
Route::put('/friendship/{friend_id}', [FriendshipController::class, 'update_friendship_status']);
// Room
Route::get('/room', [RoomController::class, 'get_user_rooms']);
Route::get('/room/{room_id}', function($room_id, Request $request){
    $con = new App\Http\Controllers\Controller();
    $friendship = new App\Http\Controllers\FriendshipController();
    $room = new App\Http\Controllers\RoomController();
    $messages = new App\Http\Controllers\MessagesController();
    $UserSettingsController = new App\Http\Controllers\UserSettingsController();
    $UserRoomModel = new UserRoom();

    $tmp = $messages->get_array($room_id);
    if($tmp == false){
        return redirect('/main');
    }

    
    $data['user_settings'] = $UserSettingsController->load_user_settings();
    $data['friends_data'] = $friendship->get_user_friends('array');
    $data['rooms_data'] = $room->get_user_rooms('array');
    $data['room'] = $room->get_room($room_id);
    $data['roommates_data'] = $room->get_roommates($room_id);
    $data['messages'] = $tmp['messages'];
    $data['msg_users'] = $tmp['msg_users'];
    $data['files'] = $tmp['files'];
    $data['newest_msg'] = $tmp['newest_msg'];
    $data['room_id'] = $room_id;
    $data['admin_room_id'] = $room_id;
    $data['img_ext'] = ['png', 'jpg', 'webp', 'gif', 'svg', 'jpeg'];
    $data['content'] = 'chat';

    $UserRoomModel->set_user_msg($room_id, Auth::id(), $tmp['newest_msg']);

    return $con->load('chat', $data);
});
Route::post('/room', function(Request $request){
    $room = new App\Http\Controllers\RoomController();
    return $room->save_room($request);
});
Route::put('/room/{room_id}', [RoomController::class, 'update_room_status']);
//Room settings
Route::delete('/room/{room_id}', [RoomController::class, 'delete_room']);
Route::put('/room/{room_id}/update', [RoomController::class, 'update']);
Route::post('/room/{room_id}/invite', [RoomController::class, 'invite']);
//Room image
Route::post('/room/{room_id}/upload', [RoomController::class, 'upload_room_profile']);
Route::get('/room/{room_id}/get_image', [RoomController::class, 'get_room_profile']);
Route::put('/room/{room_id}/revert', [RoomController::class, 'revert_room_profile']);
// Messages
Route::post('/chat/message/{room_id}', [MessagesController::class, 'send']);
Route::post('/chat/file/{room_id}', [MessagesController::class, 'upload']);
Route::get('/get_msg/{room_id}', [MessagesController::class, 'get']);
Route::get('/get_newest_id/{room_id}', [MessagesController::class, 'get_newest_id']);

