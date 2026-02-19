<x-layouts.app title="Laporan Kas">
    <div class="space-y-6">
        
        {{-- Header & Filter --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Laporan Kas Kelas</h1>
                <p class="text-sm text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            
            <form action="{{ route('bendahara.laporan') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                <div>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div class="flex items-center text-gray-400">-</div>
                <div>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <button type="submit" class="inline-flex justify-center items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Start
                </button>
            </form>
        </div>

        {{-- Export Buttons --}}
        <div class="flex gap-2 print:hidden">
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.134-.515-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 001.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                </svg>
                Print / PDF
            </button>
            <button onclick="exportTableToCSV('laporan-kas.csv')" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Excel (CSV)
            </button>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 print:grid-cols-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-500">Total Pemasukan</p>
                <p class="mt-2 text-2xl font-bold text-green-600">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-500">Total Pengeluaran</p>
                <p class="mt-2 text-2xl font-bold text-red-600">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-500">Saldo Periode Ini</p>
                <p class="mt-2 text-2xl font-bold {{ $saldoPeriode >= 0 ? 'text-gray-900' : 'text-red-600' }}">Rp {{ number_format($saldoPeriode, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full" id="laporanTable">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Masuk</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Keluar</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider print:hidden">Oleh</th>
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
                                <td class="px-6 py-4 text-sm">
                                    @if($trx->type == 'pemasukan')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Masuk</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Keluar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-green-600">
                                    {{ $trx->type == 'pemasukan' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right font-medium text-red-600">
                                    {{ $trx->type == 'pengeluaran' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right text-gray-500 print:hidden">
                                    {{ $trx->user->name ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">
                                    Tidak ada data pada periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t border-gray-200">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right">TOTAL</td>
                            <td class="px-6 py-4 text-right text-green-700">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-red-700">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                            <td class="print:hidden"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Simple CSV Export Script --}}
    <script>
        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("#laporanTable tr");
            
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (var j = 0; j < cols.length; j++) {
                    // Skip hidden columns (e.g., 'Action' or 'Oleh' if hidden in print)
                    if (cols[j].classList.contains('print:hidden')) continue;
                    
                    var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/,/g, ""); // Clean data
                    row.push('"' + data + '"');
                }
                csv.push(row.join(","));
            }

            downloadCSV(csv.join("\n"), filename);
        }

        function downloadCSV(csv, filename) {
            var csvFile;
            var downloadLink;

            csvFile = new Blob([csv], {type: "text/csv"});
            downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }
    </script>
</x-layouts.app>
