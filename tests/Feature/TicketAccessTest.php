<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Seat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_without_client_header_is_blocked_by_middleware(): void
    {
        $response = $this->postJson('/tickets', []);

        $response->assertStatus(403)
            ->assertJson(['status' => 'access_denied']);
    }

    public function test_request_with_valid_header_passes_middleware(): void
    {
        $order = Order::factory()->create();
        $seat = Seat::factory()->create();

        $response = $this->withHeaders(['X-App-Client' => 'web'])
            ->postJson('/tickets', [
                'order_id' => $order->id,
                'showtime_id' => $order->showtime_id,
                'seat_id' => $seat->id,
                'price' => 180,
                'status' => 'reserved',
            ]);

        $response->assertStatus(201);
        $response->assertHeader('X-Security-Policy', 'Enforced');
    }

    public function test_invalid_payload_is_rejected_with_validation_error(): void
    {
        $response = $this->withHeaders(['X-App-Client' => 'web'])
            ->postJson('/tickets', ['price' => 180]);

        $response->assertStatus(422);
    }

    public function test_tickets_list_returns_ok(): void
    {
        $response = $this->getJson('/tickets');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }

    public function test_unknown_ticket_returns_not_found(): void
    {
        $response = $this->getJson('/tickets/999');

        $response->assertStatus(404);
    }
}
