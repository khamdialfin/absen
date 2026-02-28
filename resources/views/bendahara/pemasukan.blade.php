<x-layouts.app title="Pemasukan Kas">
    <div x-data="{ modalOpen: false }" class="space-y-6">
        
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pemasukan Kas</h1>
                <p class="text-sm text-gray-500 mt-1">Catat semua uang masuk (Debet)</p>
            </div>
            <button @click="modalOpen = true" 
                    class="inline-flex items-center justify-center rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                 Catat Pemasukan
            </button>
        </div>
        <button type="button" class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPemasukan"><i class="bi bi-plus-circle"></i> Catat Pemasukan</button>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">Tanggal</th>
                        <th class="small text-white fw-semibold">Keterangan</th>
                        <th class="small text-white fw-semibold text-end">Jumlah</th>
                        <th class="small text-white fw-semibold text-end">Oleh</th>
                        <th class="small text-white fw-semibold text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td class="small text-muted font-monospace">{{ $trx->date->format('d/m/Y') }}</td>
                            <td class="small fw-medium">{{ $trx->description }}</td>
                            <td class="small text-end fw-bold text-success">+ Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                            <td class="small text-end text-muted">{{ $trx->user->name ?? '-' }}</td>
                            <td class="small text-end">
                                <form action="{{ route('bendahara.transaction.destroy', $trx) }}" method="POST" onsubmit="return confirm('Hapus pemasukan ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger p-0" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted small py-4">Belum ada data pemasukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-transparent border-top">{{ $transactions->links() }}</div>
        @endif
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="modalPemasukan" tabindex="-1" aria-labelledby="modalPemasukanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalPemasukanLabel">Catat Pemasukan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bendahara.pemasukan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="amount" class="form-label small fw-medium">Nominal (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="amount" id="amount" required min="1" class="form-control" placeholder="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label small fw-medium">Tanggal</label>
                            <input type="date" name="date" id="date" required value="{{ date('Y-m-d') }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label small fw-medium">Keterangan</label>
                            <textarea name="description" id="description" required rows="3" class="form-control" placeholder="Contoh: Uang Kas Mingguan, Sumbangan, dll."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-semibold">Simpan Pemasukan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
