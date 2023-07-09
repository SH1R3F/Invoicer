<?php

namespace Tests\Feature\Api\Controllers;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->user = User::factory()->create();
        $this->user->syncRoles(Role::where('name', 'superadmin')->first());
        Sanctum::actingAs($this->user);
    }

    public function test_it_lists_paginated_users(): void
    {
        $response = $this->json('GET', action([UserController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 10);
    }

    public function test_it_lists_filtered_users(): void
    {
        $this->user = User::factory()->create();
        $this->user->syncRoles(Role::where('name', 'client')->first());

        $response = $this->json('GET', action([UserController::class, 'index']), [
            'role' => 'superadmin'
        ]);

        $this->assertEquals(3, User::count());
        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_it_lists_searched_users(): void
    {
        $response = $this->json('GET', action([UserController::class, 'index']), [
            'q' => 'superadmin@example.test'
        ]);

        $this->assertEquals(2, User::count());
        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_lists_ordered_users(): void
    {
        $response = $this->json('GET', action([UserController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertSeeInOrder([$this->user->email, 'superadmin@example.test']);
    }

    public function test_it_deletes_user(): void
    {
        $user = User::factory()->create();
        $response = $this->json('DELETE', action([UserController::class, 'destroy'], [$user->id]));

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'User deleted successfully']);
    }

    public function test_it_doesnt_delete_superadmin_users(): void
    {
        $user = User::factory()->create();
        $user->syncRoles(Role::where('name', 'superadmin')->first());

        $this->json('DELETE', action([UserController::class, 'destroy'], [$user->id]))->assertStatus(403);
    }

    public function test_it_creates_new_user_with_role(): void
    {
        $response = $this->json('POST', action([UserController::class, 'store']), [
            "name" => "test",
            "role" => "client",
            "email" => $email = "tesst@test.test",
            "password" => "password123!"
        ]);

        $response->assertStatus(201);
        $this->assertEquals('client', User::where('email', $email)->first()->roles->first()?->name);
    }

    public function test_it_updates_user_with_role(): void
    {
        $user = User::factory()->create();

        $this->json('PUT', action([UserController::class, 'update'], [$user->id]), [
            "name" => "test",
            "role" => "client",
            "email" => $email = "tesst@test.test",
            "password" => "password123!",
            'avatar' => 'url'
        ])->assertStatus(200);

        $user->refresh();
        $this->assertEquals('client', $user->roles->first()?->name);
        $this->assertEquals('test', $user->name);
    }

    public function test_it_shows_single_user_with_resources(): void
    {
        $user = User::factory()->create();

        $response = $this->json('GET', action([UserController::class, 'show'], [$user->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'avatar', 'role'],
                'roles' => []
            ]);
    }

    public function test_it_exports_users_collection()
    {
        Excel::fake();

        $this->json('GET', action([UserController::class, 'export']))
            ->assertStatus(200)
            ->assertJsonStructure(['url']);

        Excel::assertStored('Exports/Users/Users-' . time() . '.xlsx', 'public');
    }
}
