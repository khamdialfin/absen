<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManageUsersController extends Controller
{
    /**
     * Tampilkan list siswa & sekretaris di kelas walikelas.
     */
    public function index()
    {
        $kelas = auth()->user()->kelas;

        $students = User::where('kelas', $kelas)
            ->where('role', 'siswa')
            ->orderBy('name')
            ->get();

        $sekretaris = User::where('kelas', $kelas)
            ->where('role', 'sekertaris')
            ->orderBy('name')
            ->get();

        $bendahara = User::where('kelas', $kelas)
            ->where('role', 'bendahara')
            ->orderBy('name')
            ->get();

        return view('walikelas.manage-users', compact('students', 'sekretaris', 'bendahara', 'kelas'));
    }

    /**
     * Hapus akun siswa/sekretaris dari kelas.
     */
    public function destroy(User $user)
    {
        $kelas = auth()->user()->kelas;

        // Hanya bisa hapus siswa/sekretaris dari kelas sendiri
        if ($user->kelas !== $kelas) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus akun ini.');
        }

        if (!in_array($user->role, ['siswa', 'sekertaris', 'bendahara'])) {
            return back()->with('error', 'Hanya akun siswa, sekretaris, atau bendahara yang bisa dihapus.');
        }

        $name = $user->name;

        // Hapus attendance records terkait
        $user->attendances()->delete();
        $user->delete();

        return back()->with('success', "Akun {$name} berhasil dihapus.");
    }

    /**
     * Update kelas siswa (pindah kelas / koreksi).
     */
    public function updateKelas(Request $request, User $user)
    {
        $request->validate([
            'kelas' => 'required|string|max:50',
        ]);

        $kelas = auth()->user()->kelas;

        if ($user->kelas !== $kelas) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah akun ini.');
        }

        $user->update(['kelas' => $request->kelas]);

        return back()->with('success', "Kelas {$user->name} berhasil diubah ke {$request->kelas}.");
    }

    /**
     * Buat akun sekretaris atau bendahara baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:sekertaris,bendahara',
        ], [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'role.required'     => 'Role wajib dipilih.',
        ]);

        $kelas = auth()->user()->kelas;

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => $request->role,
            'kelas'             => $kelas,
            'email_verified_at' => now(),
            'profile_completed' => true,
        ]);

        $roleName = $request->role === 'sekertaris' ? 'Sekretaris' : 'Bendahara';

        return back()->with('success', "Akun {$roleName} ({$request->name}) berhasil dibuat.");
    }
}
