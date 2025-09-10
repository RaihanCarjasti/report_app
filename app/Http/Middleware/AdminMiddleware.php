<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect('/login'); // Redirect to login if not authenticated
        }

        // Check if the user's role matches any of the allowed roles
        if (!in_array(Auth::user()->role, $roles)) {
            return abort(403, 'Unauthorized action.'); // Forbidden access
        }

        return $next($request);
    }
}
