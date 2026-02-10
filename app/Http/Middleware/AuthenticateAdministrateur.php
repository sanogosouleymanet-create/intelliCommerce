<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateAdministrateur
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('administrateur')->check()) {
            return redirect()->route('admin.login');
        }
        return $next($request);
    }
}
