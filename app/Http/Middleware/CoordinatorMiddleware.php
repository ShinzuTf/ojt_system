<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CoordinatorMiddleware
{
    /**
     * Handle incoming request - only allow supervisors/coordinators
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'coordinator') {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Only coordinators/supervisors can access this area.');
    }
}
