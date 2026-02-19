<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileSetupController extends Controller
{
    /**
     * Tampilkan form setup profil.
     */
    public function show()
    {
        $user = auth()->user();

        // Harus set password dulu
        if (!$user->hasPassword()) {
            return redirect()->route('password.setup');
        }

        // Jika profil sudah lengkap, ke dashboard
        if ($user->profile_completed) {
            return redirect()->route('dashboard');
        }

        return view('auth.setup-profile', compact('user'));
    }

    /**
     * Simpan data profil.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'kelas'    => ['required', 'string', 'max:20'],
            'no_absen' => ['required', 'string', 'max:10'],
            'gender'   => ['required', 'in:L,P'],
            'nis'      => ['required', 'string', 'max:30', 'unique:users,nis,' . $user->id],
        ]);

        $user->update([
            'name'              => $validated['name'],
            'kelas'             => $validated['kelas'],
            'no_absen'          => $validated['no_absen'],
            'gender'            => $validated['gender'],
            'nis'               => $validated['nis'],
            'profile_completed' => true,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Profil berhasil disimpan! Selamat datang, ' . $user->name);
    }
}
