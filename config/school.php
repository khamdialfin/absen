<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Koordinat Kelas / Sekolah
    |--------------------------------------------------------------------------
    |
    | Latitude & Longitude lokasi sekolah untuk validasi radius geolokasi.
    | Siswa harus berada dalam radius tertentu agar QR Code bisa di-generate.
    |
    */

    'latitude'  => env('SCHOOL_LATITUDE', -6.9175),
    'longitude' => env('SCHOOL_LONGITUDE', 110.4203),

    /*
    |--------------------------------------------------------------------------
    | Radius Validasi (meter)
    |--------------------------------------------------------------------------
    |
    | Jarak maksimum (dalam meter) antara siswa dan koordinat sekolah
    | agar QR Code bisa ditampilkan.
    |
    */

    'radius' => env('SCHOOL_RADIUS', 5),

    /*
    |--------------------------------------------------------------------------
    | Jadwal Sesi Absensi
    |--------------------------------------------------------------------------
    |
    | Jam operasional untuk sesi pagi (check-in) dan sore (check-out).
    |
    */

    'session' => [
        'morning' => [
            'start' => '00:00',
            'end'   => '23:59',
        ],
        'afternoon' => [
            'start' => '00:00',
            'end'   => '23:59',
        ],
        // Batas waktu terlambat (siswa yang absen setelah jam ini = terlambat)
        'late_threshold' => '08:00:00',
    ],
];
