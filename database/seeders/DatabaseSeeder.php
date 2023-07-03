<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Roles & Permissions
        $this->call(AuthorizationSeeder::class);

        // Seed Users
        // User::factory(10)->create();
        $superadmin = User::where('email', $email = 'superadmin@example.test')->first() ?? User::factory()->create([
            'name' => 'Superadmin',
            'email' => $email,
        ]);

        $superadmin->syncRoles(Role::where('name', 'superadmin')->first());
    }
}
