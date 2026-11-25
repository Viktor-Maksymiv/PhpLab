<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/register',
        summary: 'Реєстрація нового користувача',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Іван Петренко'),
                    new OA\Property(property: 'email', type: 'string', example: 'ivan@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Користувача створено, повернуто токен'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }

    #[OA\Post(
        path: '/login',
        summary: 'Вхід та отримання токена доступу',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'customer1@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Успішний вхід, повернуто токен'),
            new OA\Response(response: 401, description: 'Невірний email або пароль'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Невірний email або пароль'], 401);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('auth_token')->plainTextToken,
        ]);
    }

    #[OA\Post(
        path: '/logout',
        summary: 'Вихід та знищення поточного токена',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Токен видалено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Токен успішно видалено']);
    }
}
