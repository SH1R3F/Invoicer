<?php

namespace Tests\Feature\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V1\Auth\ProfileController;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();
    }

    /**
     * It gets logged in user info
     */
    public function test_logged_in_user_info_endpoint(): void
    {
        // Create user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Perform login
        $response = $this->json('GET', action([ProfileController::class, 'user']));

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $user->id,
                'email' => $user->email,
                'fullName' => $user->name,
            ]);
    }

    /**
     * It requires user to be logged in
     */
    public function test_logged_in_user_info_endpoint_middleware(): void
    {
        // Perform login
        $response = $this->json('GET', action([ProfileController::class, 'user']));

        // Assert
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthenticated.'
            ]);
    }
}
