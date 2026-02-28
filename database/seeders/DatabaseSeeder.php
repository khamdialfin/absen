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
        // ─── XII RPL 1 Staff ─────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'walikelas@sekolah.test'],
            [
                'name'     => 'Walikelas Demo',
                'password' => Hash::make('password'),
                'role'     => 'walikelas',
                'kelas'    => 'XII RPL 1',
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
                'kelas'    => 'XII RPL 1',
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
                'kelas'    => 'XII RPL 1',
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
                'kelas'    => 'XII RPL 1',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );

        // ─── XII TKJ 1 Staff ─────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'walikelas.tkj1@sekolah.test'],
            [
                'name'     => 'Walikelas XII TKJ 1',
                'password' => Hash::make('password'),
                'role'     => 'walikelas',
                'kelas'    => 'XII TKJ 1',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'sekertaris.tkj1@sekolah.test'],
            [
                'name'     => 'Sekertaris XII TKJ 1',
                'password' => Hash::make('password'),
                'role'     => 'sekertaris',
                'kelas'    => 'XII TKJ 1',
                'email_verified_at' => now(),
                'profile_completed' => true,
            ]
        );

        // ─── Demo Siswa XII RPL 1 ────────────────────────────────
        foreach (['Ahmad Rizky', 'Budi Santoso', 'Citra Dewi'] as $i => $name) {
            User::firstOrCreate(
                ['email' => 'siswa.rpl' . ($i + 1) . '@sekolah.test'],
                [
                    'name'     => $name,
                    'password' => Hash::make('password'),
                    'role'     => 'siswa',
                    'kelas'    => 'XII RPL 1',
                    'nis'      => '2024' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                    'email_verified_at' => now(),
                    'profile_completed' => true,
                ]
            );
        }

        // ─── Demo Siswa XII TKJ 1 ────────────────────────────────
        foreach (['Dani Prasetyo', 'Eka Fitriani', 'Fajar Hidayat'] as $i => $name) {
            User::firstOrCreate(
                ['email' => 'siswa.tkj' . ($i + 1) . '@sekolah.test'],
                [
                    'name'     => $name,
                    'password' => Hash::make('password'),
                    'role'     => 'siswa',
                    'kelas'    => 'XII TKJ 1',
                    'nis'      => '2024' . str_pad($i + 4, 3, '0', STR_PAD_LEFT),
                    'email_verified_at' => now(),
                    'profile_completed' => true,
                ]
            );
        }
    }
}
