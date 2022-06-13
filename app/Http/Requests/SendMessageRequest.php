<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'content' => 'required'
        ];
    }
}
