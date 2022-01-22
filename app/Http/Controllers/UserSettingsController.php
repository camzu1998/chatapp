<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSettings;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    protected $inputs = [
        'sounds' => 0,
        'notifications' => 0,
        'press_on_enter' => 0
    ];

    public function save_user_settings(Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 1,
                'msg'    => 'Brak autoryzacji'
            ]);
        }
        $user_id = Auth::id();
        $userSettingsModel = new UserSettings();

        foreach($this->inputs as $name => $init_val){
            switch($request->input($name)){
                case 0:
                    $res = $userSettingsModel->set($user_id, $name, 0);
                    break;
                case 1:
                    $res = $userSettingsModel->set($user_id, $name, 1);
                    break;
                default:
                    $userSettingsModel->set($user_id, $name, $init_val);
            }
        }
        return response()->json([
            'status' => 0,
            'msg'    => 'Ustawienia zapisano'
        ]);
    }
    public function load_user_settings(){
        $user_id = Auth::id();
        $userSettingsModel = new UserSettings();
        
        return $userSettingsModel->get_all($user_id);
    }
    public function set_init_settings($user_id = null){
        if(!$user_id)
            return false;

        $userSettingsModel = new UserSettings();

        foreach($this->inputs as $name => $init_val){
            $userSettingsModel->add($user_id, $name, $init_val);
        }

        return true;
    }

    public function set_user_profile(Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return false;
        }

        if ($request->hasFile('input_profile')) {
            $file = $request->input_profile;

            //Check extension & weight
            if(!in_array($file->extension(), $this->profile_ext)){
                //Extension didn't pass
                return false;
            }
            if($file->getSize() > (1024 * (1024 * 25))){
                //File is oversized
                return false;
            }
            //Check if need to delete previous image
            $user_id = Auth::id();
            $userModel = new User();
            $user = $userModel->get_user_data($user_id);
            if($user->profile_img != 'no_image.jpg'){
                //Delete old profile image
                Storage::delete('profiles_miniatures/'.$user->profile_img);
            }
            //Store file
            $filename = $file->getClientOriginalName();

            $path = $file->storeAs('profiles_miniatures', $filename);
            $userModel->set_profile_image($user_id, $filename);
            //Return data
            return $filename;
        }

        return false;
    }
}
