<x-layouts.app title="Rekapan Absensi - Masuk">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-4 d-print-none">
        <div>
            <h1 class="h4 fw-bold">Rekap Absensi Masuk</h1>
            <p class="text-muted small">Laporan khusus jam masuk siswa</p>
        </div>

        <div class="flex items-end gap-3">
            <button onclick="window.print()"
                    class="h-10 inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                🖨️ Print
            </button>
            {{-- Filter --}}
            <form action="{{ route('sekertaris.recap.masuk') }}" method="GET" class="flex items-center gap-2">
                @include('sekertaris.partials.filter')
            </form>
        </div>
    </div>
    <div class="d-none d-print-block mb-3"><h1 class="h5 fw-bold">Rekap Absensi Masuk</h1><p class="text-muted small">Periode: {{ $label }}</p></div>
    <style>@media print { .sidebar, .sidebar-backdrop, .mobile-header, .d-print-none { display: none !important; } .main-content { margin-left: 0 !important; } body { background: white !important; } .shadow-sm { box-shadow: none !important; } }</style>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">No</th>
                        <th class="small text-white fw-semibold">Nama Siswa</th>
                        <th class="small text-white fw-semibold">Jam Masuk</th>
                        <th class="small text-white fw-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $key => $row)
                        <tr>
                            <td class="small text-muted">{{ $key + 1 }}</td>
                            <td><div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-light d-flex align-items-center justify-content-center fw-bold text-muted" style="width:32px;height:32px;font-size:0.7rem;flex-shrink:0;">{{ substr($row->user->name, 0, 2) }}</div><span class="small fw-medium">{{ $row->user->name }}</span></div></td>
                            <td class="small fw-bold font-monospace">{{ $row->time_in ?? '—' }}</td>
                            <td>
                                @php $cls = match($row->status) { 'hadir' => 'bg-success', 'terlambat' => 'bg-warning text-dark', 'izin' => 'bg-info', 'sakit' => 'bg-secondary', 'alfa' => 'bg-danger', default => 'bg-secondary' }; @endphp
                                <span class="badge {{ $cls }}">{{ ucfirst($row->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data absensi masuk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
