<?php

namespace Tests\Feature\Api\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quote;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Api\V1\QuoteController;
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
}
