<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\V1\APIController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocalizationTest extends TestCase
{

    public function test_it_returns_localization_data(): void
    {
        $response = $this->json('GET', action([APIController::class, 'localization']));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'en' => [],
                'ar' => [],
            ]);
    }
}
