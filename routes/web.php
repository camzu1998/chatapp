<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\PasswordController;

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
//Only Guest
Route::middleware(['only.guest'])->group(function () {
    Route::get('/', [Controller::class, 'init'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('user.login');
    Route::get('/register', [UserController::class, 'register_form'])->name('user.register');
    Route::post('/register', [UserController::class, 'register']);
    Route::get('/forgot_password', function(){
        return view('remember_password');
    })->name('user.forgot_password');
});
//Only Auth
Route::middleware('auth')->group(function ()
{
    Route::get('/main', [Controller::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [LoginController::class, 'logout']);
    Route::post('/user/set_profile/{user_id}', [UserSettingsController::class, 'set_user_profile']);
    Route::post('/user/set_settings', [UserSettingsController::class, 'save_user_settings']);

    // Room
    Route::get('/room', [RoomController::class, 'get_user_rooms']); //JSON Response
    Route::get('/room/{room_id}', [RoomController::class, 'load_room'])->middleware('room.guard');
    Route::post('/room', [RoomController::class, 'save_room'])->middleware('room.friends');
    Route::put('/room/{room_id}', [RoomController::class, 'update_room_status']);
    //Room settings
    Route::delete('/room/{room_id}', [RoomController::class, 'delete_room'])->middleware('admin.room.guard');
    Route::put('/room/{room_id}/update', [RoomController::class, 'update'])->middleware('admin.room.guard');
    Route::post('/room/{room_id}/invite', [RoomController::class, 'invite'])->middleware('admin.room.guard');
    //Room image
    Route::post('/room/{room_id}/upload', [RoomController::class, 'upload_room_profile'])->middleware('admin.room.guard');
    Route::get('/room/{room_id}/get_image', [RoomController::class, 'get_room_profile'])->middleware('admin.room.guard');
    Route::put('/room/{room_id}/revert', [RoomController::class, 'revert_room_profile'])->middleware('admin.room.guard');
});

// Friendship
Route::get('/friendship', [FriendshipController::class, 'get_user_friends']);
Route::post('/friendship', [FriendshipController::class, 'save_friendship']);
Route::put('/friendship/{friend_id}', [FriendshipController::class, 'update_friendship_status']);


// Messages
Route::post('/chat/message/{room_id}', [MessagesController::class, 'send']);
Route::post('/chat/file/{room_id}', [MessagesController::class, 'upload']);
Route::get('/get_msg/{room_id}', [MessagesController::class, 'get']);
Route::get('/get_newest_id/{room_id}', [MessagesController::class, 'get_newest_id']);
//Notifications
Route::get('/get_notify_data/{room_id}', [NotificationController::class, 'notify_room_message']);
Route::get('/get_notify_data', [NotificationController::class, 'check_messages']);
// Reset password
Route::post('/reset/{token}', [PasswordController::class, 'save_password']); //Store new pass in db 
Route::post('/reset', [PasswordController::class, 'forgot_password']); //Sending email to user with token to reset pass
Route::get('/reset/{token}', [PasswordController::class, 'reset']); //Return form to set new pass

Route::get('/.well-known/acme-challenge/nY4AcXJSv_Mrjqndf9rr7N53YLNsB2lsS3IbH4yla1o', function(){
    return 'nY4AcXJSv_Mrjqndf9rr7N53YLNsB2lsS3IbH4yla1o.U_4xLF8dgJ1k8O7LJc-iDhIvoxBMmlL84C3ANwg4VEw';
});