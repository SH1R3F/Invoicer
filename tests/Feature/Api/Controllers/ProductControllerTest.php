<?php

namespace Tests\Feature\Api\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Api\V1\ProductController;
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

    public function test_it_creates_new_product(): void
    {
        $category = Category::factory()->create();

        $response = $this->json('POST', action([ProductController::class, 'store']), [
            "sku" => $sku = Str::random(8),
            "name" => "prod name",
            "description" => "description",
            "category_id" => $category->id,
            "price" => 100,
        ]);

        $response->assertStatus(201);
        $this->assertEquals($sku, Product::first()->sku);
    }

    public function test_it_shows_single_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('GET', action([ProductController::class, 'show'], [$product->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'product'    => ['id', 'name', 'sku', 'description', 'category_id'],
                'categories' => []
            ]);
    }

    public function test_it_updates_product(): void
    {
        $product = Product::factory()->create();

        $this->json('PUT', action([ProductController::class, 'update'], [$product->id]), [
            "sku" => Str::random(8),
            "name" => "test",
            "description" => "description",
            "category_id" => $product->category_id,
            "price" => 100,
        ])->assertStatus(200);

        $product->refresh();
        $this->assertEquals('test', $product->name);
    }

    public function test_it_deletes_product(): void
    {
        $product = Product::factory()->create();
        $response = $this->json('DELETE', action([ProductController::class, 'destroy'], [$product->id]));

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Product deleted successfully']);
    }
}
