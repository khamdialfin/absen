<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parent;

class ParentController extends Controller
{
    public function show(Request $request)
    {
        // hanya siswa
        abort_if($request->user()->role !== 'siswa', 403);

        return view('profile.parent');
    }

    public function store(Request $request)
    {
        abort_if($request->user()->role !== 'siswa', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'regex:/^628[0-9]{8,13}$/'],
            'addres'     => ['required', 'string', 'max:255'],
        ]);

        Parent::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        // tandai profile selesai
        $request->user()->update([
            'profile_completed' => true,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Profil lengkap. Terima kasih.');
    }
}
