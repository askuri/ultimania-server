<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class RecordFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->uuid(),
            'player_login' => $this->faker->userName,
            'map_uid' => $this->faker->uuid,
            'score' => 142,
        ];
    }

    public function withReplayAvailable() {
        return $this->state(function (array $attributes) {
            return [
                'replay_available' => true,
            ];
        });
    }
}
