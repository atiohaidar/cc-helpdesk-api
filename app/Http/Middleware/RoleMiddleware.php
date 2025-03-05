<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // echo $role . "\n" .$request->user()->hasRole($role) ;
        // Pastikan user sudah login dan memiliki role yang sesuai
        if (!$request->user() || !$request->user()->hasRole($role)) {
            return response()->json(['message' => 'Unauthorized - Access Denied'], 403);
        }

        return $next($request);
    }
}
