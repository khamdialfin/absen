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
                ➕ Catat Pemasukan
            </button>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Oleh</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                    {{ $trx->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    {{ $trx->description }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-bold text-green-600">
                                    + Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-500">
                                    {{ $trx->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <form action="{{ route('bendahara.transaction.destroy', $trx) }}" method="POST" onsubmit="return confirm('Hapus pemasukan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition-colors" title="Hapus">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">
                                    Belum ada data pemasukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="border-t border-gray-200 p-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Input --}}
        <div x-show="modalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             x-cloak>
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
                 x-show="modalOpen"
                 x-transition.opacity
                 @click="modalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white p-6 text-left shadow-xl transition-all"
                     x-show="modalOpen"
                     x-transition.scale>
                    
                    <div class="mb-5 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Catat Pemasukan Baru</h3>
                        <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('bendahara.pemasukan.store') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="amount" id="amount" required min="1"
                                    class="block w-full rounded-xl border-gray-300 pl-10 focus:border-green-500 focus:ring-green-500 py-2.5" placeholder="0">
                            </div>
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                            <input type="date" name="date" id="date" required value="{{ date('Y-m-d') }}"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="description" id="description" required rows="3"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-2.5" placeholder="Contoh: Uang Kas Mingguan, Sumbangan, dll."></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="modalOpen = false" 
                                    class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="rounded-xl bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Simpan Pemasukan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
