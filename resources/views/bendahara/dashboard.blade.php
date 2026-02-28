<x-layouts.app title="Dashboard Bendahara">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Kas Kelas</h1>
        <p class="text-muted small">Manajemen keuangan kelas</p>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="small fw-medium text-muted mb-1">Saldo Akhir</p>
                    <span class="h3 fw-bold {{ $saldoAkhir >= 0 ? '' : 'text-danger' }}">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="small fw-medium text-success mb-1">Total Pemasukan</p>
                    <span class="h4 fw-bold">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</span>
                    <span class="badge bg-success bg-opacity-10 text-success ms-1">Total</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="small fw-medium text-danger mb-1">Total Pengeluaran</p>
                    <span class="h4 fw-bold">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger ms-1">Total</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Transaction History --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom fw-bold">Riwayat Transaksi</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">Tanggal</th>
                        <th class="small text-white fw-semibold">Keterangan</th>
                        <th class="small text-white fw-semibold">Tipe</th>
                        <th class="small text-white fw-semibold text-end">Jumlah</th>
                        <th class="small text-white fw-semibold text-end">Oleh</th>
                        <th class="small text-white fw-semibold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $trx)
                        <tr>
                            <td class="small text-muted font-monospace">{{ $trx->date->format('d/m/Y') }}</td>
                            <td class="small fw-medium">{{ $trx->description }}</td>
                            <td class="small">
                                @if($trx->type === 'pemasukan')
                                    <span class="badge bg-success">Debet (+)</span>
                                @else
                                    <span class="badge bg-danger">Kredit (-)</span>
                                @endif
                            </td>
                            <td class="small text-end fw-bold {{ $trx->type === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="small text-end text-muted">{{ $trx->user->name ?? '-' }}</td>
                            <td class="small text-end">
                                <form action="{{ route('bendahara.transaction.destroy', $trx) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger p-0" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Belum ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
