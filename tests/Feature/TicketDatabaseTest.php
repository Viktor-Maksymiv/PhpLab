<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Order;
use App\Models\Seat;
use App\Models\Showtime;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TicketDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_tickets_with_relations(): void
    {
        $movie = Movie::factory()->create(['title' => 'Inception']);
        $showtime = Showtime::factory()->create(['movie_id' => $movie->id]);
        Ticket::factory()->create(['showtime_id' => $showtime->id]);

        $response = $this->getJson('/api/tickets');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Inception']);
    }

    public function test_can_create_ticket_in_database(): void
    {
        $order = Order::factory()->create(['total_amount' => 0]);
        $seat = Seat::factory()->create();

        $response = $this->withHeaders(['X-App-Client' => 'web'])
            ->postJson('/api/tickets', [
                'order_id' => $order->id,
                'showtime_id' => $order->showtime_id,
                'seat_id' => $seat->id,
                'price' => 180,
                'status' => 'reserved',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tickets', [
            'order_id' => $order->id,
            'showtime_id' => $order->showtime_id,
            'seat_id' => $seat->id,
        ]);
        $this->assertEquals(180.0, (float) $order->fresh()->total_amount);
    }

    public function test_cannot_sell_same_seat_twice_for_one_showtime(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->withHeaders(['X-App-Client' => 'web'])
            ->postJson('/api/tickets', [
                'order_id' => $ticket->order_id,
                'showtime_id' => $ticket->showtime_id,
                'seat_id' => $ticket->seat_id,
                'price' => 180,
                'status' => 'reserved',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('seat_id');
    }

    public function test_can_create_movie_with_genres_synced_in_pivot_table(): void
    {
        $genres = Genre::factory()->count(2)->create();

        $response = $this->postJson('/api/movies', [
            'title' => 'Dune',
            'duration_minutes' => 155,
            'age_rating' => '12+',
            'release_date' => '2021-10-21',
            'genres' => $genres->pluck('id')->all(),
        ]);

        $response->assertStatus(201);

        $movieId = $response->json('data.id');

        foreach ($genres as $genre) {
            $this->assertDatabaseHas('genre_movie', [
                'movie_id' => $movieId,
                'genre_id' => $genre->id,
            ]);
        }
    }

    public function test_update_movie_syncs_genres_in_pivot_table(): void
    {
        $genres = Genre::factory()->count(2)->create();
        $movie = Movie::factory()->create();
        $movie->genres()->attach($genres->pluck('id')->all());

        $kept = $genres->first();
        $removed = $genres->last();

        $response = $this->putJson('/api/movies/'.$movie->id, ['genres' => [$kept->id]]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('genre_movie', ['movie_id' => $movie->id, 'genre_id' => $kept->id]);
        $this->assertDatabaseMissing('genre_movie', ['movie_id' => $movie->id, 'genre_id' => $removed->id]);
    }

    public function test_ticket_list_uses_constant_number_of_queries(): void
    {
        Ticket::factory()->count(5)->create();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->getJson('/api/tickets')->assertStatus(200);

        $this->assertLessThanOrEqual(6, count(DB::getQueryLog()));
    }
}
