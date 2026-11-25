<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $items = User::query()->withCount('orders')->orderBy('name')->paginate(10);

        return view('users.index', compact('items'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate($this->rules($request) + [
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Користувача успішно створено!');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate($this->rules($request, $user) + [
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Користувача успішно оновлено!');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Користувача успішно видалено!');
    }

    private function rules(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        if ($request->user()->isAdmin()) {
            $rules += [
                'role' => ['required', 'string', 'in:customer,cashier,admin'],
                'loyalty_points' => ['required', 'integer', 'min:0'],
            ];
        }

        return $rules;
    }
}
