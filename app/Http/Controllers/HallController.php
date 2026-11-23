<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hall;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HallController extends Controller
{
    public function index(): View
    {
        $items = Hall::query()->withCount('seats')->orderBy('name')->paginate(10);

        return view('halls.index', compact('items'));
    }

    public function show(Hall $hall): View
    {
        $hall->load('seats');

        return view('halls.show', compact('hall'));
    }

    public function create(): View
    {
        return view('halls.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        Hall::create($validated);

        return redirect()->route('halls.index')->with('success', 'Зал успішно створено!');
    }

    public function edit(Hall $hall): View
    {
        return view('halls.edit', compact('hall'));
    }

    public function update(Request $request, Hall $hall): RedirectResponse
    {
        $validated = $request->validate($this->rules($hall));

        $hall->update($validated);

        return redirect()->route('halls.index')->with('success', 'Зал успішно оновлено!');
    }

    public function destroy(Hall $hall): RedirectResponse
    {
        $hall->delete();

        return redirect()->route('halls.index')->with('success', 'Зал успішно видалено!');
    }

    private function rules(?Hall $hall = null): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:50', Rule::unique('halls', 'name')->ignore($hall?->id)],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'format' => ['required', 'string', 'in:2D,3D,IMAX,4DX'],
            'rows_count' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
