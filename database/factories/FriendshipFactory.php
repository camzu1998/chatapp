<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Friendship;

class FriendshipFactory extends Factory
{
    protected $model = Friendship::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => false,
            'user2_id' => false,
            'status' => 0,
            'by_who' => false,
            'created_at' =>  now()
        ];
    }
}
