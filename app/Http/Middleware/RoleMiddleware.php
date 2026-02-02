<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 401);
            }

            // Check if user has any of the required roles
            $userRole = $user->role->value;
            
            if (!in_array($userRole, $roles)) {
                return response()->json([
                    'message' => 'Forbidden: Insufficient permissions'
                ], 403);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authorization token not found'
            ], 401);
        }

        return $next($request);
    }
}
