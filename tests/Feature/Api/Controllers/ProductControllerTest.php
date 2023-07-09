<?php

namespace Tests\Feature\Api\Controllers;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Api\V1\ProductController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
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

    public function test_it_lists_paginated_products(): void
    {
        Product::factory(2)->create();

        $response = $this->json('GET', action([ProductController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data', 'meta', 'links', 'categories'])
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 10);
    }

    public function test_it_lists_filtered_products(): void
    {
        Product::factory(3)->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->json('GET', action([ProductController::class, 'index']), [
            'category_id' => $category->id
        ]);

        $this->assertEquals(4, Product::count());
        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_lists_searched_products(): void
    {
        Product::factory(4)->create();
        Product::factory()->create(['name' => 'Search name']);

        $response = $this->json('GET', action([ProductController::class, 'index']), [
            'q' => 'Search name'
        ]);

        $this->assertEquals(5, Product::count());
        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_lists_ordered_products(): void
    {
        Product::factory(10)->create();
        $response = $this->json('GET', action([ProductController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertSeeInOrder([10, 9, 8, 7, 6, 5, 4, 3, 2, 1]);
    }
}
