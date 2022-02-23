<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;

use App\Repositories\UserRepository;
class UserController extends Controller
{
    public $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function register_form()
    {
        return view('register_form');
    }

    public function register(CreateUserRequest $request){
        $data = $request->validated();
        
        $this->repository->create($data);

        return redirect('/');
    }

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

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
