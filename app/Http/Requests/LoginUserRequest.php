<?php

namespace App\Http\Requests;

class LoginUserRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required',
        ];
    }
}
