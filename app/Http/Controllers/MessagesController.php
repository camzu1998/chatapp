<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;
use App\Models\Files;
use App\Http\Controllers\FilesController;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function show(){
        if (!Auth::check()) {
            // The user is logged in...
        }

        $file_array = array();
        $msgM = new \App\Models\Messages;
        $files_model = new \App\Models\Files;

        $user = Auth::user();

        $msgs = $msgM->get();

        foreach($msgs as $k => $msg){
            if($msg->file_id != 0){
                $file_array[$msg->file_id] = $files_model->get($msg->file_id);
            }
        }

        return view('index', [
            'messages' => $msgs,
            'files'    => $file_array,
            'user'     => $user,
        ]);
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

        $msg->save($nick, $content, $file_id);

        return $this->get();
    }

    public function get(){
        $file_array = array();
        $msgM = new \App\Models\Messages;
        $files_model = new \App\Models\Files;
        
        $msgs = $msgM->get(10);

        foreach($msgs as $k => $msg){
            if($msg->file_id != 0){
                $file_array[$msg->file_id] = $files_model->get($msg->file_id);
            }
        }

        return response()->json([
            'messages' => $msgs,
            'files'    => $file_array
        ]);
    }
}
