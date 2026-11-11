<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class TicketAccessTest extends TestCase
{
    private array $payload = [
        'showtime_id' => 101,
        'seat_id' => 20,
        'price' => 180,
        'status' => 'reserved',
    ];

    public function test_request_without_client_header_is_blocked_by_middleware(): void
    {
        $response = $this->postJson('/tickets', $this->payload);

        $response->assertStatus(403)
            ->assertJson(['status' => 'access_denied']);
    }

    public function test_request_with_valid_header_passes_middleware(): void
    {
        $response = $this->withHeaders(['X-App-Client' => 'web'])
            ->postJson('/tickets', $this->payload);

        $response->assertStatus(201);
        $response->assertHeader('X-Security-Policy', 'Enforced');
    }

    public function test_invalid_payload_is_rejected_with_validation_error(): void
    {
        $response = $this->withHeaders(['X-App-Client' => 'web'])
            ->postJson('/tickets', ['showtime_id' => 101]);

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
