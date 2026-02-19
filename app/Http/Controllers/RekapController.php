<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    /**
     * Halaman rekapitulasi kehadiran (Walikelas).
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'daily');
        $date = $request->get('date', now()->toDateString());
        $week = $request->get('week', now()->format('Y-\WW')); // Format: 2024-W05
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        // Ambil semua siswa
        $students = User::where('role', 'siswa')
            ->orderBy('name')
            ->get();

        $label = '';
        $rekap = collect();
        $isDaily = ($type === 'daily');

        // Tentukan Range Tanggal & Label
        if ($type === 'daily') {
            $startDate = \Carbon\Carbon::parse($date)->startOfDay();
            $endDate   = $startDate->copy()->endOfDay();
            $label     = $startDate->translatedFormat('l, d F Y');
        } elseif ($type === 'weekly') {
            // Parse YYYY-Www using ISO-8601
            $startDate = \Carbon\Carbon::parse($week)->startOfWeek();
            $endDate   = $startDate->copy()->endOfWeek();
            $label     = "Minggu ke-" . $startDate->weekOfYear . " (" . $startDate->format('d M') . " - " . $endDate->format('d M Y') . ")";
        } elseif ($type === 'monthly') {
            $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $endDate   = $startDate->copy()->endOfMonth();
            $label     = $startDate->translatedFormat('F Y');
        } else { // yearly
            $startDate = \Carbon\Carbon::create($year, 1, 1)->startOfYear();
            $endDate   = $startDate->copy()->endOfYear();
            $label     = "Tahun " . $year;
        }

        // Ambil data attendance dalam range
        $attendances = Attendance::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy('user_id');

        if ($isDaily) {
            // Format Harian: Detail per siswa
            $rekap = $students->map(function ($student) use ($attendances, $startDate) {
                // Ambil attendance spesifik hari ini (seharusnya cuma 1 per user)
                $attendance = $attendances->get($student->id)?->first();

                // Logic status: Jika ada record -> statusnya. Jika tidak -> Alfa (kecuali hari libur/masa depan?)
                // Untuk simplifikasi, jika tidak ada record di hari kerja yg sudah lewat -> Alfa/Belum Absen
                $status = $attendance ? $attendance->status : 'alfa';
                
                // Cek jika hari ini belum lewat/sedang berjalan, mungkin statusnya 'Belum Absen' visualnya saja
                if (!$attendance && $startDate->isFuture()) {
                    $status = '-';
                }

                return [
                    'student'   => $student,
                    'time_in'   => $attendance?->time_in ?? '-',
                    'time_out'  => $attendance?->time_out ?? '-',
                    'status'    => $status,
                ];
            });
        } else {
            // Format Periode: Ringkasan per siswa
            // Hitung hari kerja
            $workingDays = 0;
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $dt) {
                if ($dt->isWeekday() && $dt->lte(now())) { // Hitung hanya hari yg sudah lewat/hari ini
                    $workingDays++;
                }
            }
            // Batasi workingDays minimal 1 untuk hindari division by zero kalau range di masa depan
            $workingDays = max(1, $workingDays);

            $rekap = $students->map(function ($student) use ($attendances, $workingDays) {
                $studentAttendances = $attendances->get($student->id, collect());

                $hadir      = $studentAttendances->where('status', 'hadir')->count();
                $terlambat  = $studentAttendances->where('status', 'terlambat')->count();
                $izin       = $studentAttendances->where('status', 'izin')->count();
                $sakit      = $studentAttendances->where('status', 'sakit')->count();
                $alfa       = $studentAttendances->where('status', 'alfa')->count();
                
                // Hitung alfa implisit? (Hari kerja - total recorded). 
                // Saat ini sistem mencatat 'alfa' jika cron jalan. 
                // Jika manual input, 'status' diisi.
                // Kita gunakan data recorded saja agar konsisten dengan DB.

                return [
                    'student'      => $student,
                    'hadir'        => $hadir,
                    'terlambat'    => $terlambat,
                    'izin'         => $izin,
                    'sakit'        => $sakit,
                    'alfa'         => $alfa,
                    'total_hadir'  => $hadir + $terlambat,
                    'persentase'   => round(($hadir + $terlambat) / $workingDays * 100, 1),
                ];
            });
        }

        return view('walikelas.rekap', compact('rekap', 'type', 'label', 'isDaily'));
    }

    /**
     * Export rekap ke CSV.
     */
    public function export(Request $request)
    {
        // Reuse logic index (ideally extract service/helper, but copying for speed/simplicity here)
        $type = $request->get('type', 'daily');
        $date = $request->get('date', now()->toDateString());
        $week = $request->get('week', now()->format('Y-\WW'));
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $students = User::where('role', 'siswa')->orderBy('name')->get();
        $isDaily = ($type === 'daily');

        if ($type === 'daily') {
            $startDate = \Carbon\Carbon::parse($date)->startOfDay();
            $endDate   = $startDate->copy()->endOfDay();
            $filename  = "rekap_daily_{$date}.csv";
        } elseif ($type === 'weekly') {
            $startDate = \Carbon\Carbon::parse($week)->startOfWeek();
            $endDate   = $startDate->copy()->endOfWeek();
            $filename  = "rekap_weekly_{$week}.csv";
        } elseif ($type === 'monthly') {
            $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $endDate   = $startDate->copy()->endOfMonth();
            $filename  = "rekap_monthly_{$year}_{$month}.csv";
        } else {
            $startDate = \Carbon\Carbon::create($year, 1, 1)->startOfYear();
            $endDate   = $startDate->copy()->endOfYear();
            $filename  = "rekap_yearly_{$year}.csv";
        }

        $attendances = Attendance::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy('user_id');

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($students, $attendances, $isDaily, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            if ($isDaily) {
                fputcsv($file, ['No', 'Nama Siswa', 'Jam Masuk', 'Jam Pulang', 'Status']);
                $no = 1;
                foreach ($students as $student) {
                    $attendance = $attendances->get($student->id)?->first();
                    fputcsv($file, [
                        $no++,
                        $student->name,
                        $attendance?->time_in ?? '-',
                        $attendance?->time_out ?? '-',
                        $attendance?->status ?? 'Alfa/Tanpa Keterangan',
                    ]);
                }
            } else {
                fputcsv($file, ['No', 'Nama Siswa', 'Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alfa', 'Total Hadir', 'Persentase']);
                
                // Calculate working days
                $workingDays = 0;
                $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
                foreach ($period as $dt) {
                    if ($dt->isWeekday() && $dt->lte(now())) $workingDays++;
                }
                $workingDays = max(1, $workingDays);

                $no = 1;
                foreach ($students as $student) {
                    $sa = $attendances->get($student->id, collect());
                    $hadir = $sa->where('status', 'hadir')->count();
                    $terlambat = $sa->where('status', 'terlambat')->count();
                    $izin = $sa->where('status', 'izin')->count();
                    $sakit = $sa->where('status', 'sakit')->count();
                    $alfa = $sa->where('status', 'alfa')->count();

                    fputcsv($file, [
                        $no++,
                        $student->name,
                        $hadir,
                        $terlambat,
                        $izin,
                        $sakit,
                        $alfa,
                        $hadir + $terlambat,
                        round(($hadir + $terlambat) / $workingDays * 100, 1) . '%',
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
