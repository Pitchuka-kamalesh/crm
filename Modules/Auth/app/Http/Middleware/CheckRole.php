<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Supports both variadic arguments ('role:1,2,3') and single comma/pipe-separated string.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 1. Parse and flatten allowed roles (handles role:1,2,3 or role:1 or array of args)
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (preg_split('/[,|]/', (string) $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }
        $allowedRoles = array_values(array_unique($allowedRoles));

        // 2. Get active role ID from method or session
        $activeRoleId = (string) (method_exists($user, 'getActiveRoleId')
            ? $user->getActiveRoleId()
            : (session('user.role_id') ?? $user->role_id ?? ''));

        // 3. Get all assigned role IDs for employees (from master.roles_assigned)
        $assignedRoleIds = method_exists($user, 'getAssignedRoleIds')
            ? array_map('strval', $user->getAssignedRoleIds())
            : [];

        // 4. Direct role_id from table
        $directRoleId = $user->role_id !== null ? (string) $user->role_id : '';

        // 5. Allow access if active role, direct role, or any assigned role matches allowed roles
        $hasAccess = in_array($activeRoleId, $allowedRoles, true)
            || ($directRoleId !== '' && in_array($directRoleId, $allowedRoles, true))
            || !empty(array_intersect($assignedRoleIds, $allowedRoles));

        // 6. Fallback: If user is an employee (user_type === 2) and route allows staff/incharge/dean (1, 2, 3)
        if (!$hasAccess && (int) $user->user_type === 2 && !empty(array_intersect(['1', '2', '3'], $allowedRoles))) {
            if (in_array('1', $assignedRoleIds, true)
                || in_array('2', $assignedRoleIds, true)
                || in_array('3', $assignedRoleIds, true)
                || (int) $user->role_id === 1) {
                $hasAccess = true;
            }
        }

        if (!$hasAccess) {
            Log::warning('[CheckRole 403 Forbidden]', [
                'usercode'     => $user->usercode,
                'active_role'  => $activeRoleId,
                'direct_role'  => $directRoleId,
                'assigned'     => $assignedRoleIds,
                'allowed'      => $allowedRoles,
                'path'         => $request->path(),
                'ip'           => $request->ip(),
            ]);

            abort(403, 'Unauthorized access. You do not have the required permissions.');
        }

        return $next($request);
    }
}
