<x-layouts.app title="Laporan Gabungan">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Laporan Gabungan</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan lengkap: Nama, NIS, Gender, Status, Masuk, Pulang</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                🖨️ Print
            </button>
            {{-- Filter --}}
            <form action="{{ route('sekertaris.report.combined') }}" method="GET" class="flex items-center gap-2">
                @include('sekertaris.partials.filter')
            </form>
        </div>
    </div>

    {{-- Print Header --}}
    <div class="hidden print:block mb-6">
        <h1 class="text-xl font-bold text-gray-900">Laporan Absensi Harian Gabungan</h1>
        <p class="text-sm text-gray-600">Periode: {{ $label }}</p>
    </div>

    <style>
        @media print {
            nav, header, footer { display: none !important; }
            body { background: white !important; }
            .print\:hidden { display: none !important; }
            .shadow-sm { box-shadow: none !important; }
            .border { border: 1px solid #ddd !important; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
            thead { background-color: #f3f4f6; -webkit-print-color-adjust: exact; }
        }
    </style>

    {{-- Table --}}
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">L/P</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pulang</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($attendances as $key => $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500 w-12">
                                {{ $key + 1 }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $row->user->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $row->user->nis ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $row->user->gender ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                {{ $row->time_in ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                {{ $row->time_out ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                    {{ match($row->status) {
                                        'hadir' => 'bg-green-100 text-green-700',
                                        'terlambat' => 'bg-yellow-100 text-yellow-700',
                                        'izin' => 'bg-blue-100 text-blue-700',
                                        'sakit' => 'bg-purple-100 text-purple-700',
                                        'alfa' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    } }}">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada data absensi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
