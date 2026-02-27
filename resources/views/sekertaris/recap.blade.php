<x-layouts.app title="Rekapan Absensi">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-4 d-print-none">
        <div>
            <h1 class="h4 fw-bold">Rekapan Absensi</h1>
            <p class="text-muted small">Laporan kehadiran siswa per hari</p>
        </div>
        <div class="d-flex flex-wrap align-items-end gap-2 mt-2">
            <a href="{{ route('sekertaris.recap.export', request()->all()) }}" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-spreadsheet"></i> Export .CSV</a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm"><i class="bi bi-printer"></i> Print</button>
            <form action="{{ route('sekertaris.recap') }}" method="GET" class="d-flex align-items-center gap-2">
                @include('sekertaris.partials.filter')
            </form>
        </div>
    </div>

    {{-- Print Header --}}
    <div class="d-none d-print-block mb-3">
        <h1 class="h5 fw-bold">Rekap Absensi Harian</h1>
        <p class="text-muted small">Periode: {{ $label }}</p>
    </div>

    <style>
        @media print {
            .sidebar, .sidebar-backdrop, .mobile-header, .d-print-none { display: none !important; }
            .main-content { margin-left: 0 !important; }
            body { background: white !important; }
            .shadow-sm { box-shadow: none !important; }
            .stat-cards { display: none !important; }
        }
    </style>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4 d-print-none stat-cards">
        @php
            $statItems = [
                ['icon' => 'bi-check-circle', 'label' => 'Hadir', 'value' => $stats['hadir'], 'color' => 'success'],
                ['icon' => 'bi-clock', 'label' => 'Terlambat', 'value' => $stats['terlambat'], 'color' => 'warning'],
                ['icon' => 'bi-clipboard-minus', 'label' => 'Izin', 'value' => $stats['izin'], 'color' => 'info'],
                ['icon' => 'bi-hospital', 'label' => 'Sakit', 'value' => $stats['sakit'], 'color' => 'secondary'],
                ['icon' => 'bi-x-circle', 'label' => 'Alfa', 'value' => $stats['alfa'], 'color' => 'danger'],
            ];
        @endphp
        @foreach($statItems as $item)
            <div class="col-6 col-md">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-2 py-2">
                        <span class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $item['color'] }} bg-opacity-10 text-{{ $item['color'] }}" style="width:40px;height:40px;font-size:1.1rem;"><i class="bi {{ $item['icon'] }}"></i></span>
                        <div>
                            <p class="mb-0 text-muted" style="font-size:0.7rem;">{{ $item['label'] }}</p>
                            <p class="mb-0 h5 fw-bold">{{ $item['value'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">Nama Siswa</th>
                        <th class="small text-white fw-semibold">Jam Masuk</th>
                        <th class="small text-white fw-semibold">Jam Pulang</th>
                        <th class="small text-white fw-semibold">Status</th>
                        <th class="small text-white fw-semibold">Keterangan</th>
                        <th class="small text-white fw-semibold">Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $row)
                        <tr>
                            <td><div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-muted" style="width:32px;height:32px;font-size:0.7rem;flex-shrink:0;">{{ substr($row->user->name, 0, 2) }}</div><span class="small fw-medium">{{ $row->user->name }}</span></div></td>
                            <td class="small text-muted font-monospace">{{ $row->time_in ?? '—' }}</td>
                            <td class="small text-muted font-monospace">{{ $row->time_out ?? '—' }}</td>
                            <td>
                                @php $cls = match($row->status) { 'hadir' => 'bg-success', 'terlambat' => 'bg-warning text-dark', 'izin' => 'bg-info', 'sakit' => 'bg-secondary', 'alfa' => 'bg-danger', default => 'bg-secondary' }; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst($row->status) }}</span>
                            </td>
                            <td class="small text-muted">{{ $row->notes ?? '—' }}</td>
                            <td class="small">
                                @if($row->letter_file)
                                    <a href="{{ Storage::url($row->letter_file) }}" target="_blank" class="text-primary text-decoration-none"><i class="bi bi-file-earmark-text-fill"></i> Lihat</a>
                                @else <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5"><span class="fs-2"><i class="bi bi-calendar3"></i></span><p class="small fw-medium mt-2 mb-0">Tidak ada data absensi</p><p class="text-muted mb-0" style="font-size:0.75rem;">Belum ada rekaman absensi untuk tanggal ini.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
