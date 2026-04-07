<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if ($request->user()) {
            return response()->json([
                'message' => 'non authentifier, veuillez vous connecter',
            ],401);
        }

        if (!in_array($request->user()->role, $roles)) {
            return response()->json([
                'message' => "acces refuse , vous n'avez pas les permissions necessaires",
                'your_role' => $request->user()->role,
                'requires_roles' => $roles,
            ],403);
        }
    
        return $next($request);
    }
}
