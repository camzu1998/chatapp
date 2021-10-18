<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\Files;
use App\Models\User;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function show(){
        if (!Auth::check()) {
            // The user is not logged in...
        }

        $users_array = array();
        $file_array = array();
        $msgM = new \App\Models\Messages;
        $files_model = new \App\Models\Files;
        $user_model = new \App\Models\User;

        $user = Auth::user();

        $msgs = $msgM->get();

        foreach($msgs as $k => $msg){
            //Check file data
            if($msg->file_id != 0){
                $file_array[$msg->file_id] = $files_model->get($msg->file_id);
            }
            //Check user data
            $msg_user = $user_model->get_user_data($msg->user_id);
            $users_array[$msg->user_id] = [
                'nick' => $msg_user->nick,
                'profile_img' => $msg_user->profile_img
            ];
        }

        return view('index', [
            'messages'   => $msgs,
            'msg_users'  => $users_array,
            'newest_msg' => $this->get_newest_id(),
            'files'      => $file_array,
            'user'       => $user,
        ]);
    }

    public function get_newest_id(){
        $msgM = new \App\Models\Messages;
        $msgs = $msgM->get_last();
        if(empty($msgs->id)){
            return 0;
        }
        return $msgs->id;
    }

    public function send(Request $request){
        $nick = $request->input('nick');
        $content = $request->input('content');
        $file_id = 0;

        if(!empty($request->file('file'))){
            $files_con = new FilesController();
            $file_id = $files_con->save($request);
        }

        $msg = new \App\Models\Messages;

        $msg->save($nick, $content, $file_id, Auth::id());

        return $this->get();
    }

    public function get(){
        $users_array = array();
        $file_array = array();
        
        $msgM = new \App\Models\Messages;
        $files_model = new \App\Models\Files;
        $user_model = new \App\Models\User;
        
        $msgs = $msgM->get(10);

        foreach($msgs as $k => $msg){
            //Check file data
            if($msg->file_id != 0){
                $file_array[$msg->file_id] = $files_model->get($msg->file_id);
            }
            //Check user data
            $msg_user = $user_model->get_user_data($msg->user_id);
            $users_array[$msg->user_id] = [
                'nick' => $msg_user->nick,
                'profile_img' => $msg_user->profile_img
            ];
        }

        return response()->json([
            'messages'   => $msgs,
            'msg_users'  => $users_array,
            'newest_msg' => $this->get_newest_id(),
            'files'      => $file_array,
        ]);
    }
}
