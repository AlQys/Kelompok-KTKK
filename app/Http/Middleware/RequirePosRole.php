<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePosRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless(in_array($request->session()->get('pos_role'), $roles, true), 403);

        return $next($request);
    }
}
