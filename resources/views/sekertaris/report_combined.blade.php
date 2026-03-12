<x-layouts.app title="Laporan Gabungan">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-4 d-print-none">
        <div>
            <h1 class="h4 fw-bold mb-1">Laporan Gabungan</h1>
            <p class="text-muted small mb-0">Laporan lengkap: Nama, NIS, Gender, Status, Masuk, Pulang</p>
        </div>

        <button onclick="window.print()" class="btn btn-secondary btn-sm fw-semibold">
            <i class="bi bi-printer me-1"></i>Print
        </button>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4 d-print-none">
        <div class="card-body py-3">
            <form action="{{ route('sekertaris.report.combined') }}" method="GET">
                @include('sekertaris.partials.filter')
            </form>
        </div>
    </div>

    {{-- Print Header --}}
    <div class="d-none d-print-block mb-3">
        <h1 class="h5 fw-bold">Laporan Absensi Gabungan</h1>
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
            thead { background-color: #f3f4f6; }
        }
    </style>

    {{-- Periode Label --}}
    <div class="bg-white rounded-3 border p-3 mb-4 d-print-none">
        <p class="text-muted small mb-0"><i class="bi bi-calendar3 me-1"></i>Periode: <strong class="text-dark">{{ $label }}</strong></p>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold" style="width:50px;">No</th>
                        <th class="small text-white fw-semibold">Nama Siswa</th>
                        <th class="small text-white fw-semibold">NIS</th>
                        <th class="small text-white fw-semibold">L/P</th>
                        <th class="small text-white fw-semibold">Tanggal</th>
                        <th class="small text-white fw-semibold">Masuk</th>
                        <th class="small text-white fw-semibold">Pulang</th>
                        <th class="small text-white fw-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $key => $row)
                        <tr>
                            <td class="small text-muted">{{ $key + 1 }}</td>
                            <td class="small fw-medium">{{ $row->user->name }}</td>
                            <td class="small text-muted">{{ $row->user->nis ?? '—' }}</td>
                            <td class="small text-muted">{{ $row->user->gender ?? '—' }}</td>
                            <td class="small text-muted">{{ $row->date->translatedFormat('d M Y') }}</td>
                            <td class="small text-muted font-monospace">{{ $row->time_in ?? '—' }}</td>
                            <td class="small text-muted font-monospace">{{ $row->time_out ?? '—' }}</td>
                            <td>
                                @php $cls = match($row->status) { 'hadir' => 'bg-success', 'terlambat' => 'bg-warning text-dark', 'izin' => 'bg-info', 'sakit' => 'bg-secondary', 'alfa' => 'bg-danger', default => 'bg-secondary' }; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst($row->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <span class="fs-2 text-muted"><i class="bi bi-file-earmark-bar-graph"></i></span>
                                <p class="small fw-medium mt-2 mb-0">Tidak ada data absensi</p>
                                <p class="text-muted mb-0" style="font-size:0.75rem;">Belum ada data untuk periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
