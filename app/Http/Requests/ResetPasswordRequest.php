<?php

namespace App\Http\Requests;

use App\Models\User;

class ResetPasswordRequest extends AbstractRequest
{
    public function authorize(): bool
    {
        return (bool)User::where('reset_token', $this->route('token'))->first();
    }
}
