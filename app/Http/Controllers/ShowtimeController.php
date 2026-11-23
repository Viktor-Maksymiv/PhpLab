<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Showtime;
use App\Support\FormOptions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShowtimeController extends Controller
{
    public function index(): View
    {
        $items = Showtime::with(['movie', 'hall'])->orderBy('start_at')->paginate(10);

        return view('showtimes.index', compact('items'));
    }

    public function show(Showtime $showtime): View
    {
        $showtime->load(['movie.genres', 'hall'])->loadCount('tickets');

        return view('showtimes.show', compact('showtime'));
    }

    public function create(): View
    {
        return view('showtimes.create', $this->options());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeStartAt($request);

        $validated = $request->validate($this->rules($request));

        Showtime::create($validated);

        return redirect()->route('showtimes.index')->with('success', 'Сеанс успішно створено!');
    }

    public function edit(Showtime $showtime): View
    {
        return view('showtimes.edit', ['showtime' => $showtime] + $this->options());
    }

    public function update(Request $request, Showtime $showtime): RedirectResponse
    {
        $this->normalizeStartAt($request);

        $validated = $request->validate($this->rules($request, $showtime));

        $showtime->update($validated);

        return redirect()->route('showtimes.index')->with('success', 'Сеанс успішно оновлено!');
    }

    public function destroy(Showtime $showtime): RedirectResponse
    {
        $showtime->delete();

        return redirect()->route('showtimes.index')->with('success', 'Сеанс успішно видалено!');
    }

    private function options(): array
    {
        return ['movies' => FormOptions::movies(), 'halls' => FormOptions::halls()];
    }

    private function normalizeStartAt(Request $request): void
    {
        $value = $request->input('start_at');

        if (is_string($value) && strtotime($value) !== false) {
            $request->merge(['start_at' => date('Y-m-d H:i:s', strtotime($value))]);
        }
    }

    private function rules(Request $request, ?Showtime $showtime = null): array
    {
        return [
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'start_at' => [
                'required',
                'date',
                Rule::unique('showtimes', 'start_at')
                    ->where('hall_id', $request->input('hall_id'))
                    ->ignore($showtime?->id),
            ],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:scheduled,cancelled,finished'],
        ];
    }
}
