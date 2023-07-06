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
            'avatar'   => 'data:image/png;base64,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... (10024 total length)'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'Profile updated successfully')
            ->assertJsonPath('userData.email', $email);
    }

    /**
     * Users can update their password
     */
    public function test_user_updates_their_password_successfully(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $response = $this->json('PUT', action([ProfileController::class, 'password']), [
            'current_password'          => 'password',
            'new_password'              => 'Password123!',
            'new_password_confirmation' => 'Password123!',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'Password updated successfully');
    }

    /**
     * User's password validation works
     *
     * @dataProvider validatePasswords
     */
    public function test_user_password_updates_are_validated(array $data, string|array $expected): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $response = $this->json('PUT', action([ProfileController::class, 'password']), $data);

        $response
            ->assertStatus(422)
            ->assertSee($expected);
    }

    public function validatePasswords(): array
    {
        return [
            // Required fields
            [[], ['The current password field is required.', 'The new password field is required.']],
            // Incorrect current password
            [['current_password' => 'wrong password'], 'The current password is incorrect.'],
            // New password validation
            [['current_password' => 'password', 'new_password' => '0'], ['The new password field confirmation does not match.', 'The new password field must be at least 8 characters.', 'The new password field format is invalid.']],
        ];
    }

    /**
     * Users can delete their accounts
     */
    public function test_user_deletes_their_account_successfully(): void
    {
        Sanctum::actingAs($user = User::factory()->create());

        $response = $this->json('POST', action([ProfileController::class, 'deactive']));

        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'User deleted successfully');

        $this->assertTrue($user->trashed());
    }
}
