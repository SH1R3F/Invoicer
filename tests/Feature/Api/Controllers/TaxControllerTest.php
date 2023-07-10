<?php

namespace Tests\Feature\Api\Controllers;

use App\Models\Tax;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Api\V1\TaxController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaxControllerTest extends TestCase
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

    public function test_it_lists_paginated_taxes(): void
    {
        Tax::factory(2)->create();

        $response = $this->json('GET', action([TaxController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 10);
    }


    public function test_it_lists_searched_products(): void
    {
        Tax::factory(4)->create();
        Tax::factory()->create(['name' => 'Search name']);

        $response = $this->json('GET', action([TaxController::class, 'index']), [
            'q' => 'Search name'
        ]);

        $this->assertEquals(5, Tax::count());
        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
    public function test_it_lists_ordered_products(): void
    {
        Tax::factory(10)->create();
        $response = $this->json('GET', action([TaxController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertSeeInOrder([10, 9, 8, 7, 6, 5, 4, 3, 2, 1]);
    }

    public function test_it_creates_new_tax(): void
    {
        $tax = Tax::factory()->make()->toArray();
        $response = $this->json('POST', action([TaxController::class, 'store']), $tax);

        $response->assertStatus(201);
        $this->assertEquals($tax['name'], Tax::first()->name);
    }

    public function test_it_shows_single_tax(): void
    {
        $tax = Tax::factory()->create();

        $response = $this->json('GET', action([TaxController::class, 'show'], [$tax->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['id', 'name', 'value', 'type', 'default']);
    }

    public function test_it_updates_tax(): void
    {
        $tax = Tax::factory()->create();

        $this->json('PUT', action([TaxController::class, 'update'], [$tax->id]), [
            "name" => "test",
            "value" => 15,
            "default" => false,
            "type" => 'percentage',
        ])->assertStatus(200);

        $tax->refresh();
        $this->assertEquals('test', $tax->name);
    }

    public function test_it_deletes_tax(): void
    {
        $tax = Tax::factory()->create();
        $response = $this->json('DELETE', action([TaxController::class, 'destroy'], [$tax->id]));

        $response
            ->assertStatus(200)
            ->assertJson(['message' => 'Tax deleted successfully']);
    }
}
