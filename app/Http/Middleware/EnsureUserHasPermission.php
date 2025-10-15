<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect('/admin/login');
        }

        $user = Auth::user();

        // Check if user has the required permission
        if (!$user->can($permission)) {
            // Return 403 for API requests, redirect for web requests
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have permission to access this resource.'
                ], 403);
            }

            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}