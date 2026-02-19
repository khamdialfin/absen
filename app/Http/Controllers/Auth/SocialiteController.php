<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect user ke halaman login Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google.
     *
     * Flow:
     * 1. Ambil data user dari Google
     * 2. Cari / buat user di database
     * 3. Login user
     * 4. Cek password:
     *    - Null  → redirect ke form set password
     *    - Exist → redirect ke dashboard
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        // Cari user berdasarkan google_id atau email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id & avatar jika belum ada
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        } else {
            // Buat user baru (role default: siswa, password: null)
            $user = User::create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'role'      => 'siswa',
                'password'  => null,
                'email_verified_at' => now(),
            ]);
        }

        // Login user
        Auth::login($user, true);

        // Cek apakah profil sudah lengkap
        if (!$user->profile_completed) {
            return redirect()->route('password.setup');
        }

        return redirect()->route('dashboard');
    }
}
