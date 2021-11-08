<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/send_msg', function(Request $request){
    $msg_con = new App\Http\Controllers\MessagesController();
    return $msg_con->save($request);
});
Route::put('/friendship/{friend_id}', [FriendshipController::class, 'update_friendship_status']);