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
            'avatar'   => 'image_url'
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
