<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogViewTest extends TestCase
{
    use RefreshDatabase;

    private function moviePayload(array $override = []): array
    {
        return array_merge([
            'title' => 'Тестовий фільм',
            'duration_minutes' => 120,
            'age_rating' => '12+',
            'release_date' => '2024-05-01',
        ], $override);
    }

    public function test_home_page_displays_navigation(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Фільми')
            ->assertSee('Квитки');
    }

    public function test_movies_index_displays_movie_with_genres(): void
    {
        $movie = Movie::factory()->create(['title' => 'Унікальний тестовий фільм']);
        $genre = Genre::factory()->create(['name' => 'Унікальний жанр']);
        $movie->genres()->attach($genre->id);

        $response = $this->get('/movies');

        $response->assertStatus(200)
            ->assertSee('Унікальний тестовий фільм')
            ->assertSee('Унікальний жанр')
            ->assertSee('Додати фільм');
    }

    public function test_movie_form_shows_validation_errors(): void
    {
        $response = $this->post('/movies', ['title' => '', 'duration_minutes' => 'invalid']);

        $response->assertSessionHasErrors(['title', 'duration_minutes', 'age_rating', 'release_date']);
    }

    public function test_movie_can_be_created_via_form_with_genres(): void
    {
        $genres = Genre::factory()->count(2)->create();

        $response = $this->post('/movies', $this->moviePayload(['genres' => $genres->pluck('id')->all()]));

        $response->assertRedirect(route('movies.index'))
            ->assertSessionHas('success');

        $movie = Movie::where('title', 'Тестовий фільм')->firstOrFail();

        foreach ($genres as $genre) {
            $this->assertDatabaseHas('genre_movie', ['movie_id' => $movie->id, 'genre_id' => $genre->id]);
        }
    }

    public function test_movie_can_be_updated_and_genres_are_synced(): void
    {
        $genres = Genre::factory()->count(2)->create();
        $movie = Movie::factory()->create();
        $movie->genres()->attach($genres->pluck('id')->all());

        $kept = $genres->first();
        $removed = $genres->last();

        $response = $this->put('/movies/'.$movie->id, $this->moviePayload([
            'title' => 'Оновлена назва',
            'genres' => [$kept->id],
        ]));

        $response->assertRedirect(route('movies.index'));

        $this->assertDatabaseHas('movies', ['id' => $movie->id, 'title' => 'Оновлена назва']);
        $this->assertDatabaseHas('genre_movie', ['movie_id' => $movie->id, 'genre_id' => $kept->id]);
        $this->assertDatabaseMissing('genre_movie', ['movie_id' => $movie->id, 'genre_id' => $removed->id]);
    }

    public function test_movie_can_be_deleted(): void
    {
        $movie = Movie::factory()->create();

        $response = $this->delete('/movies/'.$movie->id);

        $response->assertRedirect(route('movies.index'));

        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);
    }

    public function test_showtime_create_form_shows_related_options(): void
    {
        Movie::factory()->create(['title' => 'Фільм для списку']);
        Hall::factory()->create(['name' => 'Зал для списку']);

        $response = $this->get('/showtimes/create');

        $response->assertStatus(200)
            ->assertSee('Фільм для списку')
            ->assertSee('Зал для списку');
    }

    public function test_tickets_index_displays_ticket_with_relations(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->get('/tickets');

        $response->assertStatus(200)
            ->assertSee($ticket->qr_code);
    }

    public function test_ticket_form_rejects_taken_seat(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->post('/tickets', [
            'order_id' => $ticket->order_id,
            'showtime_id' => $ticket->showtime_id,
            'seat_id' => $ticket->seat_id,
            'price' => 180,
            'status' => 'reserved',
        ]);

        $response->assertSessionHasErrors('seat_id');
    }

    public function test_order_can_be_paid_via_form(): void
    {
        $order = Order::factory()->create(['status' => 'pending', 'total_amount' => 200]);

        $response = $this->post(route('orders.pay', $order), ['method' => 'card']);

        $response->assertRedirect(route('orders.show', $order));

        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'method' => 'card']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid']);
    }
}
