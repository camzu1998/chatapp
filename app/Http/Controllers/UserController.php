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

        if(empty($request->nick) || empty($request->email) || empty($request->pass))
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);

        if($request->pass != $request->pass_2)
            return back()->withErrors([
                'pass_2' => 'Paswwords do not match each other.',
            ]);
        $pass = Hash::make($request->input('pass'));
        
        $user = User::where('nick', $request->nick)->orWhere('email', $request->email)->first();
        if(!empty($user->created_at)){
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $user = User::create([
            'nick' => $request->nick,
            'email' => $request->email,
            'password' => $pass,
            'profile_img' => 'no_image.jpg',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $user_settings_controller = new UserSettingsController();
        $user_settings_controller->set_init_settings($user->id);

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

        $user = User::where('email', $request->email)->first();
        if(!empty($user->created_at)){
            $token = Str::random(40);
            $user->reset_token = $token;
            $user->save();
            if(!$user->wasChanged()){
                return back()->withErrors(['email' => 'Provided email doesnt pass xx']);
            }
            //Send email
            Mail::to($user->email)->send(new ResetPassword($token));

            return redirect('/');
        }
        return back()->withErrors(['email' => 'Provided email doesnt pass']);
    }

    public function reset(string $token){
        if(empty($token)){
            return redirect('/');
        }
        //Check token
        $user = User::where('reset_token', $token)->first();
        if(!empty($user->created_at)){
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
        //Check token
        $user = User::where('reset_token', $token)->first();
        if(!empty($user->created_at)){
            $pass = Hash::make($request->pass);
            //Store pass in db & clear token
            $user->password = $pass;
            $user->save();
            //Redirect user to login page
            return redirect('/');
        }

        return back()->withErrors(['pass' => 'An error occured']);
    }
}
