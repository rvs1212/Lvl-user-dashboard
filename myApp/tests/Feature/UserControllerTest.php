<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_users()
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/users?per_page=2');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data'])
            ->assertJsonCount(2, 'data');
    }

    public function test_index_filters_by_search_term()
    {
        User::factory()->create([
            'first_name' => 'Alice',
            'email'      => 'alice@example.com',
        ]);
        User::factory()->create([
            'first_name' => 'Bob',
            'email'      => 'bob@example.com',
        ]);

        $response = $this->getJson('/api/v1/users?search=Alice');

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['first_name' => 'Alice']);
    }

    public function test_index_respects_per_page_and_page_parameters()
    {
        $users      = User::factory()->count(3)->create();
        $sortedDesc = $users->sortByDesc('id')->values();

        $resp1 = $this->getJson('/api/v1/users?per_page=1&page=1');
        $resp1->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $sortedDesc[0]->id]);

        $resp2 = $this->getJson('/api/v1/users?per_page=1&page=2');
        $resp2->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['id' => $sortedDesc[1]->id]);
    }

    public function test_show_returns_user_when_found()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'id'         => $user->id,
                'first_name' => $user->first_name,
                'email'      => $user->email,
            ]);
    }

    public function test_show_returns_validation_error_for_invalid_id()
    {
        $response = $this->getJson('/api/v1/users/99999999999');

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('id');
    }

    public function test_store_creates_user_and_returns_201()
    {
        $payload = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john.doe@example.com',
            'password'   => 'secret123',
            'city'       => 'Metropolis',
            'country'    => 'Fictionland',
            'post_code'  => '12345',
            'street'     => '123 Main St',
        ];

        $response = $this->postJson('/api/v1/users', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['email' => 'john.doe@example.com']);

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@example.com',
        ]);
    }

    public function test_store_returns_validation_errors_for_missing_fields()
    {
        $response = $this->postJson('/api/v1/users', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
                'password',
            ]);
    }

    public function test_update_modifies_user_and_returns_200()
    {
        $user = User::factory()->create([
            'first_name' => 'Original',
            'last_name'  => 'Name',
            'email'      => 'orig@example.com',
        ]);

        // Now call PUT on the correct URL, without including 'id' in payload
        $payload = [
            'first_name' => 'Updated',
            'last_name'  => 'User',
            'email'      => 'updated@example.com',
            'city'       => 'NewCity',
            'country'    => 'NewCountry',
            'post_code'  => '99999',
            'street'     => '999 New St',
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $payload);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'id'         => $user->id,
                'first_name' => 'Updated',
                'email'      => 'updated@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'first_name' => 'Updated',
            'email'      => 'updated@example.com',
        ]);
    }


    public function test_update_returns_validation_errors_for_invalid_data()
    {
        $user = User::factory()->create();

        $response = $this->putJson("/api/v1/users/{$user->id}", []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
            ]);
    }


    public function test_delete_user_deletes_and_returns_204()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_delete_returns_404_when_user_not_found()
    {
        $response = $this->deleteJson('/api/v1/users/999');

        $response
            ->assertStatus(404)
            ->assertExactJson(['message' => 'Not found']);
    }

}
