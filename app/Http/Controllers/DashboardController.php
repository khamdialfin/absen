<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BendaharaController;

class DashboardController extends Controller
{
    /**
     * Redirect user ke dashboard sesuai role.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        return match ($user->role) {
            'walikelas'  => view('dashboard.walikelas', $this->walkelasData()),
            'sekertaris' => view('dashboard.sekertaris', $this->sekertarisData()),
            'bendahara'  => app(BendaharaController::class)->index(),
            'siswa'      => view('dashboard.siswa', $this->siswaData($user)),
            default      => abort(403),
        };
    }

    /**
     * Data untuk dashboard Walikelas.
     */
    private function walkelasData(): array
    {
        $today = now()->toDateString();
        $kelas = auth()->user()->kelas;
        $totalSiswa = \App\Models\User::where('role', 'siswa')->where('kelas', $kelas)->count();
        
        // Ambil statistik hari ini (hanya kelas sendiri)
        $kelasFilter = fn($q) => $q->whereHas('user', fn($u) => $u->where('kelas', $kelas));
        $stats = [
            'hadir'     => \App\Models\Attendance::where('date', $today)->where('status', 'hadir')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
            'terlambat' => \App\Models\Attendance::where('date', $today)->where('status', 'terlambat')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
            'izin'      => \App\Models\Attendance::where('date', $today)->where('status', 'izin')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
            'sakit'     => \App\Models\Attendance::where('date', $today)->where('status', 'sakit')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
            'alfa'      => \App\Models\Attendance::where('date', $today)->where('status', 'alfa')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
        ];
        
        // Aktivitas terbaru (5 record terakhir hari ini, kelas sendiri)
        $recentActivities = \App\Models\Attendance::with('user')
            ->where('date', $today)
            ->whereHas('user', fn($q) => $q->where('kelas', $kelas))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return compact('totalSiswa', 'stats', 'recentActivities');
    }

    /**
     * Data untuk dashboard Sekertaris.
     */
    private function sekertarisData(): array
    {
        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');
        $kelas = auth()->user()->kelas;

        // Tentukan sesi aktif
        $morningStart = config('school.session.morning.start');
        $morningEnd   = config('school.session.morning.end');
        $afternoonStart = config('school.session.afternoon.start');
        $afternoonEnd   = config('school.session.afternoon.end');

        $isMorningSession = $currentTime >= $morningStart && $currentTime <= $morningEnd;
        $isAfternoonSession = $currentTime >= $afternoonStart && $currentTime <= $afternoonEnd;

        // Statistik (hanya kelas sendiri)
        $totalSiswa = \App\Models\User::where('role', 'siswa')->where('kelas', $kelas)->count();
        $stats = [
            'hadir'     => \App\Models\Attendance::where('date', $today)->where('status', 'hadir')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
            'terlambat' => \App\Models\Attendance::where('date', $today)->where('status', 'terlambat')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
            'izin'      => \App\Models\Attendance::where('date', $today)->where('status', 'izin')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
            'sakit'     => \App\Models\Attendance::where('date', $today)->where('status', 'sakit')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
            'alfa'      => \App\Models\Attendance::where('date', $today)->where('status', 'alfa')->whereHas('user', fn($q) => $q->where('kelas', $kelas))->count(),
        ];
        
        // Aktivitas terbaru (kelas sendiri)
        $recentActivities = \App\Models\Attendance::with('user')
            ->where('date', $today)
            ->whereHas('user', fn($q) => $q->where('kelas', $kelas))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return compact('isMorningSession', 'isAfternoonSession', 'currentTime', 'totalSiswa', 'stats', 'recentActivities');
    }

    /**
     * Data untuk dashboard Siswa.
     */
    private function siswaData($user): array
    {
        $todayAttendance = $user->attendances()
            ->where('date', now()->toDateString())
            ->first();

        $recentAttendances = $user->attendances()
            ->orderByDesc('date')
            ->limit(7)
            ->get();

        $schoolLat = config('school.latitude');
        $schoolLng = config('school.longitude');
        $schoolRadius = config('school.radius');

        return compact('todayAttendance', 'recentAttendances', 'schoolLat', 'schoolLng', 'schoolRadius');
    }
}
