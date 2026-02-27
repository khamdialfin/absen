<?php
// app/Http/Controllers/Auth/ParentController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ParentProfile;

class ParentController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        
        // Hanya siswa yang bisa akses
        if (!$user->isSiswa()) {
            return redirect()->route('dashboard');
        }
        
        // Cek apakah sudah mengisi data orang tua
        $parentProfile = ParentProfile::where('user_id', $user->id)->first();
        
        // Jika sudah mengisi dan profile completed, langsung ke dashboard
        if ($user->profile_completed && $parentProfile) {
            return redirect()->route('dashboard');
        }

        return view('auth.parent', compact('user', 'parentProfile'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Hanya siswa yang bisa akses
        if (!$user->isSiswa()) {
            return redirect()->route('dashboard');
        }

        $cleanPhone = $this->cleanPhoneNumber($request->no_wa);
        $request->merge(['no_wa' => $cleanPhone]);


        $validated = $request->validate([
            'nama_ortu' => ['required', 'string', 'max:255'],
            'no_wa'     => ['required', 'string','regex:/^62[0-9]{9,13}$/'],
            'alamat'    => ['required', 'string', 'max:500'],
        ]);

        // Simpan atau update data orang tua
        ParentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $validated['nama_ortu'],
                'phone' => $validated['no_wa'],
                'address' => $validated['alamat'],
            ]
        );

        // Tandai profile sudah lengkap
        $user->update([
            'profile_completed' => true,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Data orang tua berhasil disimpan! Selamat datang di dashboard.');
    }

    /**
     * Helper untuk membersihkan nomor telepon.
     */
    private function cleanPhoneNumber($phone)
    {
        // Hapus semua karakter kecuali angka
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Jika diawali 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika diawali 8 (tanpa 62), tambahkan 62
        if (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}