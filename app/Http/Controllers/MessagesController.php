<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Messages;

class MessagesController extends Controller
{
    public function show(){
        $msg = new \App\Models\Messages;
        $msgs = $msg->get();

        return view('index', [
            'messages' => $msgs
        ]);
    }

    public function send(Request $request){
        $nick = $request->input('nick');
        $content = $request->input('content');

        $msg = new \App\Models\Messages;

        $msg->save($nick, $content);

        return $this->show();
    }

    public function get(){
        $msg = new \App\Models\Messages;
        $msgs = $msg->get(10);

        return response()->json([
            'messages' => $msgs
        ]);
    }
}
