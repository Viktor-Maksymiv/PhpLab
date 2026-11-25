<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function moviePayload(): array
    {
        return [
            'title' => 'Дюна: Частина друга',
            'duration_minutes' => 166,
            'age_rating' => '12+',
            'release_date' => '2024-02-29',
        ];
    }

    public function test_public_movies_list_is_available_without_token(): void
    {
        Movie::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/movies');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['*' => ['id', 'title', 'age_rating']]]);
    }

    public function test_guest_cannot_create_movie(): void
    {
        $response = $this->postJson('/api/v1/movies', $this->moviePayload());

        $response->assertStatus(401);
    }

    public function test_customer_cannot_create_movie(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'customer']));

        $response = $this->postJson('/api/v1/movies', $this->moviePayload());

        $response->assertStatus(403);
    }

    public function test_admin_can_create_movie(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->postJson('/api/v1/movies', $this->moviePayload());

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Дюна: Частина друга');

        $this->assertDatabaseHas('movies', ['title' => 'Дюна: Частина друга']);
    }

    public function test_admin_gets_validation_errors_for_invalid_movie(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->postJson('/api/v1/movies', ['title' => '']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'duration_minutes']);
    }

    public function test_owner_can_delete_own_ticket(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($owner);

        $response = $this->deleteJson('/api/v1/tickets/'.$ticket->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_foreign_user_cannot_delete_ticket(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $intruder = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        Sanctum::actingAs($intruder);

        $response = $this->deleteJson('/api/v1/tickets/'.$ticket->id);

        $response->assertStatus(403);

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    public function test_customer_sees_only_own_orders(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Order::factory()->create(['user_id' => $customer->id]);
        Order::factory()->count(2)->create();

        Sanctum::actingAs($customer);

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_login_returns_token_for_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret-pass-123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user' => ['id', 'email'], 'token']);
    }

    public function test_login_rejects_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_register_creates_user_and_returns_token(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Новий Користувач',
            'email' => 'new-user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user' => ['id', 'email'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'new-user@example.com', 'role' => 'customer']);
    }
}
