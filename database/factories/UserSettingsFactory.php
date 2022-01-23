<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\UserSettings;

class UserSettingsFactory extends Factory
{
    protected $model = UserSettings::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'name' => $this->faker->words(1, true),
            'value' => 0,
            'created_at' => now()
        ];
    }
}
