<?php

namespace Tests\Feature\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V1\Auth\ProfileController;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Users can update their profile successfully
     */
    public function test_user_updates_their_profile_successfully(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $response = $this->json('PUT', action([ProfileController::class, 'profile']), [
            'name'     => fake()->name,
            'email'    => $email = fake()->email,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('userData.email', $email);
    }
}
