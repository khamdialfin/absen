<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSchedule;
use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // SISWA: Generate QR Code
    // ═══════════════════════════════════════════════════════════════

    /**
     * Halaman generate QR Code untuk siswa.
     * QR berisi encrypted string: user_id|timestamp
     */
    public function generateQr(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $schoolLat    = config('school.latitude');
        $schoolLng    = config('school.longitude');
        $schoolRadius = config('school.radius');

        return view('siswa.qr', compact('user', 'schoolLat', 'schoolLng', 'schoolRadius'));
    }

    /**
     * API endpoint: Generate QR code data (encrypted).
     * Dipanggil via AJAX setelah validasi lokasi di client-side.
     */
    public function generateQrData(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Validasi jarak dengan Haversine Formula
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            config('school.latitude'),
            config('school.longitude')
        );

        if ($distance > config('school.radius')) {
            return response()->json([
                'success'  => false,
                'message'  => 'Anda berada di luar jangkauan kelas (' . round($distance, 1) . 'm).',
                'distance' => round($distance, 1),
            ], 422);
        }

        // Generate short token dan simpan di cache (5 menit)
        $token = strtoupper(Str::random(8));
        Cache::put('qr_token:' . $token, [
            'user_id'   => /** @scrutinizer ignore-type */ auth()->id(),
            'timestamp' => now()->timestamp,
        ], now()->addMinutes(5));

        // Generate QR Code sebagai SVG (token pendek = QR simpel)
        $qrCode = QrCode::size(300)
            ->errorCorrection('H')
            ->generate($token);

        return response()->json([
            'success'  => true,
            'qr_svg'   => (string) $qrCode,
            'token'    => $token,
            'distance' => round($distance, 1),
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // SISWA: Riwayat Kehadiran
    // ═══════════════════════════════════════════════════════════════

    /**
     * Tampilkan riwayat kehadiran siswa.
     */
    public function riwayat()
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        $attendances = $authUser->attendances()
            ->orderByDesc('date')
            ->paginate(15);

        return view('siswa.riwayat', compact('attendances'));
    }

    // ═══════════════════════════════════════════════════════════════
    // SEKERTARIS: Scan QR Code
    // ═══════════════════════════════════════════════════════════════

    /**
     * Halaman scanner QR Code.
     */
    public function scanPage()
    {
        $now = now();
        $today = $now->toDateString();
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        $kelas = $authUser->kelas;

        // Cek status sesi dari jadwal di database
        $schedule = AttendanceSchedule::where('kelas', $kelas)->first();
        $morningActive   = $schedule ? $schedule->isMorningActive() : false;
        $afternoonActive = $schedule ? $schedule->isAfternoonActive() : false;

        // Data siswa yang sudah scan hari ini (hanya kelas sendiri)
        $todayAttendances = Attendance::with('user')
            ->whereDate('date', $today)
            ->whereHas('user', fn($q) => $q->where('kelas', $kelas))
            ->orderByDesc('created_at')
            ->get();

        // Jumlah total siswa kelas ini (untuk progress)
        $totalSiswa = User::where('role', 'siswa')->where('kelas', $kelas)->count();

        return view('sekertaris.scan', compact(
            'morningActive',
            'afternoonActive',
            'todayAttendances',
            'totalSiswa',
            'schedule'
        ));
    }

    /**
     * Form pengaturan jadwal presensi (Wali Kelas).
     */
    public function scheduleForm()
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        $kelas = $authUser->kelas;

        $schedule = AttendanceSchedule::where('kelas', $kelas)->first();

        return view('walikelas.schedule', compact('schedule', 'kelas'));
    }

    /**
     * Simpan jadwal presensi (Wali Kelas).
     */
    public function saveSchedule(Request $request)
    {
        $request->validate([
            'morning_start'    => 'required|date_format:H:i',
            'morning_end'      => 'required|date_format:H:i|after:morning_start',
            'afternoon_start'  => 'required|date_format:H:i',
            'afternoon_end'    => 'required|date_format:H:i|after:afternoon_start',
            'late_threshold'   => 'required|date_format:H:i',
        ], [
            'morning_end.after'    => 'Jam selesai pagi harus setelah jam mulai pagi.',
            'afternoon_end.after'  => 'Jam selesai sore harus setelah jam mulai sore.',
        ]);

        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();

        AttendanceSchedule::updateOrCreate(
            ['kelas' => $authUser->kelas],
            [
                'morning_start'   => $request->morning_start,
                'morning_end'     => $request->morning_end,
                'afternoon_start' => $request->afternoon_start,
                'afternoon_end'   => $request->afternoon_end,
                'late_threshold'  => $request->late_threshold,
            ]
        );

        return back()->with('success', 'Jadwal presensi berhasil disimpan!');
    }

    /**
     * Tutup sesi pagi secara manual & mark alpha siswa yang belum absen.
     */
    public function closeSessionMorning()
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        $kelas = $authUser->kelas;
        $today = now()->toDateString();

        $absentStudents = User::where('role', 'siswa')
            ->where('kelas', $kelas)
            ->whereDoesntHave('attendances', function ($q) use ($today) {
                $q->whereDate('date', $today);
            })
            ->get();

        foreach ($absentStudents as $student) {
            Attendance::create([
                'user_id' => $student->id,
                'date'    => $today,
                'time_in' => null,
                'status'  => 'alfa',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Siswa yang belum absen telah di-mark ALFA. (' . $absentStudents->count() . ' siswa)',
            'absent_count' => $absentStudents->count(),
        ]);
    }

    /**
     * Proses scan QR Code.
     * Decrypt data QR → validasi → simpan/update attendance.
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
            'session' => 'required|in:morning,afternoon',
        ]);

        // Ambil data dari cache berdasarkan token QR
        $token = strtoupper(trim($request->qr_data));
        $data = Cache::get('qr_token:' . $token);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau sudah kadaluarsa. (Token: ' . $token . ')',
            ], 422);
        }

        // Validasi data QR
        if (!isset($data['user_id']) || !isset($data['timestamp'])) {
            return response()->json([
                'success' => false,
                'message' => 'Format QR Code tidak valid.',
            ], 422);
        }

        // Cari user
        $user = User::find($data['user_id']);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User dengan ID ' . $data['user_id'] . ' tidak ditemukan.',
            ], 422);
        }

        if (!$user->isSiswa()) {
            return response()->json([
                'success' => false,
                'message' => $user->name . ' bukan siswa (role: ' . $user->role . ').',
            ], 422);
        }

        // Validasi kelas: siswa harus dari kelas yang sama dengan sekretaris
        /** @var \App\Models\User $sekretaris */
        $sekretaris = auth()->user();
        if ($sekretaris->kelas && $user->kelas !== $sekretaris->kelas) {
            return response()->json([
                'success' => false,
                'message' => $user->name . ' bukan siswa kelas ' . $sekretaris->kelas . ' (kelas: ' . ($user->kelas ?? 'belum diset') . ').',
            ], 422);
        }

        // Token hanya dihapus setelah attendance berhasil disimpan

        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        if ($request->session === 'morning') {
            // Sesi Pagi: Check-in — cek apakah sudah absen hari ini
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if ($attendance) {
                return response()->json([
                    'success' => false,
                    'message' => $user->name . ' sudah melakukan absen pagi hari ini.',
                ], 422);
            }

            // Ambil late threshold dari schedule, fallback ke config
            $scheduleRecord = AttendanceSchedule::where('kelas', $sekretaris->kelas)->first();
            $lateThreshold = $scheduleRecord ? $scheduleRecord->late_threshold : config('school.session.late_threshold', '08:00:00');

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date'    => $today,
                'time_in' => $currentTime,
                'status'  => $currentTime > $lateThreshold ? 'terlambat' : 'hadir',
            ]);

            Cache::forget('qr_token:' . $token);

            // Kirim notifikasi WA ke orang tua
            $this->sendParentNotification($user, 'checkin', $currentTime, $attendance->status);

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil: ' . $user->name,
                'data'    => [
                    'name'    => $user->name,
                    'time_in' => $currentTime,
                    'status'  => $attendance->status,
                ],
            ]);
        } else {
            // Sesi Sore: Check-out
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => $user->name . ' belum melakukan absen pagi.',
                ], 422);
            }

            if ($attendance->time_out) {
                return response()->json([
                    'success' => false,
                    'message' => $user->name . ' sudah melakukan absen sore.',
                ], 422);
            }

            $attendance->update(['time_out' => $currentTime]);

            Cache::forget('qr_token:' . $token);

            // Kirim notifikasi WA ke orang tua
            $this->sendParentNotification($user, 'checkout', $currentTime, null, $attendance->time_in);

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil: ' . $user->name,
                'data'    => [
                    'name'     => $user->name,
                    'time_out' => $currentTime,
                ],
            ]);
        }
    }

    /**
     * Kirim notifikasi WhatsApp ke orang tua siswa via Fonnte.
     */
    private function sendParentNotification(User $student, string $type, string $time, ?string $status = null, ?string $timeIn = null): void
    {
        \Illuminate\Support\Facades\Log::info("WA Notif: called for {$student->name} (ID:{$student->id}), type={$type}");

        if (!config('services.fonnte.enabled')) {
            \Illuminate\Support\Facades\Log::info("WA Notif: SKIPPED - fonnte not enabled");
            return;
        }

        try {
            $parentProfile = $student->parentProfile;
            if (!$parentProfile || empty($parentProfile->phone)) {
                \Illuminate\Support\Facades\Log::info("WA Notif: SKIPPED - no parent profile/phone for {$student->name}");
                return;
            }

            \Illuminate\Support\Facades\Log::info("WA Notif: sending to {$parentProfile->phone} for {$student->name}");

            $fonnte = new FonnteService();

            if ($type === 'checkin') {
                $result = $fonnte->sendCheckInNotification(
                    $parentProfile->phone,
                    $student->name,
                    $time,
                    $status ?? 'hadir'
                );
            } else {
                $result = $fonnte->sendCheckOutNotification(
                    $parentProfile->phone,
                    $student->name,
                    $timeIn ?? '-',
                    $time
                );
            }

            \Illuminate\Support\Facades\Log::info("WA Notif: result=" . ($result ? 'SUCCESS' : 'FAILED'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WA Notification error: ' . $e->getMessage());
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // SEKERTARIS: Input Manual (Izin / Sakit)
    // ═══════════════════════════════════════════════════════════════

    /**
     * Form input manual untuk izin/sakit.
     */
    public function manualForm()
    {
        /** @var \App\Models\User $authUser */
        $authUser = auth()->user();
        $kelas = $authUser->kelas;
        $students = User::where('role', 'siswa')
            ->where('kelas', $kelas)
            ->orderBy('name')
            ->get();

        return view('sekertaris.manual', compact('students'));
    }

    /**
     * Simpan data izin/sakit manual.
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status'  => 'required|in:izin,sakit',
            'notes'   => 'nullable|string|max:255',
            'letter'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'user_id.required' => 'Siswa wajib dipilih.',
            'user_id.exists'   => 'Siswa tidak ditemukan.',
            'status.required'  => 'Status wajib dipilih.',
            'letter.mimes'     => 'File harus berformat JPG, PNG, atau PDF.',
            'letter.max'       => 'Ukuran file maksimal 2MB.',
        ]);

        $today = now()->toDateString();

        // Cek duplikat atau update
        // Menggunakan logic yang lebih robust untuk menemukan record hari ini
        $attendance = Attendance::where('user_id', $request->user_id)
            ->where(function ($query) use ($today) {
                $query->whereDate('date', $today)
                      ->orWhere('date', 'like', "$today%");
            })
            ->first();

        // Upload surat jika ada
        $letterPath = null;
        if ($request->hasFile('letter')) {
            $letterPath = $request->file('letter')->store('letters', 'public');
        }

        // Jika status Izin/Sakit/Alfa, reset jam masuk/pulang
        $shouldClearTime = in_array($request->status, ['izin', 'sakit', 'alfa']);
        $timeData = $shouldClearTime ? ['time_in' => null, 'time_out' => null] : [];

        if ($attendance) {
            // Update data existing
            $attendance->update(array_merge([
                'status'      => $request->status,
                'notes'       => $request->notes,
                'letter_file' => $letterPath ?? $attendance->letter_file,
            ], $timeData));

            $message = "Data kehadiran {$request->status} berhasil diperbarui.";
        } else {
            // Create baru
            Attendance::create(array_merge([
                'user_id'     => $request->user_id,
                'date'        => $today,
                'status'      => $request->status,
                'notes'       => $request->notes,
                'letter_file' => $letterPath,
            ], $timeData));

            $message = "Data {$request->status} berhasil disimpan.";
        }

        $studentName = User::find($request->user_id)->name;

        return back()->with('success', "{$message} (Siswa: {$studentName})");
    }

    // ═══════════════════════════════════════════════════════════════
    // HELPER: Haversine Distance Calculation
    // ═══════════════════════════════════════════════════════════════

    /**
     * Hitung jarak antara dua koordinat menggunakan Haversine Formula.
     * Return dalam meter.
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
    // ═══════════════════════════════════════════════════════════════
    // SEKERTARIS: Rekapan Absensi
    // ═══════════════════════════════════════════════════════════════

    /**
     * Halaman rekapan absensi harian.
     */
    private function applyFilter($query, Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', now()->toDateString());
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        if ($type == 'daily') {
            $query->whereDate('date', $date);
        } elseif ($type == 'monthly') {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        } elseif ($type == 'yearly') {
            $query->whereYear('date', $year);
        }
    }

    private function getFilterLabel(Request $request)
    {
        $type = $request->input('type', 'daily');
        $date = $request->input('date', now()->toDateString());
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        if ($type == 'daily') {
            return \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y');
        } elseif ($type == 'monthly') {
            return 'Bulan ' . \Carbon\Carbon::create(null, $month)->isoFormat('MMMM') . ' ' . $year;
        } elseif ($type == 'yearly') {
            return 'Tahun ' . $year;
        }
        return '';
    }

    /**
     * Tampilkan halaman rekap absensi.
     */
    public function recap(Request $request)
    {
        $kelas = auth()->user()->kelas;
        $query = Attendance::with('user')
            ->whereHas('user', fn($q) => $q->where('kelas', $kelas))
            ->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        $this->applyFilter($query, $request);
        $attendances = $query->get();

        $stats = [
            'hadir'     => $attendances->where('status', 'hadir')->count(),
            'terlambat' => $attendances->where('status', 'terlambat')->count(),
            'izin'      => $attendances->where('status', 'izin')->count(),
            'sakit'     => $attendances->where('status', 'sakit')->count(),
            'alfa'      => $attendances->where('status', 'alfa')->count(),
        ];
        
        $label = $this->getFilterLabel($request);

        return view('sekertaris.recap', compact('attendances', 'stats', 'label'));
    }

    /**
     * Export rekapan absensi ke CSV.
     */
    public function exportRecap(Request $request)
    {
        $kelas = auth()->user()->kelas;
        $query = Attendance::with('user')
            ->whereHas('user', fn($q) => $q->where('kelas', $kelas))
            ->orderBy('date', 'desc')->orderBy('created_at', 'desc');
        $this->applyFilter($query, $request);
        $attendances = $query->get();

        $label = $this->getFilterLabel($request);
        $csvFileName = 'rekap_absensi_' . Str::slug($label) . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Siswa', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterangan']);
            foreach ($attendances as $key => $row) {
                fputcsv($file, [
                    $key + 1,
                    $row->user->name,
                    $row->date,
                    $row->time_in,
                    $row->time_out,
                    ucfirst($row->status),
                    $row->notes
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Rekap khusus Masuk (Urut berdasarkan jam masuk).
     */
    public function recapMasuk(Request $request)
    {
        $kelas = auth()->user()->kelas;
        $query = Attendance::with('user')
            ->whereHas('user', fn($q) => $q->where('kelas', $kelas))
            ->whereNotNull('time_in')->orderBy('date', 'asc')->orderBy('time_in', 'asc');
        $this->applyFilter($query, $request);
        $attendances = $query->get();
        
        $label = $this->getFilterLabel($request);
        return view('sekertaris.recap_masuk', compact('attendances', 'label'));
    }

    /**
     * Rekap khusus Pulang (Urut berdasarkan jam pulang).
     */
    public function recapPulang(Request $request)
    {
        $kelas = auth()->user()->kelas;
        $query = Attendance::with('user')
            ->whereHas('user', fn($q) => $q->where('kelas', $kelas))
            ->whereNotNull('time_out')->orderBy('date', 'asc')->orderBy('time_out', 'asc');
        $this->applyFilter($query, $request);
        $attendances = $query->get();

        $label = $this->getFilterLabel($request);
        return view('sekertaris.recap_pulang', compact('attendances', 'label'));
    }

    /**
     * Laporan Gabungan Lengkap (Nama, NIS, Gender, dll).
     */
    public function reportCombined(Request $request)
    {
        $kelas = auth()->user()->kelas;
        $query = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->select('attendances.*')
            ->where('users.kelas', $kelas)
            ->with('user')
            ->orderBy('attendances.date', 'asc')
            ->orderBy('users.name', 'asc');
            
        // Apply filter manually because of join ambiguity if needed, but applyFilter uses 'date' column which is ambiguous?
        // applyFilter uses ->whereDate('date', ...). "date" column exists in attendances. 
        // But if strict mode, might need 'attendances.date'.
        // Let's modify applyFilter to be smarter or pass table name.
        // Actually, let's override logic here or update applyFilter.
        // Update applyFilter to accept column name or use 'attendances.date'.
        
        // Revised logic below:
        $type = $request->input('type', 'daily');
        $date = $request->input('date', now()->toDateString());
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        if ($type == 'daily') {
            $query->whereDate('attendances.date', $date);
        } elseif ($type == 'monthly') {
            $query->whereMonth('attendances.date', $month)->whereYear('attendances.date', $year);
        } elseif ($type == 'yearly') {
            $query->whereYear('attendances.date', $year);
        }

        $attendances = $query->get();
        $label = $this->getFilterLabel($request);
            
        return view('sekertaris.report_combined', compact('attendances', 'label'));
    }
}
