<?php

namespace App\Http\Requests;

class SaveRoomRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'room_name' => 'nullable',
            'add_friend' => 'required',
        ];
    }
}
