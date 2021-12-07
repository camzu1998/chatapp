<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    protected $profile_ext = array('png', 'jpeg', 'jpg');

    public function save_profile_image($file){
        if (!Auth::check()) {
            // The user is not logged in...
            return redirect('/');
        }
        
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
        $userModel = new \App\Models\User();
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
}
