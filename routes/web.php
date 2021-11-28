<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\RoomController;

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
Route::get('/register_form', [Controller::class, 'register_form']);

Route::get('/main', function(Request $request){
    $con = new App\Http\Controllers\Controller();
    $friendship = new App\Http\Controllers\FriendshipController();
    $room = new App\Http\Controllers\RoomController();
    
    $data['friends_data'] = $friendship->get_user_friends('array');
    $data['rooms_data'] = $room->get_user_rooms('array');
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

    $tmp = $messages->get_array($room_id);
    
    $data['friends_data'] = $friendship->get_user_friends('array');
    $data['rooms_data'] = $room->get_user_rooms('array');
    $data['messages'] = $tmp['messages'];
    $data['msg_users'] = $tmp['msg_users'];
    $data['files'] = $tmp['files'];
    $data['newest_msg'] = $tmp['newest_msg'];
    $data['room_id'] = $room_id;
    $data['content'] = 'chat';

    return $con->load('chat', $data);
});
Route::post('/room', function(Request $request){
    $room = new App\Http\Controllers\RoomController();
    return $room->save_room($request);
});
Route::put('/room/{room_id}', [RoomController::class, 'update_room_status']);
// Messages
Route::post('/send_msg', function(Request $request){
    $msg_con = new App\Http\Controllers\MessagesController();
    return $msg_con->send($request);  
});
Route::get('/get_msg/{room_id}', [MessagesController::class, 'get']);
Route::get('/get_newest_id/{room_id}', [MessagesController::class, 'get_newest_id']);