<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if account is active
            if (!$user->is_active) {
                Auth::logout();
                return redirect('/admin/login')->withErrors([
                    'email' => 'Your account has been deactivated. Please contact an administrator.'
                ]);
            }

            // Check if account is locked
            if ($user->isLocked()) {
                Auth::logout();
                return redirect('/admin/login')->withErrors([
                    'email' => "Your account is locked until {$user->locked_until->format('M j, Y g:i A')}. Please try again later or contact an administrator."
                ]);
            }

            // Check if password must be changed
            if ($user->mustChangePassword()) {
                if (!$request->routeIs('filament.admin.pages.change-password')) {
                    return redirect()->route('filament.admin.pages.change-password')
                        ->with('warning', 'You must change your password before continuing.');
                }
            }

            // Update last activity
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
        }

        return $next($request);
    }
}