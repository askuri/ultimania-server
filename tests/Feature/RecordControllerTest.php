<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdate()
    {
        $response = $this->put('/api/v5/');

        $response->assertStatus(200);
    }
}
