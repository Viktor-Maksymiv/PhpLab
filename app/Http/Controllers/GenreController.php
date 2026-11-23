<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GenreController extends Controller
{
    public function index(): View
    {
        $items = Genre::query()->withCount('movies')->orderBy('name')->paginate(10);

        return view('genres.index', compact('items'));
    }

    public function create(): View
    {
        return view('genres.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Genre::create($validated);

        return redirect()->route('genres.index')->with('success', 'Жанр успішно створено!');
    }

    public function edit(Genre $genre): View
    {
        return view('genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre): RedirectResponse
    {
        $validated = $request->validate($this->rules($genre));

        $genre->update($validated);

        return redirect()->route('genres.index')->with('success', 'Жанр успішно оновлено!');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        $genre->delete();

        return redirect()->route('genres.index')->with('success', 'Жанр успішно видалено!');
    }

    private function rules(?Genre $genre = null): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('genres', 'name')->ignore($genre?->id)],
            'slug' => ['required', 'string', 'max:120', Rule::unique('genres', 'slug')->ignore($genre?->id)],
            'description' => ['nullable', 'string'],
        ];
    }
}
