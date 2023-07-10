<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Tax;
use App\Models\User;
use App\Models\Quote;
use App\Models\Product;
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

        // Just for testing
        Product::factory(30)->create();
        Tax::factory(10)->create();
        Quote::factory(20)->create()->each(function (Quote $quote) {
            $ids = array_rand(range(1, 30), rand(2, 7));
            $products = [];
            foreach ($ids as $k => $id) {
                $id = $id + 1;
                $product = Product::find($id);
                $taxes = [];
                $tax_ids = array_rand(range(1, 10), rand(2, 3));
                if (rand(0, 1)) {
                    foreach ($tax_ids as $tax_id) {
                        $tax_id = $tax_id + 1;
                        $tax = Tax::find($tax_id);
                        $taxes[] = $tax->toArray();
                    }
                }

                $products[$id] = [
                    'name' => $product->name,
                    'price' => $product->getAttributes()['price'],
                    'quantity' => rand(1, 7),
                    'taxes' => json_encode($taxes),
                ];
            }
            $quote->products()->sync($products);
        });
    }
}
