<?php

namespace Tests;

use App\Models\Map;
use App\Models\Player;
use App\Models\Record;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestData {
    public const REPLAY_CONTENT = 'some_content';

    /**
     * Generate a record and associate a player and a map with it.
     * Call ->create() to persist it in DB or ->make() to simply get the Record object.
     * In either case the database must be initialized because player and map need to be saved.
     */
    public static function record(): Factory {
        return Record::factory()
            ->for(Player::factory())
            ->for(Map::factory());
    }
}
