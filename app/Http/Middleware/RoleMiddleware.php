<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
       $user = $request->user();

        if (!$user) {
            return response()->json([
                'status'  => 'Error',
                'code'    => 401,
                'message' => 'Unauthorized',
                'data'    => null,
            ], 401);
        }

        // Check if user's role matches any of the allowed roles
        if (!in_array($user->role->name, $roles)) {
            return response()->json([
                'status'  => 'Error',
                'code'    => 403,
                'message' => 'Forbidden',
                'data'    => null,
            ], 403);
        }

        return $next($request);
    }
}
