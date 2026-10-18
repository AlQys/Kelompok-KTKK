<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Kasir;
use App\Models\Owner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (session()->has(['pos_role', 'pos_user_id'])) {
            return to_route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        foreach ([
            'admin' => Admin::class,
            'kasir' => Kasir::class,
            'owner' => Owner::class,
        ] as $role => $model) {
            $user = $model::where('username', $credentials['username'])->first();

            if ($user !== null && is_string($user->password) && $user->password !== ''
                && Hash::check($credentials['password'], $user->password)) {
                $request->session()->regenerate();
                $request->session()->put([
                    'pos_role' => $role,
                    'pos_user_id' => $user->getKey(),
                ]);

                return to_route('dashboard');
            }
        }

        throw ValidationException::withMessages([
            'username' => 'Username atau password tidak sesuai.',
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
