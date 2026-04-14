<x-layouts.app title="Rekap Kehadiran">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-4 d-print-none">
        <div>
            <h1 class="h4 fw-bold mb-1">Rekap Kehadiran</h1>
            <p class="text-muted small mb-0">Laporan kehadiran siswa kelas {{ auth()->user()->kelas }}</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('walikelas.rekap.export', request()->all()) }}" class="btn btn-success btn-sm fw-semibold">
                <i class="bi bi-download me-1"></i>Export CSV
            </a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm fw-semibold">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4 d-print-none">
        <div class="card-body py-3">
            <form action="{{ route('walikelas.rekap') }}" method="GET">
                @include('sekertaris.partials.filter')
            </form>
        </div>
    </div>

    {{-- Print Header --}}
    <div class="d-none d-print-block mb-3">
        <h1 class="h5 fw-bold">Rekap Kehadiran — {{ auth()->user()->kelas }}</h1>
        <p class="text-muted small">Periode: {{ $label }}</p>
    </div>

    <style>
        @media print {
            .sidebar, .sidebar-backdrop, .mobile-header, .top-navbar, .d-print-none { display: none !important; }
            .main-content { margin-left: 0 !important; }
            body { background: white !important; }
            .shadow-sm { box-shadow: none !important; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 6px 8px; font-size: 11px; }
        }
    </style>

    {{-- Info Periode --}}
    <div class="bg-white rounded-3 border p-3 mb-4 d-print-none">
        <p class="text-muted small mb-0">
            <i class="bi bi-calendar3 me-1"></i>Periode: <strong class="text-dark">{{ $label }}</strong>
            @if(isset($workingDays) && $workingDays > 0)
                — Total hari kerja: <strong class="text-dark">{{ $workingDays }} hari</strong>
            @endif
        </p>
    </div>

    {{-- Tabel Rekap --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold" style="width:50px;">No</th>
                        <th class="small text-white fw-semibold">Nama Siswa</th>
                        @if($isDaily)
                            <th class="small text-white fw-semibold text-center">Jam Masuk</th>
                            <th class="small text-white fw-semibold text-center">Jam Pulang</th>
                            <th class="small text-white fw-semibold text-center">Status</th>
                        @else
                            <th class="small text-white fw-semibold text-center">Hadir</th>
                            <th class="small text-white fw-semibold text-center">Terlambat</th>
                            <th class="small text-white fw-semibold text-center">Izin</th>
                            <th class="small text-white fw-semibold text-center">Sakit</th>
                            <th class="small text-white fw-semibold text-center">Alfa</th>
                            <th class="small text-white fw-semibold text-center">Total</th>
                            <th class="small text-white fw-semibold text-center">%</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $index => $row)
                        <tr>
                            <td class="small text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-muted" style="width:32px;height:32px;font-size:0.7rem;flex-shrink:0;">{{ substr($row['student']->name, 0, 2) }}</div>
                                    <span class="small fw-medium">{{ $row['student']->name }}</span>
                                </div>
                            </td>
                            @if($isDaily)
                                <td class="small text-center text-muted font-monospace">{{ $row['time_in'] }}</td>
                                <td class="small text-center text-muted font-monospace">{{ $row['time_out'] }}</td>
                                <td class="small text-center">
                                    @php
                                        $cls = match($row['status']) {
                                            'hadir' => 'bg-success',
                                            'terlambat' => 'bg-warning text-dark',
                                            'izin' => 'bg-info',
                                            'sakit' => 'bg-secondary',
                                            'alfa' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $cls }}">{{ ucfirst($row['status']) }}</span>
                                </td>
                            @else
                                <td class="small text-center"><span class="badge bg-success bg-opacity-10 text-success">{{ $row['hadir'] }}</span></td>
                                <td class="small text-center"><span class="badge bg-warning bg-opacity-10 text-warning">{{ $row['terlambat'] }}</span></td>
                                <td class="small text-center"><span class="badge bg-info bg-opacity-10 text-info">{{ $row['izin'] }}</span></td>
                                <td class="small text-center"><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $row['sakit'] }}</span></td>
                                <td class="small text-center"><span class="badge bg-danger bg-opacity-10 text-danger">{{ $row['alfa'] }}</span></td>
                                <td class="small text-center fw-semibold">{{ $row['total_hadir'] }}</td>
                                <td class="small text-center fw-bold {{ $row['persentase'] >= 75 ? 'text-success' : 'text-danger' }}">{{ $row['persentase'] }}%</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isDaily ? 5 : 9 }}" class="text-center py-5">
                                <span class="fs-2 text-muted"><i class="bi bi-calendar3"></i></span>
                                <p class="small fw-medium mt-2 mb-0">Tidak ada data siswa</p>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">Belum ada data kehadiran untuk periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
