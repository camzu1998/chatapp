<?php

namespace App\Http\Requests;

class ForgotPasswordRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email'
        ];
    }
}
