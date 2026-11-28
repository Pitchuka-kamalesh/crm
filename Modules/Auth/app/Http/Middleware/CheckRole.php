<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Symfony\Component\HttpFoundation\Response;
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user    = Auth::user();
        $allowed = array_map(fn ($r) => (int) trim($r), $roles);

        // Same role the menus/controllers use (session role switch + roles_assigned + login.role_id)
        $activeRole = (int) $user->getActiveRoleId();

        if (!in_array($activeRole, $allowed, true)) {
            Log::warning('[CheckRole] 403', [
                'usercode'    => $user->usercode,
                'active_role' => $activeRole,
                'login_role'  => $user->role_id,
                'allowed'     => $allowed,
                'path'        => $request->path(),
            ]);
            abort(403, 'Unauthorized access. You do not have the required permissions.');
        }

        return $next($request);
    }
}
