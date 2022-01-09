<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserSettings;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    protected $inputs = [
        'sounds' => 0,
        'notifications' => 1,
        'press_on_enter' => 0
    ];

    public function save_user_settings(Request $request){
        $user_id = Auth::id();
        $userSettingsModel = new \App\Models\UserSettings();

        //Check for profile upload
        if ($request->hasFile('input_profile')) {
            //Run UserController store functions
            $usrCon = new \App\Http\Controllers\UserController();
            $usrCon->save_profile_image($request->input_profile);
        }

        foreach($this->inputs as $name => $init_val){
            switch($request->input($name)){
                case 0:
                    $userSettingsModel->set($user_id, $name, 0);
                    break;
                case 1:
                    $userSettingsModel->set($user_id, $name, 1);
                    break;
                default:
                    $userSettingsModel->set($user_id, $name, $init_val);
            }
        }
        return true;
    }
    public function load_user_settings(){
        $user_id = Auth::id();
        $userSettingsModel = new \App\Models\UserSettings();
        
        return $userSettingsModel->get_all($user_id);
    }
    public function set_init_settings($user_id = null){
        if(!$user_id)
            return false;

        $userSettingsModel = new \App\Models\UserSettings();

        foreach($this->inputs as $name => $init_val){
            $userSettingsModel->add($user_id, $name, $init_val);
        }

        return true;
    }
}
