<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 401);
            }

            // Attach user to request
            $request->merge(['auth_user' => $user]);

        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token is invalid or expired'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authorization token not found'
            ], 401);
        }

        return $next($request);
    }
}
