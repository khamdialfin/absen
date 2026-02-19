<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetPasswordController extends Controller
{
    /**
     * Tampilkan form set password.
     */
    public function show()
    {
        $user = auth()->user();

        // Jika sudah punya password, lanjut ke profil atau dashboard
        if ($user->hasPassword()) {
            if (!$user->profile_completed) {
                return redirect()->route('profile.setup');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.set-password');
    }

    /**
     * Simpan password.
     */
    public function store(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => $request->password,
        ]);

        return redirect()->route('profile.setup')
            ->with('success', 'Password berhasil disimpan! Sekarang lengkapi profil Anda.');
    }
}
