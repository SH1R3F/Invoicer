<?php

namespace Tests\Feature\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V1\Auth\AuthController;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Users can register successfully
     */
    public function test_user_registers_successfully(): void
    {
        $response = $this->json('POST', action([AuthController::class, 'register']), [
            'email'    => fake()->email,
            'password' => 'password123',
            'name'     => fake()->name
        ]);

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
     * Users registers is being validated
     */
    public function test_new_user_credentials_are_validated(): void
    {
        $response = $this->json('POST', action([AuthController::class, 'register']), []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['name', 'email', 'password']
            ]);
    }
}
