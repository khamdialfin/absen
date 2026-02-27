<x-layouts.app title="Rekap Kehadiran">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-4 d-print-none">
        <div>
            <h1 class="h4 fw-bold">Rekap Kehadiran</h1>
            <p class="text-muted small">Laporan kehadiran siswa</p>
        </div>
        <div class="d-flex flex-wrap align-items-end gap-2">
            <form action="{{ route('walikelas.rekap') }}" method="GET" class="d-flex align-items-center gap-2">
                @include('sekertaris.partials.filter')
            </form>
            <a href="{{ route('walikelas.rekap.export', request()->all()) }}" class="btn btn-success btn-sm"><i class="bi bi-cloud-arrow-down"></i> Export</a>
        </div>
    </div>

    {{-- Info --}}
    <div class="alert alert-primary small mb-3 py-2">
        Laporan: <strong>{{ $label }}</strong>
        @if(isset($workingDays) && $workingDays > 0)
         — Total hari kerja: <strong>{{ $workingDays }} hari</strong>
        @endif
    </div>

    {{-- Tabel Rekap --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">No</th>
                        <th class="small text-white fw-semibold">Nama Siswa</th>
                        @if($isDaily)
                            <th class="small text-white fw-semibold text-center">Jam Masuk</th>
                            <th class="small text-white fw-semibold text-center">Jam Pulang</th>
                            <th class="small text-white fw-semibold text-center">Status</th>
                        @else
                            <th class="small text-white fw-semibold text-center text-success">Hadir</th>
                            <th class="small text-white fw-semibold text-center text-warning">Terlambat</th>
                            <th class="small text-white fw-semibold text-center text-info">Izin</th>
                            <th class="small text-white fw-semibold text-center" style="color:#6f42c1;">Sakit</th>
                            <th class="small text-white fw-semibold text-center text-danger">Alfa</th>
                            <th class="small text-white fw-semibold text-center text-white">Total</th>
                            <th class="small text-white fw-semibold text-center text-white">%</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $index => $row)
                        <tr>
                            <td class="small text-muted">{{ $index + 1 }}</td>
                            <td class="small fw-medium">{{ $row['student']->name }}</td>
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
                        <tr><td colspan="{{ $isDaily ? 5 : 9 }}" class="text-center text-muted small py-4">Tidak ada data siswa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
