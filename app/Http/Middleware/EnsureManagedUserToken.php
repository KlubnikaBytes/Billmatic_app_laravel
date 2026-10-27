<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureManagedUserToken
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $payload = auth('managed-api')->payload();

            if ($payload->get('account_type') !== 'managed_user') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid managed-user token.',
                ], 403);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        return $next($request);
    }
}