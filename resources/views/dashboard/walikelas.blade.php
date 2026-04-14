<x-layouts.app title="Dashboard Walikelas">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Dashboard Walikelas</h1>
        <p class="text-muted small">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan kehadiran hari ini.</p>
    </div>

    {{-- Statistik Cards --}}
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['label' => 'Total Siswa', 'value' => $totalSiswa, 'color' => 'dark'],
                ['label' => 'Hadir (Tepat Waktu)', 'value' => $stats['hadir'], 'color' => 'success'],
                ['label' => 'Terlambat', 'value' => $stats['terlambat'], 'color' => 'warning'],
                ['label' => 'Izin / Sakit', 'value' => $stats['izin'] + $stats['sakit'], 'color' => 'info'],
                ['label' => 'Alfa / Belum Absen', 'value' => $stats['alfa'], 'color' => 'danger'],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="col-6 col-lg">
                <div class="card border-0 shadow-sm bg-{{ $card['color'] }} bg-opacity-75">
                    <div class="card-body py-3">
                        <div class="text-white" style="font-size:0.7rem;">{{ $card['label'] }}</div>
                        <div class="h4 fw-bold text-white mb-0">{{ $card['value'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        {{-- Recent Activity --}}
        <div class="col-lg-8">
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

        {{-- Session Status & Quick Actions --}}
        <div class="col-lg-4">
            {{-- Status Sesi Otomatis --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="small fw-semibold text-muted mb-3"><i class="bi bi-clock-history me-1"></i>Status Sesi Absensi</h2>
                    @if($schedule)
                        <div class="row g-3">
                            {{-- Pagi --}}
                            <div class="col-6">
                                <div class="rounded-3 border border-2 p-3 text-center {{ $morningActive ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light' }}">
                                    <span class="fs-2"><i class="bi bi-sunrise-fill"></i></span>
                                    <p class="small fw-semibold mb-0 mt-0">Sesi Pagi</p>
                                    <p class="mb-1 {{ $morningActive ? 'text-success fw-semibold' : 'text-muted' }}" style="font-size:0.7rem;">
                                        {{ $morningActive ? '● AKTIF' : '○ NONAKTIF' }}
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size:0.65rem;">
                                        {{ \Carbon\Carbon::parse($schedule->morning_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->morning_end)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            {{-- Sore --}}
                            <div class="col-6">
                                <div class="rounded-3 border border-2 p-3 text-center {{ $afternoonActive ? 'border-primary bg-primary bg-opacity-10' : 'border-secondary-subtle bg-light' }}">
                                    <span class="fs-2"><i class="bi bi-sunset-fill"></i></span>
                                    <p class="small fw-semibold mb-0 mt-0">Sesi Sore</p>
                                    <p class="mb-1 {{ $afternoonActive ? 'text-primary fw-semibold' : 'text-muted' }}" style="font-size:0.7rem;">
                                        {{ $afternoonActive ? '● AKTIF' : '○ NONAKTIF' }}
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size:0.65rem;">
                                        {{ \Carbon\Carbon::parse($schedule->afternoon_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->afternoon_end)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-light rounded-3 p-2 text-center mt-3">
                            <p class="mb-0 text-muted" style="font-size:0.7rem;">
                                <i class="bi bi-info-circle me-1"></i>Sesi buka/tutup otomatis sesuai jadwal
                            </p>
                        </div>
                    @else
                        <div class="bg-warning bg-opacity-10 border border-warning rounded-3 p-3 text-center">
                            <i class="bi bi-exclamation-triangle text-warning fs-3"></i>
                            <p class="small fw-medium mt-2 mb-1">Jadwal belum diatur</p>
                            <p class="mb-2" style="font-size:0.7rem;">Scanner Sekretaris tidak akan aktif sampai jadwal diatur.</p>
                            <a href="{{ route('walikelas.schedule') }}" class="btn btn-warning btn-sm fw-semibold">
                                <i class="bi bi-gear me-1"></i>Atur Jadwal
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column gap-2">
                    <h2 class="h6 fw-semibold">Aksi Cepat</h2>
                    <a href="{{ route('walikelas.schedule') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-clock"></i> Atur Jadwal Presensi
                    </a>
                    <a href="{{ route('walikelas.rekap') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-table"></i> Lihat Rekap Bulanan
                    </a>
                    <a href="{{ route('walikelas.rekap.export') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
