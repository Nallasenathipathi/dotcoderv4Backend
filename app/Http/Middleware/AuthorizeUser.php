<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is authenticated and has role == 1
        if ($user && $user->role == 1) {
            $token = $user->currentAccessToken();

            // Check if token is expired
            if ($token && (!$token->expires_at || $token->expires_at->isFuture())) {
                return $next($request);
            }

            return response()->json([
                'message' => 'Token has expired',
                'status' => 401
            ], 401);
        }

        return response()->json([
            'message' => 'Unauthorized',
            'status' => 403
        ], 403);
    }
}
