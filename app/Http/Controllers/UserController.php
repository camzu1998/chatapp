<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\UserSettingsController;
use App\Models\User;
use App\Mail\ResetPassword;

class UserController extends Controller
{
    protected $profile_ext = array('png', 'jpeg', 'jpg');

    public function register_form(){
        if (Auth::check()) {
            return redirect('/main');
        }

        return view('register_form');
    }

    public function register(Request $request){
        if (Auth::check()) {
            return redirect('/main');
        }

        if(empty($request->input('nick')) || empty($request->input('email')) || empty($request->input('pass')))
            return redirect('/register')->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);

        if($request->input('pass') != $request->input('pass_2'))
            return redirect('/register')->withErrors([
                'pass_2' => 'Paswwords do not match each other.',
            ]);

        $pass = Hash::make($request->input('pass'));
        $userModel = new User();

        $tmp = $userModel->check_names($request->input('nick'), $request->input('email'));
        if($tmp->duplicated != 0){
            return redirect('/register')->withErrors([
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
        if(Auth::check()) {
            return redirect('/main');
        }
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
        if (!Auth::check()) {
            return redirect('/');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function remember_password(Request $request){
        if(empty($request->email)){
            return back()->withErrors(['email' => 'Provided email doesnt pass x']);
        }

        $userModel = new User();
        $res = $userModel->check_email($request->email);
        if($res != null){
            $token = Str::random(40);
            $affected = $userModel->set_token($res->id, $token);
            if($affected != 1){
                return back()->withErrors(['email' => 'Provided email doesnt pass xx']);
            }
            //Send email
            Mail::to($res->email)->send(new ResetPassword($token));

            return redirect('/');
        }
        return back()->withErrors(['email' => 'Provided email doesnt pass']);
    }

    public function reset(string $token){
        if(empty($token)){
            return redirect('/');
        }
        $userModel = new User();
        //Check token
        $res = $userModel->check_token($token);
        if($res != null && $res != false){
            return view('set_password', ['token' => $token]);
        }

        return redirect('/');
    }

    public function save_password(string $token, Request $request){
        if(empty($request->pass) || empty($request->pass2)){
            return back()->withErrors(['pass' => 'Provided password doesnt pass']);
        }
        if($request->pass != $request->pass2){
            return back()->withErrors(['pass' => 'Provided passwords doesnt match']);
        }
        $userModel = new User();
        //Check token
        $res = $userModel->check_token($token);
        if($res != null && $res != false){
            $pass = Hash::make($request->pass);
            //Store pass in db & clear token
            $userModel->update_pass($pass, $token, $res->id);
            //Redirect user to login page
            return redirect('/');
        }

        return back()->withErrors(['pass' => 'An error occured']);
    }
}
