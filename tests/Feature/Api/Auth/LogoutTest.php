<?php

namespace Tests\Feature\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V1\Auth\AuthController;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Users can login successfully
     */
    public function test_user_logs_out_successfully(): void
    {
        Sanctum::actingAs(User::factory()->create());

        // Perform login
        $response = $this->json('POST', action([AuthController::class, 'logout']));

        // Assert
        $response->assertStatus(204);
    }
}
