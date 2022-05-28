<?php

namespace App\Http\Requests;

class CreateUserRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'nick' => 'required|unique:users,nick',
            'email' => 'required|email|unique:users,email',
            'pass' => 'required',
            'pass_2' => 'required|same:pass',
        ];
    }
}
