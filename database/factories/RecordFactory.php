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
        /*
        $table->string('id')->primary();
        $table->string('player_login');
        $table->string('map_uid');
        $table->integer('score', false, true);
        $table->timestamps();*/

        return [
            'id' => $this->faker->uuid(),
            'player_login' => $this->faker->userName,
            'map_uid' => $this->faker->uuid,
            'score' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
