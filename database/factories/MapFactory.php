<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class MapFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        /*
            $table->string('uid')->primary();
            $table->string('name');
            $table->timestamps();*/

        return [
            'uid' => $this->faker->uuid,
            'name' => $this->faker->word,
        ];
    }
}
