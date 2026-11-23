<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Support\FormOptions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SeatController extends Controller
{
    public function index(): View
    {
        $items = Seat::with('hall')->orderBy('hall_id')->orderBy('row_number')->orderBy('seat_number')->paginate(15);

        return view('seats.index', compact('items'));
    }

    public function create(): View
    {
        return view('seats.create', ['halls' => FormOptions::halls()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules($request));

        Seat::create($validated);

        return redirect()->route('seats.index')->with('success', 'Місце успішно створено!');
    }

    public function edit(Seat $seat): View
    {
        return view('seats.edit', ['seat' => $seat, 'halls' => FormOptions::halls()]);
    }

    public function update(Request $request, Seat $seat): RedirectResponse
    {
        $validated = $request->validate($this->rules($request, $seat));

        $seat->update($validated);

        return redirect()->route('seats.index')->with('success', 'Місце успішно оновлено!');
    }

    public function destroy(Seat $seat): RedirectResponse
    {
        $seat->delete();

        return redirect()->route('seats.index')->with('success', 'Місце успішно видалено!');
    }

    private function rules(Request $request, ?Seat $seat = null): array
    {
        return [
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'row_number' => ['required', 'integer', 'min:1', 'max:50'],
            'seat_number' => [
                'required',
                'integer',
                'min:1',
                'max:50',
                Rule::unique('seats', 'seat_number')
                    ->where('hall_id', $request->input('hall_id'))
                    ->where('row_number', $request->input('row_number'))
                    ->ignore($seat?->id),
            ],
            'type' => ['required', 'string', 'in:standard,vip,sofa'],
        ];
    }
}
