<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Kasir;
use App\Models\Owner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatePosUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $model = match ($request->session()->get('pos_role')) {
            'admin' => Admin::class,
            'kasir' => Kasir::class,
            'owner' => Owner::class,
            default => null,
        };

        if ($model === null || ! $model::whereKey($request->session()->get('pos_user_id'))->exists()) {
            $request->session()->forget(['pos_role', 'pos_user_id']);

            return redirect()->route('login');
        }

        return $next($request);
    }
}
