<?php

namespace App\Common\Traits;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Models\User;

trait RequiresLogin
{
    /**
     * Require authenticated user, redirecting to login if unauthenticated.
     */
    protected function requireLogin(): User
    {
        if (Auth::check()) {
            return Auth::user();
        }

        if (!Session::has('user.usercode') && !Session::has('user.phnno')) {
            abort(redirect()->route('login'));
        }

        $user = null;
        if (Session::has('user.usercode')) {
            $user = User::where('usercode', Session::get('user.usercode'))->first();
        } elseif (Session::has('user.phnno')) {
            $user = User::where('phnno', Session::get('user.phnno'))->first();
        }

        if (!$user) {
            abort(redirect()->route('login'));
        }

        return $user;
    }
}

