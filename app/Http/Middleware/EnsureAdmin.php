<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        abort_unless(
            $user && $user->is_active && in_array($user->role, ['super_admin', 'registry'], true),
            403
        );

        return $next($request);
    }
}
