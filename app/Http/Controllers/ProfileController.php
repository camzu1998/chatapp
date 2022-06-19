<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function upload(Request $request, string $type, int $type_id): string
    {
        $user = Auth::user();
        $file = $request->input_profile;

        if ($type == 'user') {
            $typeModel = $user;
        } else if ($type == 'room') {
            $typeModel = $user->adminRoom()->find($type_id);
        }

        $actual_image = $typeModel->profile_img;
        $filename = $file->getClientOriginalName();
        $typeModel->profile_img = $filename;
        
        //Check if need to delete previous image
        if ( $typeModel->isDirty() ) {
            if ( $actual_image != 'no_image.jpg' ) {
                //Delete old profile image
                Storage::delete($typeModel::PROFILE_PATH.'/'.$actual_image);
            }
            //Store image
            $path = $file->storeAs($typeModel::PROFILE_PATH, $filename);
            //Change img in db
            $typeModel->save();
        }

        return $path;
    }

    public function get(Request $request, string $type, int $type_id)
    {
        $user = Auth::user();
        if ($type == 'user') {
            $typeModel = $user;
        } else if ($type == 'room') {
            $typeModel = $user->adminRoom()->find($type_id);
        }

        $path = './storage/'.$typeModel::PROFILE_PATH .'/'. $typeModel->profile_img;

        return response()->file($path, [
            'Content-Disposition' => 'inline',
            'filename' => $typeModel->profile_img
        ]);
    }
}
