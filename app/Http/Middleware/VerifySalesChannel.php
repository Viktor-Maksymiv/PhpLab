<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySalesChannel
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasHeader('X-App-Client')) {
            return response()->json([
                'status' => 'access_denied',
                'message' => 'Відхилено: відсутній обов’язковий заголовок X-App-Client.',
            ], 403);
        }

        $response = $next($request);
        $response->headers->set('X-Security-Policy', 'Enforced');

        return $response;
    }
}
