<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRoleId = method_exists($user, 'getActiveRoleId') ? (string) $user->getActiveRoleId() : (string) ($user->role_id ?? '');
        $primaryRoleId = (string) ($user->role_id ?? '');

        // Flatten roles whether passed as comma-separated string ('role:1,2,3') or multiple args ('role:1,2')
        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', (string) $r) as $subRole) {
                $trimmed = trim($subRole);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // Super Admin (Role 1) has universal access
        if ($primaryRoleId === '1' || $userRoleId === '1') {
            return $next($request);
        }

        if (!in_array($userRoleId, $allowedRoles) && !in_array($primaryRoleId, $allowedRoles)) {
            abort(403, 'Unauthorized access. You do not have the required permissions.');
        }

        return $next($request);
    }
}
