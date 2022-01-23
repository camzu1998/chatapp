<?php

namespace Database\Factories;

use App\Models\Room;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoomFactory extends Factory
{
    protected $model = Room::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'admin_id' => 1,
            'room_name' => $this->faker->unique()->words(2, true),
            'room_img' => 'no_image.jpg',
            'created_at' => now(),
        ];
    }
}
