<?php

namespace Tests\Feature\Api\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quote;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Api\V1\QuoteController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuoteControllerTest extends TestCase
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

    public function test_it_lists_paginated_quotes(): void
    {
        Quote::factory(2)->create();

        $response = $this->json('GET', action([QuoteController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 10);
    }


    public function test_it_lists_searched_quotes_by_users(): void
    {
        Quote::factory(4)->create();
        $quote = Quote::factory()->create();

        $response = $this->json('GET', action([QuoteController::class, 'index']), [
            'q' => $quote->client->name
        ]);

        $this->assertEquals(5, Quote::count());
        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_creates_new_quote_with_products(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('POST', action([QuoteController::class, 'store']), [
            "user_id" => 1,
            "quote_date" => "2003-07-16",
            "due_date" => "2003-07-16",
            "discount_type" => "percentage",
            "discount_value" => 5,
            "notes" => "my deepest thoughts",
            "products" => [
                [
                    "product_id" => $product->id,
                    "product_name" => $product->name,
                    "product_price" => $product->getAttribute('price'),
                    "product_quantity" => 3,
                    "product_taxes" => []
                ],
                [
                    "product_name" => "new test",
                    "product_price" => 2423,
                    "product_quantity" => 4,
                    "product_taxes" => []
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertEquals('0001', Quote::first()->quote_number);
        $this->assertDatabaseCount('productables', 2);
    }

    public function test_it_shows_single_quote_with_products(): void
    {
        $quote = Quote::factory()->create();

        $response = $this->json('GET', action([QuoteController::class, 'show'], [$quote->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'user', 'quote_number', 'productables' => []]);
    }

    public function test_it_deletes_quote_with_its_quotables(): void
    {
        $products = Product::factory(2)->create();
        $quote = Quote::factory()->create();

        $quote->quotables()->createMany($products->map(function (Product $product) {
            return [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->getAttribute('price'),
                'quantity' => 2,
                'taxes' => []
            ];
        })->toArray());

        $this->assertDatabaseCount('productables', 2);


        $response = $this->json('DELETE', action([QuoteController::class, 'destroy'], [$quote->id]));

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Quote deleted successfully']);
        $this->assertDatabaseCount('productables', 0);
    }
}
