<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleAccess
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || empty($roles) || !$user->hasAnyRole($roles)) {
            abort(403, 'Ban khong co quyen truy cap trang nay.');
        }

        return $next($request);
    }
}
