<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Support\FormOptions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MovieController extends Controller
{
    public function index(): View
    {
        $items = Movie::with('genres')->orderByDesc('id')->paginate(10);

        return view('movies.index', compact('items'));
    }

    public function show(Movie $movie): View
    {
        $movie->load(['genres', 'showtimes.hall']);

        return view('movies.show', compact('movie'));
    }

    public function create(): View
    {
        $this->authorize('create', Movie::class);

        return view('movies.create', ['genres' => FormOptions::genres()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Movie::class);

        $validated = $request->validate($this->rules());

        $movie = Movie::create(Arr::except($validated, ['genres']));
        $movie->genres()->sync($validated['genres'] ?? []);

        return redirect()->route('movies.index')->with('success', 'Фільм успішно створено!');
    }

    public function edit(Movie $movie): View
    {
        $this->authorize('update', $movie);

        $movie->load('genres');

        return view('movies.edit', ['movie' => $movie, 'genres' => FormOptions::genres()]);
    }

    public function update(Request $request, Movie $movie): RedirectResponse
    {
        $this->authorize('update', $movie);

        $validated = $request->validate($this->rules());

        $movie->update(Arr::except($validated, ['genres']));
        $movie->genres()->sync($validated['genres'] ?? []);

        return redirect()->route('movies.index')->with('success', 'Фільм успішно оновлено!');
    }

    public function destroy(Movie $movie): RedirectResponse
    {
        $this->authorize('delete', $movie);

        $movie->delete();

        return redirect()->route('movies.index')->with('success', 'Фільм успішно видалено!');
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'age_rating' => ['required', 'string', 'in:0+,6+,12+,16+,18+'],
            'release_date' => ['required', 'date'],
            'poster_url' => ['nullable', 'string', 'max:255'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ];
    }
}
