<?php

namespace App\Http\Controllers;

use App\Models\ParentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        // Ambil data orang tua jika user adalah siswa
        $parentProfile = null;
        if ($user->isSiswa()) {
            $parentProfile = ParentProfile::where('user_id', $user->id)->first();
        }
        
        return view('profile.show', [
            'user' => $user,
            'parentProfile' => $parentProfile, // Tambahkan ini!
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Email updates disabled for now to prevent login issues with Google, 
            // but can be enabled if needed.
            // 'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:1024'], // 1MB Max
        ]);

        $user->name = $validated['name'];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists and not a default/external URL
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = Storage::url($path);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            // avatar is stored as a URL like /storage/avatars/xxx.jpg, 
            // extract the relative path for the public disk
            $path = str_replace('/storage/', '', $user->avatar);
            Storage::disk('public')->delete($path);
        }

        $user->avatar = null;
        $user->save();

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

     /**
     * Update data orang tua dari halaman profile.
     */
    public function updateParent(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isSiswa()) {
            return redirect()->route('profile.show')
                ->with('error', 'Anda tidak memiliki akses ke fitur ini.');
        }

        // Bersihkan nomor WA
        $cleanPhone = $this->cleanPhoneNumber($request->parent_phone);

        $validated = $request->validate([
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'regex:/^62[0-9]{9,13}$/'],
            'parent_address' => ['required', 'string', 'max:500'],
        ]);

        ParentProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $validated['parent_name'],
                'phone' => $cleanPhone,
                'address' => $validated['parent_address'],
            ]
        );

        return redirect()->route('profile.show')
            ->with('success', 'Data orang tua berhasil diperbarui!');
    }

    /**
     * Helper untuk membersihkan nomor telepon.
     */
    private function cleanPhoneNumber($phone)
    {
        // Hapus semua karakter kecuali angka
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) === '8') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }
}
