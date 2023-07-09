<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AuthorizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Seed Roles
         */
        $superadmin = Role::updateOrCreate(['name' => 'superadmin']);
        $client     = Role::updateOrCreate(['name' => 'client']);

        /**
         * Seed Permissions
         */
        $permissions = [
            // Real permissions
            Permission::updateOrCreate(['name' => 'Create role']),
            Permission::updateOrCreate(['name' => 'Read role']),
            Permission::updateOrCreate(['name' => 'Update role']),
            Permission::updateOrCreate(['name' => 'Delete role']),

            Permission::updateOrCreate(['name' => 'Create user']),
            Permission::updateOrCreate(['name' => 'Read user']),
            Permission::updateOrCreate(['name' => 'Update user']),
            Permission::updateOrCreate(['name' => 'Delete user']),

            Permission::updateOrCreate(['name' => 'Create category']),
            Permission::updateOrCreate(['name' => 'Read category']),
            Permission::updateOrCreate(['name' => 'Update category']),
            Permission::updateOrCreate(['name' => 'Delete category']),
        ];


        // Assign permissions to roles
        $superadmin->syncPermissions($permissions);
    }
}
