<x-layouts.app title="Riwayat Kehadiran">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Riwayat Kehadiran</h1>
        <p class="text-muted small">Catatan kehadiran Anda selama ini</p>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">Tanggal</th>
                        <th class="small text-white fw-semibold">Check-in</th>
                        <th class="small text-white fw-semibold">Check-out</th>
                        <th class="small text-white fw-semibold">Status</th>
                        <th class="small text-white fw-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                        <tr>
                            <td class="small fw-medium">{{ $att->date->translatedFormat('l, d M Y') }}</td>
                            <td class="small text-muted">{{ $att->time_in ?? '—' }}</td>
                            <td class="small text-muted">{{ $att->time_out ?? '—' }}</td>
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
                                <span class="badge {{ $cls }}">{{ ucfirst(str_replace('_', ' ', $att->final_status)) }}</span>
                            </td>
                            <td class="small text-muted">{{ $att->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-clock text-muted fs-1"></i>
                                <p class="text-muted small mt-2 mb-0">Belum ada riwayat kehadiran</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
