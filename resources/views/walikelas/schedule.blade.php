<x-layouts.app title="Jadwal Presensi">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Pengaturan Jadwal Presensi</h1>
        <p class="text-muted small">Atur jam presensi berangkat (pagi) dan pulang (sore) untuk kelas <strong>{{ $kelas }}</strong></p>
    </div>

    <div class="row g-4">
        {{-- Form Jadwal --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('walikelas.schedule.save') }}">
                        @csrf

                        {{-- Sesi Pagi --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="fs-4"><i class="bi bi-sunrise-fill text-warning"></i></span>
                                <h2 class="h6 fw-semibold mb-0">Sesi Pagi (Berangkat)</h2>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="morning_start" class="form-label small fw-medium">Jam Mulai</label>
                                    <input type="time" name="morning_start" id="morning_start" class="form-control @error('morning_start') is-invalid @enderror"
                                        value="{{ old('morning_start', $schedule->morning_start ?? '06:30') }}" required>
                                    @error('morning_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="morning_end" class="form-label small fw-medium">Jam Selesai</label>
                                    <input type="time" name="morning_end" id="morning_end" class="form-control @error('morning_end') is-invalid @enderror"
                                        value="{{ old('morning_end', $schedule->morning_end ?? '08:00') }}" required>
                                    @error('morning_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-muted mt-2 mb-0" style="font-size:0.75rem;">
                                <i class="bi bi-info-circle me-1"></i>Scanner QR di Sekretaris akan otomatis aktif di rentang waktu ini.
                            </p>
                        </div>

                        <hr>

                        {{-- Batas Terlambat --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="fs-4"><i class="bi bi-alarm-fill text-danger"></i></span>
                                <h2 class="h6 fw-semibold mb-0">Batas Terlambat</h2>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="late_threshold" class="form-label small fw-medium">Jam Batas Terlambat</label>
                                    <input type="time" name="late_threshold" id="late_threshold" class="form-control @error('late_threshold') is-invalid @enderror"
                                        value="{{ old('late_threshold', $schedule->late_threshold ?? '07:30') }}" required>
                                    @error('late_threshold')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-muted mt-2 mb-0" style="font-size:0.75rem;">
                                <i class="bi bi-info-circle me-1"></i>Siswa yang scan setelah jam ini akan dicatat sebagai <strong>Terlambat</strong>.
                            </p>
                        </div>

                        <hr>

                        {{-- Sesi Sore --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="fs-4"><i class="bi bi-sunset-fill text-primary"></i></span>
                                <h2 class="h6 fw-semibold mb-0">Sesi Sore (Pulang)</h2>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="afternoon_start" class="form-label small fw-medium">Jam Mulai</label>
                                    <input type="time" name="afternoon_start" id="afternoon_start" class="form-control @error('afternoon_start') is-invalid @enderror"
                                        value="{{ old('afternoon_start', $schedule->afternoon_start ?? '15:00') }}" required>
                                    @error('afternoon_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label for="afternoon_end" class="form-label small fw-medium">Jam Selesai</label>
                                    <input type="time" name="afternoon_end" id="afternoon_end" class="form-control @error('afternoon_end') is-invalid @enderror"
                                        value="{{ old('afternoon_end', $schedule->afternoon_end ?? '16:00') }}" required>
                                    @error('afternoon_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-muted mt-2 mb-0" style="font-size:0.75rem;">
                                <i class="bi bi-info-circle me-1"></i>Scanner QR sesi pulang akan otomatis aktif di rentang waktu ini.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <i class="bi bi-check-lg me-1"></i>Simpan Jadwal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Preview Jadwal --}}
        <div class="col-lg-5">
            {{-- Status Saat Ini --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="small fw-semibold text-muted mb-3"><i class="bi bi-broadcast me-1"></i>Status Sesi Saat Ini</h2>
                    @if($schedule)
                        @php
                            $morningActive = $schedule->isMorningActive();
                            $afternoonActive = $schedule->isAfternoonActive();
                        @endphp
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="rounded-3 border border-2 p-3 text-center {{ $morningActive ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light' }}">
                                    <span class="fs-2"><i class="bi bi-sunrise-fill"></i></span>
                                    <p class="small fw-semibold mb-0 mt-0">Sesi Pagi</p>
                                    <p class="mb-0 {{ $morningActive ? 'text-success fw-semibold' : 'text-muted' }}" style="font-size:0.7rem;">
                                        {{ $morningActive ? '● AKTIF' : '○ NONAKTIF' }}
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size:0.7rem;">
                                        {{ \Carbon\Carbon::parse($schedule->morning_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->morning_end)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="rounded-3 border border-2 p-3 text-center {{ $afternoonActive ? 'border-primary bg-primary bg-opacity-10' : 'border-secondary-subtle bg-light' }}">
                                    <span class="fs-2"><i class="bi bi-sunset-fill"></i></span>
                                    <p class="small fw-semibold mb-0 mt-0">Sesi Sore</p>
                                    <p class="mb-0 {{ $afternoonActive ? 'text-primary fw-semibold' : 'text-muted' }}" style="font-size:0.7rem;">
                                        {{ $afternoonActive ? '● AKTIF' : '○ NONAKTIF' }}
                                    </p>
                                    <p class="mb-0 text-muted" style="font-size:0.7rem;">
                                        {{ \Carbon\Carbon::parse($schedule->afternoon_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->afternoon_end)->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-light rounded-3 p-2 text-center mt-3">
                            <p class="mb-0 text-muted" style="font-size:0.75rem;">
                                Batas terlambat: <strong class="text-dark">{{ \Carbon\Carbon::parse($schedule->late_threshold)->format('H:i') }}</strong>
                            </p>
                        </div>
                    @else
                        <div class="bg-light rounded-3 p-4 text-center">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="small text-muted mb-0 mt-2">Jadwal belum diatur. Silakan isi form di samping.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Mark Alfa Manual --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="small fw-semibold text-muted mb-3"><i class="bi bi-exclamation-triangle me-1"></i>Mark Alfa Manual</h2>
                    <p class="text-muted mb-3" style="font-size:0.75rem;">
                        Tandai semua siswa yang belum absen hari ini sebagai <strong>ALFA</strong>. Gunakan setelah sesi pagi berakhir.
                    </p>
                    <button onclick="markAlfa()" class="btn btn-outline-danger btn-sm w-100 fw-semibold">
                        <i class="bi bi-person-x me-1"></i>Mark Siswa Belum Absen sebagai Alfa
                    </button>
                </div>
            </div>

            {{-- Info --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h2 class="small fw-semibold text-muted mb-2"><i class="bi bi-lightbulb me-1"></i>Cara Kerja</h2>
                    <ul class="small text-muted mb-0" style="font-size:0.75rem; padding-left: 1.2rem;">
                        <li class="mb-1">Wali Kelas mengatur jam presensi berangkat & pulang</li>
                        <li class="mb-1">Scanner QR di Sekretaris <strong>otomatis aktif</strong> sesuai jadwal</li>
                        <li class="mb-1">Di luar jam yang ditentukan, scanner otomatis nonaktif</li>
                        <li class="mb-1">Siswa scan setelah <strong>batas terlambat</strong> → status Terlambat</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function markAlfa() {
            if (!confirm('Tandai semua siswa yang belum absen hari ini sebagai ALFA?\n\nAksi ini tidak bisa dibatalkan.')) {
                return;
            }

            try {
                const response = await fetch('{{ route("walikelas.session.close-morning") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();
                alert(data.message);
                window.location.reload();
            } catch (error) {
                alert('Gagal: ' + error.message);
            }
        }
    </script>
</x-layouts.app>
