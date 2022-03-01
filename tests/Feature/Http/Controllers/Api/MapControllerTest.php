<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Map;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testMapStoredIfNotExist()
    {
        $map = Map::factory()->make();
        $mapToSubmit = [
            'uid' => $map->uid,
            'name' => $map->name,
        ];

        $response = $this->put('/api/v5/maps', $mapToSubmit);

        $response->assertStatus(201);
        $response->assertJson($mapToSubmit);
    }

    public function testMapUpdatedIfExists()
    {
        $map = Map::factory()->create();
        $mapToSubmit = [
            'uid' => $map->uid,
            'name' => $map->name,
        ];

        $response = $this->put('/api/v5/maps', $mapToSubmit);

        $response->assertStatus(200);
        $response->assertJson($mapToSubmit);
    }
}
