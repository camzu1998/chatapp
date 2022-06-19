<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginUserRequest;
use App\Services\Auth\AuthInterface;
use App\Services\Auth\FacebookService;
use App\Services\Auth\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController
{
    public function authenticate(LoginUserRequest $request)
    {
        $data = $request->validated();

        if (Auth::attempt($data)) {
            $request->session()->regenerate();

            return redirect('/main');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function socialLogin(string $social, Request $request)
    {
        return Socialite::driver($social)->redirect();
    }

    public function socialCallback(string $social)
    {
        $socialUser = Socialite::driver($social)->user();

        $service = $this->chooseProvider($social);
        $user = $service->findUser($socialUser);

        if($user == null){
            $user = $service->createOrUpdate($socialUser);
        }
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function chooseProvider(string $providerName): AuthInterface
    {
        switch ($providerName) {
            case 'google':
                return new GoogleService();
                break;
            case 'facebook':
                return new FacebookService();
                break;
        }
    }
}
