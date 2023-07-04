<?php

namespace Tests\Feature\Api\Auth;

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Users can login successfully
     */
    public function test_user_logs_in_successfully(): void
    {
        // Create user
        $user = User::factory()->create();

        // Perform login
        $response = $this->json('POST', action([AuthController::class, 'login']), [
            'email' => $user->email,
            'password' => 'password'
        ]);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'accessToken',
                'userData' => [
                    'id',
                    'fullName',
                ],
                'userAbilities' => []
            ]);
    }

    /**
     * Users login is being validated
     */
    public function test_user_credentials_are_validated(): void
    {
        // Perform login with no credentials
        $response = $this->json('POST', action([AuthController::class, 'login']), []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email', 'password']
            ]);
    }
}
