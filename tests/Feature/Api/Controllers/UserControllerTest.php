<?php

namespace Tests\Feature\Api\Controllers;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
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
}
