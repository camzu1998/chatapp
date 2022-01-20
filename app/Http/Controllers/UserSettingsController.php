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

        foreach($this->inputs as $name => $init_val){
            switch($request->input($name)){
                case 0:
                    $res = $userSettingsModel->set($user_id, $name, 0);
                    if($res == 0)
                        return response()->json([
                            'status' => 1,
                            'msg'    => 'Błąd zapisu'
                        ]);
                    break;
                case 1:
                    $res = $userSettingsModel->set($user_id, $name, 1);
                    if($res == 0)
                        return response()->json([
                            'status' => 1,
                            'msg'    => 'Błąd zapisu'
                        ]);
                    break;
                default:
                    if($userSettingsModel->set($user_id, $name, $init_val) == 0)
                        return response()->json([
                            'status' => 1,
                            'msg'    => 'Błąd zapisu'
                        ]);
            }
        }
        return response()->json([
            'status' => 0,
            'msg'    => 'Ustawienia zapisano'
        ]);
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

    public function set_user_profile(Request $request){
        if ($request->hasFile('input_profile')) {
            //Run UserController store functions
            $usrCon = new \App\Http\Controllers\UserController();
            $res = $usrCon->save_profile_image($request->input_profile);
            if(!$res){
                return false;
            }

            return $res;
        }

        return false;
    }
}
