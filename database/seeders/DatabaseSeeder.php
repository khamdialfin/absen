<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── Default Users untuk Testing ──────────────────────────

        User::firstOrCreate(
            ['email' => 'walikelas@sekolah.test'],
            [
                'name'     => 'Walikelas Demo',
                'password' => Hash::make('password'),
                'role'     => 'walikelas',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'sekertaris@sekolah.test'],
            [
                'name'     => 'Sekertaris Demo',
                'password' => Hash::make('password'),
                'role'     => 'sekertaris',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'siswa@sekolah.test'],
            [
                'name'     => 'Siswa Demo',
                'password' => Hash::make('password'),
                'role'     => 'siswa',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'bendahara@sekolah.test'],
            [
                'name'     => 'Bendahara Kelas',
                'password' => Hash::make('Password'),
                'role'     => 'bendahara',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );
    }
}
