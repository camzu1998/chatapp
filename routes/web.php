<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\Proxy\RoomProxy;
use App\Models\UserRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

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

Route::get('/', [Controller::class, 'init']);
Route::get('/.well-known/acme-challenge/nY4AcXJSv_Mrjqndf9rr7N53YLNsB2lsS3IbH4yla1o', function(){
    return 'nY4AcXJSv_Mrjqndf9rr7N53YLNsB2lsS3IbH4yla1o.U_4xLF8dgJ1k8O7LJc-iDhIvoxBMmlL84C3ANwg4VEw';
});
Route::get('/register_form', [UserController::class, 'register_form']);
Route::get('/logout', [UserController::class, 'logout']);

Route::get('/main', [Controller::class, 'dashboard']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'authenticate']);
// User
Route::post('/user/set_profile/{user_id}', [UserSettingsController::class, 'set_user_profile']);
Route::post('/user/set_settings', [UserSettingsController::class, 'save_user_settings']);
// Friendship
Route::get('/friendship', [FriendshipController::class, 'get_user_friends']);
Route::post('/friendship', [FriendshipController::class, 'save_friendship']);
Route::put('/friendship/{friend_id}', [FriendshipController::class, 'update_friendship_status']);
// Room
Route::get('/room', [RoomProxy::class, 'get_user_rooms']); //JSON Response
Route::get('/room/{room_id}', [RoomProxy::class, 'load_room']);
Route::post('/room', [RoomProxy::class, 'save_room']);
Route::put('/room/{room_id}', [RoomProxy::class, 'update_room_status']);
//Room settings
Route::delete('/room/{room_id}', [RoomProxy::class, 'delete_room']);
Route::put('/room/{room_id}/update', [RoomProxy::class, 'update']);
Route::post('/room/{room_id}/invite', [RoomProxy::class, 'invite']);
//Room image
Route::post('/room/{room_id}/upload', [RoomProxy::class, 'upload_room_profile']);
Route::get('/room/{room_id}/get_image', [RoomProxy::class, 'get_room_profile']);
Route::put('/room/{room_id}/revert', [RoomProxy::class, 'revert_room_profile']);
// Messages
Route::post('/chat/message/{room_id}', [MessagesController::class, 'send']);
Route::post('/chat/file/{room_id}', [MessagesController::class, 'upload']);
Route::get('/get_msg/{room_id}', [MessagesController::class, 'get']);
Route::get('/get_newest_id/{room_id}', [MessagesController::class, 'get_newest_id']);
//Notifications
Route::get('/get_notify_data/{room_id}', [NotificationController::class, 'notify_room_message']);
Route::get('/get_notify_data', [NotificationController::class, 'check_messages']);
// Reset password
Route::post('/reset/{token}', [UserController::class, 'save_password']); //Store new pass in db 
Route::post('/reset', [UserController::class, 'remember_password']); //Sending email to user with token to reset pass
Route::get('/reset/{token}', [UserController::class, 'reset']); //Return form to set new pass
Route::get('/forgot_password', function(){
    if (Auth::check()){
        return redirect('main');
    }

    return view('remember_password');
});


Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
});

