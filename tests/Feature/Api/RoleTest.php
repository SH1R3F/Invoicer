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

        $this->json('GET', action([RoleController::class, 'index']))->assertStatus(403);
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
        $this->assertCount(4, Role::where('name', $name)->first()->permissions);
    }

    public function test_it_doesnt_create_role_for_unauthorized_users(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->json('POST', action([RoleController::class, 'store']))->assertStatus(403);
    }

    public function test_it_updates_role_with_permissions(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->syncRoles(Role::where('name', 'superadmin')->first());
        Sanctum::actingAs($user);

        $role = Role::create(['name' => 'test']);
        $response = $this->json('PUT', action([RoleController::class, 'update'], [$role->id]), [
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

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Role updated successfully']);
        $this->assertCount(4, Role::where('name', $name)->first()->permissions);
    }

    public function test_it_doesnt_update_role_for_unauthorized_users(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $role = Role::create(['name' => 'test']);
        $this->json('PUT', action([RoleController::class, 'update'], [$role->id]))->assertStatus(403);
    }

    public function test_it_deletes_role(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->syncRoles(Role::where('name', 'superadmin')->first());
        Sanctum::actingAs($user);

        $role = Role::create(['name' => 'test']);
        $response = $this->json('DELETE', action([RoleController::class, 'destroy'], [$role->id]));

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Role deleted successfully']);
    }

    public function test_it_doesnt_delete_superadmin_and_client_roles(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        $user->syncRoles(Role::where('name', 'superadmin')->first());
        Sanctum::actingAs($user);

        $role = Role::where('name', 'superadmin')->first();
        $this->json('DELETE', action([RoleController::class, 'destroy'], [$role->id]))->assertStatus(403);

        $role = Role::where('name', 'client')->first();
        $this->json('DELETE', action([RoleController::class, 'destroy'], [$role->id]))->assertStatus(403);
    }

    public function test_it_doesnt_delete_role_for_unauthorized_users(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $role = Role::create(['name' => 'test']);

        $this->json('DELETE', action([RoleController::class, 'destroy'], [$role->id]))->assertStatus(403);
    }
}
