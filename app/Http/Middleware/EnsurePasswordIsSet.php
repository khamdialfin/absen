<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsSet
{
    /**
     * Cek password dan profil sebelum akses dashboard.
     * Flow: password dulu → profil → dashboard
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Cek password dulu
            if (!$user->hasPassword()) {
                return redirect()->route('password.setup');
            }

            // Kemudian cek profil
            if (!$user->profile_completed) {
                return redirect()->route('profile.setup');
            }
        }

        return $next($request);
    }
}
