<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalAccess
{
    /**
     * Only users with a recognised Gemana role may enter the member portal.
     * Unauthenticated users are sent to login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // All recognised roles may access the portal.
        $portalRoles = ['super-admin', 'admin', 'team', 'volunteer', 'member'];

        if (! $user->hasAnyRole($portalRoles)) {
            abort(403, 'You do not have permission to access the member portal.');
        }

        return $next($request);
    }
}
