<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Redirect authenticated users to the correct dashboard based on their role.
     * This replaces Laravel's default RedirectIfAuthenticated.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                return redirect($this->homeForUser($user));
            }
        }

        return $next($request);
    }

    /**
     * Determine the correct home URL for a given user based on their role.
     */
    protected function homeForUser($user): string
    {
        if ($user->hasRole('super-admin')) {
            return '/admin';
        }

        if ($user->hasAnyRole(['admin', 'team'])) {
            return '/admin';
        }

        // Volunteers and Members go to the member portal.
        return '/portal/dashboard';
    }
}
