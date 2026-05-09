<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $allowedRoutes = [
            'portal.security',
            'two-factor.enable',
            'two-factor.confirm',
            'two-factor.challenge',
            'two-factor.qr-code',
            'two-factor.secret-key',
            'two-factor.recovery-codes',
            'two-factor.regenerate-recovery-codes',
            'logout',
        ];

        if (in_array($request->route()?->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // Admin routes bypass 2FA enforcement until the admin
        // security page is built in a later phase.
        if ($request->is('admin*')) {
            return $next($request);
        }

        // Redirect to portal security page to set up 2FA.
        if ($user->requires2fa() && ! $user->hasTwoFactorEnabled()) {
            return redirect()->route('portal.security')
                ->with('warning', 'Your account role requires two-factor authentication. Please set it up to continue.');
        }

        return $next($request);
    }
}
