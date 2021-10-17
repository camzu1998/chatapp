<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSettings;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    public function save_user_settings(Request $request){
        $user_id = Auth::id();


    }
    public function load_user_settings(){
        $user_id = Auth::id();


    }
}
