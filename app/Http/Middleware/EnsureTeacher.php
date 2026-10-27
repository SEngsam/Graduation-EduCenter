<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class EnsureTeacher
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! ($user->teacher || $user->hasRole(Role::ROLE_ADMIN))) {
            abort(403);
        }

        return $next($request);
    }
}
