<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_movies_without_management_buttons(): void
    {
        Movie::factory()->create(['title' => 'Публічний фільм']);

        $response = $this->get('/movies');

        $response->assertStatus(200)
            ->assertSee('Публічний фільм')
            ->assertDontSee('Додати фільм');
    }

    public function test_guest_is_redirected_to_login_from_protected_pages(): void
    {
        $this->get('/movies/create')->assertRedirect(route('login'));
        $this->get('/tickets')->assertRedirect(route('login'));
        $this->get('/orders')->assertRedirect(route('login'));
    }

    public function test_customer_cannot_open_movie_create_form(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/movies/create');

        $response->assertStatus(403);
    }

    public function test_admin_sees_management_buttons(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/movies');

        $response->assertStatus(200)
            ->assertSee('Додати фільм');
    }

    public function test_foreign_user_cannot_edit_ticket(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $intruder = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($intruder)->get('/tickets/'.$ticket->id.'/edit');

        $response->assertStatus(403);
    }

    public function test_owner_can_open_ticket_edit_form(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->get('/tickets/'.$ticket->id.'/edit');

        $response->assertStatus(200);
    }

    public function test_customer_ticket_list_contains_only_own_tickets(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $own = Ticket::factory()->create(['user_id' => $owner->id]);
        $foreign = Ticket::factory()->create();

        $response = $this->actingAs($owner)->get('/tickets');

        $response->assertStatus(200)
            ->assertSee($own->qr_code)
            ->assertDontSee($foreign->qr_code);
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'secret-pass-123']);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-123']);

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Новий Глядач',
            'email' => 'viewer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('movies.index'));

        $this->assertDatabaseHas('users', ['email' => 'viewer@example.com', 'role' => 'customer']);
        $this->assertAuthenticated();
    }
}
