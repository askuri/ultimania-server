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
            'allow_replay_download' => true,
        ];
    }

    public function banned()
    {
        return $this->state(function (array $attributes) {
            return [
                'banned' => true,
            ];
        });
    }
}
