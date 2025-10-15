<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTwoFactorAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if 2FA is enabled and user hasn't verified it in this session
            if ($user->two_factor_enabled && !session('two_factor_verified_' . $user->id)) {
                // Skip 2FA check for the verification page itself
                if (!$request->routeIs('filament.admin.pages.two-factor-verification')) {
                    return redirect()->route('filament.admin.pages.two-factor-verification')
                        ->with('message', 'Please verify your two-factor authentication code.');
                }
            }
        }

        return $next($request);
    }
}