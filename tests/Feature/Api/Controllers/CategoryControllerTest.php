<?php

namespace Tests\Feature\Api\Controllers;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Models\Category;

class CategoryControllerTest extends TestCase
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

    public function test_it_lists_paginated_categories(): void
    {
        Category::factory(2)->create();

        $response = $this->json('GET', action([CategoryController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 10);
    }

    public function test_it_lists_searched_Categories(): void
    {
        Category::factory(10)->create();
        Category::factory()->create([
            'name' => 'Searched Name'
        ]);

        $response = $this->json('GET', action([CategoryController::class, 'index']), [
            'q' => 'Searched Name'
        ]);


        $this->assertEquals(11, Category::count());
        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_it_lists_ordered_categories(): void
    {
        Category::factory(10)->create();

        $response = $this->json('GET', action([CategoryController::class, 'index']));

        $response
            ->assertStatus(200)
            ->assertSeeInOrder([10, 9, 8, 7, 6, 5, 4, 3, 2, 1]);
    }
}
