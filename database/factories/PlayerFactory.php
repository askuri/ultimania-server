<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'login' => $this->faker->userName,
            'nick' => $this->faker->name,
            'banned' => false,
            'auto_upload_replay' => true,
        ];
    }
}
