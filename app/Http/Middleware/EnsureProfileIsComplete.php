<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // ✅ List of routes that should skip this middleware
            $excludedRoutes = [
                'profile.complete',
                'profile.complete.store',
                'logout',
                'auth.google',
                'auth.google.callback',
            ];

            // ✅ Skip if route is excluded
            $currentRoute = $request->route() ? $request->route()->getName() : null;
            if ($currentRoute && in_array($currentRoute, $excludedRoutes)) {
                return $next($request);
            }

            // ✅ Skip if URL path is complete-profile
            if ($request->is('complete-profile') || $request->is('complete-profile/*')) {
                return $next($request);
            }

            // ✅ Only apply to authenticated users
            if (!Auth::check()) {
                return $next($request);
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // ✅ Now IDE knows $user is User model
            if (!$user->hasCompletedProfile()) {
                Log::info('Redirecting to complete profile', [
                    'user_id' => $user->id,
                    'current_route' => $currentRoute,
                    'current_path' => $request->path()
                ]);

                return redirect()->route('profile.complete')
                    ->with('info', 'Please complete your profile to continue.');
            }

            return $next($request);

        } catch (\Exception $e) {
            // ✅ Log error and allow request to continue
            Log::error('EnsureProfileIsComplete middleware error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $next($request);
        }
    }
}
