<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Messages;

class MessagesFactory extends Factory
{
    protected $model = Messages::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'room_id'=> 1,
            'file_id' => 0,
            'content' => $this->faker->words(5, true),
            'created_at' => now()
        ];
    }
}
