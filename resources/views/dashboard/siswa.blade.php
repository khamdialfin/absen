<x-layouts.app title="Dashboard Siswa">
    {{-- Greeting Banner --}}
    <div class="rounded-4 p-4 mb-4 text-white position-relative overflow-hidden bg-primary bg-opacity-50">
        <div class="position-relative" style="z-index:1;">
            <p class="mb-1 small" style="color:rgba(255,255,255,0.7);">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="h4 fw-bold mb-1">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="mb-0 small" style="color:rgba(255,255,255,0.8);">Selamat Datang, Jangan lupa absen ya!</p>
        </div>
        <div class="position-absolute" style="top:-20px;right:-20px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.08);"></div>
        <div class="position-absolute" style="bottom:-30px;right:60px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
    </div>

    {{-- Status Hari Ini --}}
    <div class="row g-3 mb-4">
        @if($todayAttendance)
            {{-- Check-in --}}
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 text-dark bg-success bg-opacity-75">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:#f3e8ff;">
                                <i class="bi bi-box-arrow-in-right" style="font-size:0.85rem;color:#success;"></i>
                            </div>
                            <span class="text-white" style="font-size:0.9rem;opacity:0.85;">Masuk</span>
                        </div>
                        <p class="h5 fw-bold text-white mb-0">{{ $todayAttendance->time_in ?? 'Belum' }}</p>
                    </div>
                </div>
            </div>
            {{-- Check-out --}}
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="background:#f01e2c !important;opacity:0.75;">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:#f3e8ff;">
                                <i class="bi bi-box-arrow-right" style="font-size:0.85rem;color:#f01e2c;"></i>
                            </div>
                            <span class="text-white" style="font-size:0.9rem;">Pulang</span>
                        </div>
                        <p class="h5 fw-bold text-white mb-0">{{ $todayAttendance->time_out ?? 'Belum' }}</p>
                    </div>
                </div>
            </div>
            {{-- Status --}}
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="background:#a855f7 !important;opacity:0.75;">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:#f3e8ff;">
                                <i class="bi bi-check-circle text-purple" style="font-size:0.85rem;color:#a855f7;"></i>
                            </div>
                            <span class="text-white" style="font-size:0.9rem;">Status</span>
                        </div>
                        @php
                            $statusClass = match($todayAttendance->final_status) {
                                'hadir' => 'bg-success',
                                'terlambat' => 'bg-warning text-dark',
                                'izin' => 'bg-info',
                                'sakit' => 'bg-secondary',
                                'setengah_hari' => 'bg-warning text-dark',
                                default => 'bg-danger',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} px-3 py-2" style="font-size:0.8rem;">{{ ucfirst(str_replace('_', ' ', $todayAttendance->final_status)) }}</span>
                    </div>
                </div>
            </div>
            {{-- Keterangan --}}
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100" style="background:#f59e0b !important;opacity:0.75;">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:#fef3c7;">
                                <i class="bi bi-info-circle text-warning" style="font-size:0.85rem;"></i>
                            </div>
                            <span class="text-white" style="font-size:0.9rem;">Keterangan</span>
                        </div>
                        <p class="small fw-bold mb-0 text-white">{{ $todayAttendance->notes ?? 'Tidak ada catatan' }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;background:linear-gradient(135deg,#ede9fe,#dbeafe);">
                            <i class="bi bi-qr-code text-primary fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Belum Absen Hari Ini</h5>
                        <p class="text-muted small mb-3">Generate QR Code untuk absensi hari ini</p>
                        <a href="{{ route('siswa.qr') }}" class="btn btn-primary fw-semibold px-4">
                            <i class="bi bi-qr-code-scan me-1"></i> Generate QR Code
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mb-4">
        <div class="col-6">
            <a href="{{ route('siswa.qr') }}" class="card border-0 shadow-sm text-decoration-none h-100" style="transition:transform 0.15s;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg, #4f46e5, #7c3aed);flex-shrink:0;opacity:0.75;">
                        <i class="bi bi-qr-code text-white fs-5"></i>
                    </div>
                    <div>
                        <p class="small fw-bold text-dark mb-0">QR Code</p>
                        <p class="mb-0 text-muted" style="font-size:0.7rem;">Tampilkan QR absensi</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('siswa.riwayat') }}" class="card border-0 shadow-sm text-decoration-none h-100" style="transition:transform 0.15s;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:linear-gradient(135deg, #059669, #10b981);flex-shrink:0;opacity:0.75;">
                        <i class="bi bi-clock-history text-white fs-5"></i>
                    </div>
                    <div>
                        <p class="small fw-bold text-dark mb-0">Riwayat</p>
                        <p class="mb-0 text-muted" style="font-size:0.7rem;">Lihat catatan kehadiran</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Riwayat 7 Hari Terakhir --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex align-items-center gap-2 py-3">
            <i class="bi bi-calendar3 text-primary"></i>
            <span class="fw-semibold">7 Hari Terakhir</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="small fw-semibold border-0 py-2">Tanggal</th>
                        <th class="small fw-semibold border-0 py-2">Masuk</th>
                        <th class="small fw-semibold border-0 py-2">Pulang</th>
                        <th class="small fw-semibold border-0 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances as $att)
                        <tr>
                            <td class="small fw-medium">{{ $att->date->translatedFormat('l, d M Y') }}</td>
                            <td>
                                <span class="small fw-medium {{ $att->time_in ? 'text-success' : 'text-muted' }}">
                                    {{ $att->time_in ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="small fw-medium {{ $att->time_out ? 'text-primary' : 'text-muted' }}">
                                    {{ $att->time_out ?? '—' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $cls = match($att->final_status) {
                                        'hadir' => 'bg-success',
                                        'terlambat' => 'bg-warning text-dark',
                                        'izin' => 'bg-info',
                                        'sakit' => 'bg-secondary',
                                        'setengah_hari' => 'bg-warning text-dark',
                                        default => 'bg-danger',
                                    };
                                @endphp
                                <span class="badge {{ $cls }} rounded-pill px-2">{{ ucfirst(str_replace('_', ' ', $att->final_status)) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="bi bi-calendar-x text-muted fs-3 d-block mb-2"></i>
                                <span class="text-muted small">Belum ada data kehadiran</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(count($recentAttendances) > 0)
            <div class="card-footer bg-transparent border-top text-center py-2">
                <a href="{{ route('siswa.riwayat') }}" class="small text-primary fw-semibold text-decoration-none">
                    Lihat Semua Riwayat <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>
