<?php

namespace Tests\Feature\Api;

use App\Http\Controllers\Api\V1\RoleController;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_roles(): void
    {
        $this->seed(AuthorizationSeeder::class);
        Sanctum::actingAs(User::factory()->create());

        $response = $this->json('GET', action([RoleController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                ['id', 'role', 'users', 'details' => ['id', 'name', 'permissions'], 'editable', 'deletable'],
                ['id', 'role', 'users', 'details' => ['id', 'name', 'permissions'], 'editable', 'deletable']
            ]);
    }
}
