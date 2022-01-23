<?php

namespace Database\Factories;

use App\Models\UserRoom;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserRoomFactory extends Factory
{
    protected $model = UserRoom::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'room_id' => 0,
            'user_id' => 0,
            'status' => 0,
            'last_msg_id' => 0,
            'last_notify_id' => 0,
            'nickname' => '',
            'created_at' => now()
        ];
    }
}
