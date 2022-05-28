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

        return redirect('/')->with(['register' => 'success']);
    }
}
