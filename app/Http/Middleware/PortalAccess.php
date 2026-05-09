<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalAccess
{
    /**
     * Only users with a recognised Gemana role may enter the member portal.
     * Super-Admins are redirected to the admin panel instead.
     * Unauthenticated users are sent to login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super-Admins belong in the admin panel, not the member portal.
        if ($user->hasRole('super-admin')) {
            return redirect('/admin');
        }

        // All other recognised roles may access the portal.
        $portalRoles = ['admin', 'team', 'volunteer', 'member'];

        if (! $user->hasAnyRole($portalRoles)) {
            abort(403, 'You do not have permission to access the member portal.');
        }

        return $next($request);
    }
}
