<x-layouts.app title="Dashboard Sekertaris">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Dashboard Sekertaris</h1>
        <p class="text-muted small">Kelola presensi siswa untuk hari ini — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Status Sesi --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100 {{ $isMorningSession ? 'bg-warning bg-opacity-10 border-success' : '' }}">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="rounded-circle d-inline-block {{ $isMorningSession ? 'bg-warning' : 'bg-secondary' }}" style="width:10px;height:10px;"></span>
                        <span class="small fw-semibold {{ $isMorningSession ? 'text-warning' : 'text-muted' }}">Sesi Pagi ({{ $schedule ? \Carbon\Carbon::parse($schedule->morning_start)->format('H:i') . ' - ' . \Carbon\Carbon::parse($schedule->morning_end)->format('H:i') : 'Belum diatur' }})</span>
                    </div>
                    <p class="mb-0" style="font-size:0.75rem;">
                        {{ $isMorningSession ? '▶ Sesi aktif — silakan scan QR' : '⏸ Sesi tidak aktif' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100 {{ $isAfternoonSession ? 'bg-primary bg-opacity-10' : '' }}">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="rounded-circle d-inline-block {{ $isAfternoonSession ? 'bg-primary' : 'bg-secondary' }}" style="width:10px;height:10px;"></span>
                        <span class="small fw-semibold {{ $isAfternoonSession ? 'text-primary' : 'text-muted' }}">Sesi Sore ({{ $schedule ? \Carbon\Carbon::parse($schedule->afternoon_start)->format('H:i') . ' - ' . \Carbon\Carbon::parse($schedule->afternoon_end)->format('H:i') : 'Belum diatur' }})</span>
                    </div>
                    <p class="mb-0" style="font-size:0.75rem;">
                        {{ $isAfternoonSession ? '▶ Sesi aktif — silakan scan QR' : '⏸ Sesi tidak aktif' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-75">
                <div class="card-body">
                    <p class="h3 fw-bold text-white mb-0">{{ $stats['hadir'] + $stats['terlambat'] }}</p>
                    <p class="text-white small mb-0">Siswa Hadir Hari Ini</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Lengkap --}}
    <h2 class="h6 fw-semibold mb-3">Statistik Kehadiran</h2>
    <div class="row g-3 mb-4">
        @php
            $statItems = [
                ['label' => 'Hadir (Tepat Waktu)', 'value' => $stats['hadir'], 'color' => 'success'],
                ['label' => 'Terlambat', 'value' => $stats['terlambat'], 'color' => 'warning'],
                ['label' => 'Izin', 'value' => $stats['izin'], 'color' => 'info'],
                ['label' => 'Sakit', 'value' => $stats['sakit'], 'color' => 'secondary'],
                ['label' => 'Alfa / Belum Absen', 'value' => $stats['alfa'], 'color' => 'danger'],
            ];
        @endphp
        @foreach($statItems as $item)
            <div class="col-6 col-lg">
                <div class="card border-0 shadow-sm bg-{{ $item['color'] }} bg-opacity-75">
                    <div class="card-body py-3">
                        <div class="text-white" style="font-size:0.7rem;opacity:0.85;">{{ $item['label'] }}</div>
                        <div class="h4 fw-bold text-white mb-0">{{ $item['value'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        {{-- Recent Activity --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom fw-semibold">Aktivitas Terkini</div>
                <div class="list-group list-group-flush">
                    @forelse($recentActivities as $activity)
                        <div class="list-group-item d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-muted" style="width:40px;height:40px;font-size:0.75rem;flex-shrink:0;">
                                {{ substr($activity->user->name, 0, 2) }}
                            </div>
                            <div class="flex-grow-1" style="min-width:0;">
                                <p class="mb-0 small fw-medium text-truncate">{{ $activity->user->name }}</p>
                                <p class="mb-0 text-muted" style="font-size:0.7rem;">
                                    {{ $activity->time_in ? 'Absen Masuk: ' . $activity->time_in : ($activity->status == 'hadir' ? 'Hadir' : ucfirst($activity->status)) }}
                                </p>
                            </div>
                            <span class="text-muted" style="font-size:0.7rem;">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted small py-4">Belum ada aktivitas hari ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="col-lg-5">
            <h2 class="h6 fw-semibold mb-3">Aksi Cepat</h2>
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('sekertaris.scan') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 bg-opacity-75 {{ !$isMorningSession && !$isAfternoonSession ? 'disabled' : '' }}">
                        <i class="bi bi-qr-code-scan"></i> Buka Scanner QR
                    </a>
                    <a href="{{ route('sekertaris.attendance.manual') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-pencil-square"></i> Input Manual (Izin/Sakit)
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Waktu --}}
    <div class="bg-light rounded-3 border p-3">
        <p class="text-muted small mb-0">Waktu saat ini: <strong class="text-dark">{{ $currentTime }}</strong></p>
    </div>
</x-layouts.app>
