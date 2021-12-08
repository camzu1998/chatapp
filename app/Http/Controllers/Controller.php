<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\UserSettingsController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function login_form(){
        if (Auth::check()) {
            // The user is not logged in...
            return redirect('/main');
        }

        return view('login');
    }
    public function register_form(){
        return view('register_form');
    }
    public function load($content = 'main' ,$data = array()){
        if (!Auth::check()) {
            // The user is not logged in...
            return back();
        }
        $data['user'] = Auth::user();
        $data['content'] = view($content, $data);

        return view('layout', $data);
    }
    public function register(Request $request){
        if(empty($request->input('nick')) || empty($request->input('email')) || empty($request->input('pass')))
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);

        if($request->input('pass') != $request->input('pass_2'))
            return back()->withErrors([
                'pass_2' => 'Paswwords do not match each other.',
            ]);

        $pass = Hash::make($request->input('pass'));
        $userModel = new User();

        $tmp = $userModel->check_names($request->input('nick'), $request->input('email'));
        if(!empty($tmp)){
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $user_id = $userModel->save_user($request->input('nick'), $request->input('email'), $pass);

        $user_settings_controller = new UserSettingsController();
        $user_settings_controller->set_init_settings($user_id);

        return redirect('/');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect('main');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
