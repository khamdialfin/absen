<x-layouts.app title="Rekapan Absensi">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekapan Absensi</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan kehadiran siswa per hari</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('sekertaris.scan') }}"
               class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                📷 Scan QR
            </a>
            <a href="{{ route('sekertaris.recap.export', request()->all()) }}"
               class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                📥 Export .CSV
            </a>
            <button onclick="window.print()"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                🖨️ Print
            </button>
            {{-- Filter --}}
            <form action="{{ route('sekertaris.recap') }}" method="GET" class="flex items-center gap-2">
                @include('sekertaris.partials.filter')
            </form>
        </div>
    </div>

    {{-- Print Header --}}
    <div class="hidden print:block mb-6">
        <h1 class="text-xl font-bold text-gray-900">Rekap Absensi Harian</h1>
        <p class="text-sm text-gray-600">Periode: {{ $label }}</p>
    </div>

    <style>
        @media print {
            nav, header, footer { display: none !important; }
            body { background: white !important; }
            .print\:hidden { display: none !important; }
            .shadow-sm { box-shadow: none !important; }
            .border { border: 1px solid #ddd !important; }
            /* Hide Stats Cards */
            .grid-cols-2.md\:grid-cols-5 { display: none !important; }
        }
    </style>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8 print:hidden">
        {{-- Hadir --}}
        <div class="rounded-xl bg-white p-4 shadow-sm border border-green-100">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-xl">✅</span>
                <div>
                    <p class="text-xs font-medium text-gray-500">Hadir</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['hadir'] }}</p>
                </div>
            </div>
        </div>

        {{-- Terlambat --}}
        <div class="rounded-xl bg-white p-4 shadow-sm border border-yellow-100">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 text-xl">⏰</span>
                <div>
                    <p class="text-xs font-medium text-gray-500">Terlambat</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['terlambat'] }}</p>
                </div>
            </div>
        </div>

        {{-- Izin --}}
        <div class="rounded-xl bg-white p-4 shadow-sm border border-blue-100">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-xl">📝</span>
                <div>
                    <p class="text-xs font-medium text-gray-500">Izin</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['izin'] }}</p>
                </div>
            </div>
        </div>

        {{-- Sakit --}}
        <div class="rounded-xl bg-white p-4 shadow-sm border border-purple-100">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100 text-xl">💊</span>
                <div>
                    <p class="text-xs font-medium text-gray-500">Sakit</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['sakit'] }}</p>
                </div>
            </div>
        </div>

        {{-- Alfa --}}
        <div class="rounded-xl bg-white p-4 shadow-sm border border-red-100">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-xl">❌</span>
                <div>
                    <p class="text-xs font-medium text-gray-500">Alfa</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['alfa'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($attendances as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 mr-3">
                                        {{ substr($row->user->name, 0, 2) }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $row->user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                {{ $row->time_in ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                {{ $row->time_out ?? '—' }}
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
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $row->notes ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($row->letter_file)
                                    <a href="{{ Storage::url($row->letter_file) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline">
                                        📄 Lihat
                                    </a>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-4xl mb-2">📅</span>
                                    <p class="text-sm font-medium text-gray-900">Tidak ada data absensi</p>
                                    <p class="text-xs text-gray-500 mt-1">Belum ada rekaman absensi untuk tanggal ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
