<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserSettingsController;

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

Route::get('/', [Controller::class, 'show']);
Route::get('/register_form', [Controller::class, 'register_form']);


Route::get('/chat', [MessagesController::class, 'show']);

Route::post('/register', function(Request $request){
    $con = new App\Http\Controllers\Controller();
    return $con->register($request);
});
Route::post('/login', function(Request $request){
    $con = new App\Http\Controllers\Controller();
    return $con->authenticate($request);
});
Route::post('/send_msg', function(Request $request){
    $msg_con = new App\Http\Controllers\MessagesController();
    return $msg_con->send($request);
});
Route::post('/get_msg', function(Request $request){
    $msg_con = new App\Http\Controllers\MessagesController();
    return $msg_con->get($request);
});
Route::post('/save_settings', function(Request $request){
    $user_settings = new App\Http\Controllers\UserSettingsController();
    return $user_settings->save_user_settings($request);
});
Route::post('/get_newest_id', [MessagesController::class, 'get_newest_id']);
