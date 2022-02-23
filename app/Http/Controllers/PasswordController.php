<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repositories\UserRepository;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\StoreNewPasswordRequest;

class PasswordController extends Controller
{
    public $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function forgot_password(ForgotPasswordRequest $request)
    {
        $data = $request->validated();

        $this->repository->create_reset_password($data);

        return redirect('/');
    }

    public function reset(ResetPasswordRequest $request, string $token)
    {
        return view('set_password', ['token' => $token]);
    }

    public function save_password(StoreNewPasswordRequest $request, string $token)
    {
        $data = $request->validated();

        $this->repository->store_new_password($data, $token);

        return redirect('/');
    }
}
