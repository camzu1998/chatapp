<?php

namespace App\Http\Requests;

use App\Models\User;

class StoreNewPasswordRequest extends AbstractRequest
{
    public function authorize(): bool
    {
        return (bool)User::where('reset_token', $this->route('token'))->first();
    }

    public function rules(): array
    {
        return [
            'pass' => 'required',
            'pass2' => 'required|same:pass',
        ];
    }
}
