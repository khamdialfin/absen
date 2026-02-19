<x-layouts.app title="Dashboard Bendahara">
    <div class="space-y-6">
        
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kas Kelas</h1>
                <p class="text-sm text-gray-500 mt-1">Manajemen keuangan kelas</p>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Saldo Akhir --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-gray-500">Saldo Akhir</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold {{ $saldoAkhir >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Total Masuk --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-green-600">Total Pemasukan</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-gray-900">
                        Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                        Total
                    </span>
                </div>
            </div>

            {{-- Total Keluar --}}
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
                <p class="text-sm font-medium text-red-600">Total Pengeluaran</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-gray-900">
                        Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                        Total
                    </span>
                </div>
            </div>
        </div>

        {{-- Transaction History --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Riwayat Transaksi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Oleh</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentTransactions as $trx)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                    {{ $trx->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $trx->description }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($trx->type === 'pemasukan')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                            Debet (+)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                            Kredit (-)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold {{ $trx->type === 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-500">
                                    {{ $trx->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <form action="{{ route('bendahara.transaction.destroy', $trx) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">
                                    Belum ada transaksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>
