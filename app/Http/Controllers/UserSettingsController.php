<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSettings;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserSettingsController extends Controller
{
    protected $inputs = [
        'sounds' => 0,
        'notifications' => 0,
        'press_on_enter' => 0
    ];
    protected $profile_ext = array('png', 'jpeg', 'jpg');
    

    public function save_user_settings(Request $request){
        if (!Auth::check()) {
            // The user is not logged in...
            return response()->json([
                'status' => 1,
                'msg'    => 'Brak autoryzacji'
            ]);
        }

        foreach($this->inputs as $name => $init_val){
            $user_setting = UserSettings::User(Auth::id())->Name($name)->first();
            switch($request->$name){
                case 0:
                    $user_setting->value = 0;
                    break;
                case 1:
                    $user_setting->value = 0;
                    break;
                default:
                    $user_setting->value = $init_val;
            }
            if($user_setting->isDirty())
                $user_setting->save();
        }
        return response()->json([
            'status' => 0,
            'msg'    => 'Ustawienia zapisano'
        ]);
    }
    public function load_user_settings(){
        return UserSettings::User(Auth::id())->get();
    }
    public function set_init_settings(int $user_id){

        foreach($this->inputs as $name => $init_val){
            UserSettings::factory()->create([
                'user_id' => $user_id,
                'name'    => $name,
                'value'   => $init_val,
            ]);
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
            $user = User::find(Auth::id());
            if($user->profile_img != 'no_image.jpg'){
                //Delete old profile image
                Storage::delete('profiles_miniatures/'.$user->profile_img);
            }
            //Store file
            $filename = $file->getClientOriginalName();

            $path = $file->storeAs('profiles_miniatures', $filename);
            $user->profile_img = $filename;
            $user->save();
            //Return data
            return $filename;
        }

        return false;
    }
}
