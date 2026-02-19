<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Login dengan email & password (untuk walikelas & sekertaris).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = auth()->user();

            // Hanya walikelas dan sekertaris yang bisa login via email
            if ($user->isSiswa()) {
                Auth::logout();
                return back()->with('error', 'Siswa harus login menggunakan Google.');
            }

            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Email atau password salah.');
    }

    /**
     * Tampilkan halaman register.
     */
    public function showRegister()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
