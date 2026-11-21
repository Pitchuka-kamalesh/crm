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
    public function handle(Request $request, Closure $next, string $roles)
    {
          if (!Auth::check()) {
            return redirect()->route('login');
        }
         $allowedRoles = explode(',', $roles);

        if (!in_array((string) Auth::user()->role_id, $allowedRoles)) {
            abort(403, 'Unauthorized access. You do not have the required permissions.');
        }

        // return $next($request);
        return $next($request);
    }
}
