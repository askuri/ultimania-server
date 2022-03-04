<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Record;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordReplayControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSavingAndRetrievingWorks()
    {
        // $record = Record::factory()->create();

        //$responsePut = $this->put('/api/v5/records/' . $record->id . '/replay', ['not-so-binary-replay'], ['Content-Type' => 'application/octet-stream']);
        //$responsePut->assertStatus(201);


    }
}
