<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserSettingsController extends Controller
{
    public function save_user_settings(Request $request)
    {
        foreach(UserSettings::SETTINGS_TYPES as $name)
        {
            $user_setting = Auth::user()->userSettings()->name($name)->first();
            switch($request->name){
                case 0:
                    $user_setting->value = 0;
                    break;
                case 1:
                    $user_setting->value = 1;
                    break;
                default:
                    $user_setting->value = 0;
            }
            if($user_setting->isDirty())
            {
                $user_setting->save();
            }
        }

        return response()->json([
            'status' => 0,
            'msg'    => 'Ustawienia zapisano'
        ]);
    }

    public function load_user_settings()
    {
        return Auth::user()->userSettings()->get();
    }

    public function set_init_settings(User $user)
    {
        foreach(UserSettings::SETTINGS_TYPES as $name){
            DB::transaction(function () use ($user, $name) {
                $user->userSettings()->create([
                    'name'    => $name,
                    'value'   => 0,
                ]);
            }, 5);
        }

        return true;
    }

    public function set_user_profile(Request $request): string
    {
        if ($request->hasFile('input_profile'))
        {
            $file = $request->input_profile;
            //Check extension & weight
            if(!in_array($file->extension(), ['png', 'jpeg', 'jpg', 'webp'])){
                //Extension didn't pass
                Log::error('Invalid image extension: '.$file->extension().' User: '.Auth::id());
                return 'xd1 - '.$file->extension(); //Todo: Error 400 Invalid image type
            }
            if($file->getSize() > (1024 * (1024 * 25))){
                //File is oversized
                Log::error('Invalid image size: '.$file->getSize().' User: '.Auth::id());
                return 'xd2'; //Todo: Error 400 Invalid image size
            }
            //Check if user need to delete previous image
            $user = User::find(Auth::id());
            if($user->profile_img != 'no_image.jpg'){
                //Delete old profile image
                Storage::delete('profiles_miniatures/'.$user->profile_img);
            }
            //Store file
            $filename = $file->getClientOriginalName();

            $path = $request->input_profile->storeAs('profiles_miniatures',  $filename);

            $user->profile_img = $filename;
            $user->save();
            //Return data
            return $filename;
        }

        return 'xd3'; //Todo: Error 400 Invalid file name
    }
}
