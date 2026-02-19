<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
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
            'user_id'   => auth()->id(),
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
        $attendances = auth()->user()->attendances()
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

        // Cek status sesi dari cache
        $morningActive   = Cache::get('session:morning:' . $today, false);
        $afternoonActive = Cache::get('session:afternoon:' . $today, false);

        // Data siswa yang sudah scan hari ini
        $todayAttendances = Attendance::with('user')
            ->whereDate('date', $today)
            ->orderByDesc('created_at')
            ->get();

        // Jumlah total siswa (untuk progress)
        $totalSiswa = User::where('role', 'siswa')->count();

        return view('sekertaris.scan', compact(
            'morningActive',
            'afternoonActive',
            'todayAttendances',
            'totalSiswa'
        ));
    }

    /**
     * Mulai sesi absensi.
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'session' => 'required|in:morning,afternoon',
        ]);

        $today = now()->toDateString();
        $sessionKey = 'session:' . $request->session . ':' . $today;

        Cache::put($sessionKey, true, now()->endOfDay());

        $label = $request->session === 'morning' ? 'Pagi' : 'Sore';

        return response()->json([
            'success' => true,
            'message' => 'Sesi ' . $label . ' dibuka! Siswa bisa mulai scan QR.',
        ]);
    }

    /**
     * Tutup sesi absensi.
     * Siswa yang belum scan otomatis di-mark alpha.
     */
    public function stopSession(Request $request)
    {
        $request->validate([
            'session' => 'required|in:morning,afternoon',
        ]);

        $today = now()->toDateString();
        $sessionKey = 'session:' . $request->session . ':' . $today;

        // Tutup sesi
        Cache::forget($sessionKey);

        if ($request->session === 'morning') {
            // Ambil semua siswa yang BELUM absen hari ini
            $absentStudents = User::where('role', 'siswa')
                ->whereDoesntHave('attendances', function ($q) use ($today) {
                    $q->whereDate('date', $today);
                })
                ->get();

            // Buat record alpha untuk siswa yang belum absen
            foreach ($absentStudents as $student) {
                Attendance::create([
                    'user_id' => $student->id,
                    'date'    => $today,
                    'time_in' => null,
                    'status'  => 'alfa',
                ]);
            }

            $label = 'Pagi';
            $message = 'Sesi Pagi ditutup. ' . $absentStudents->count() . ' siswa tidak hadir (alpha).';
        } else {
            $label = 'Sore';
            $message = 'Sesi Sore ditutup.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'absent_count' => $request->session === 'morning' ? ($absentStudents->count() ?? 0) : 0,
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

            $lateThreshold = config('school.session.late_threshold', '08:00:00');

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date'    => $today,
                'time_in' => $currentTime,
                'status'  => $currentTime > $lateThreshold ? 'terlambat' : 'hadir',
            ]);

            Cache::forget('qr_token:' . $token);

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

    // ═══════════════════════════════════════════════════════════════
    // SEKERTARIS: Input Manual (Izin / Sakit)
    // ═══════════════════════════════════════════════════════════════

    /**
     * Form input manual untuk izin/sakit.
     */
    public function manualForm()
    {
        $students = User::where('role', 'siswa')
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
        $query = Attendance::with('user')->orderBy('date', 'desc')->orderBy('created_at', 'desc');
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
        $query = Attendance::with('user')->orderBy('date', 'desc')->orderBy('created_at', 'desc');
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
        $query = Attendance::with('user')->whereNotNull('time_in')->orderBy('date', 'asc')->orderBy('time_in', 'asc');
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
        $query = Attendance::with('user')->whereNotNull('time_out')->orderBy('date', 'asc')->orderBy('time_out', 'asc');
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
        $query = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->select('attendances.*')
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
