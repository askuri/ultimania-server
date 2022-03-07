<?php

namespace Tests;

use App\Models\Map;
use App\Models\Player;
use App\Models\Record;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestData {
    public const VALID_REPLAY_CONTENT = 'GBX_some_content';
    public const INVALID_REPLAY_CONTENT = 'invalid_content';

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
