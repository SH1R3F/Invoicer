<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Api\V1\RoleController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_roles(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->syncRoles(Role::where('name', 'superadmin')->first());
        Sanctum::actingAs($user);

        $response = $this->json('GET', action([RoleController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonStructure([
                ['id', 'role', 'users', 'details' => ['id', 'name', 'permissions'], 'editable', 'deletable'],
                ['id', 'role', 'users', 'details' => ['id', 'name', 'permissions'], 'editable', 'deletable']
            ]);
    }

    public function test_it_doesnt_list_roles_for_unauthorized_users(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->json('GET', action([RoleController::class, 'index']));

        $response
            ->assertStatus(403);
    }

    public function test_it_creates_new_role_with_permissions(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->syncRoles(Role::where('name', 'superadmin')->first());
        Sanctum::actingAs($user);

        $response = $this->json('POST', action([RoleController::class, 'store']), [
            'name' => $name = 'test',
            'permissions' => [
                [
                    "name" => "Roles",
                    "Create role" => true,
                    "Read role" => true,
                    "Update role" => true,
                    "Delete role" => true
                ],
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('role_has_permissions', ['role_id' => Role::where('name', $name)->first()->id]);
    }
}
