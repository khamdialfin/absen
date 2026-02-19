<x-layouts.app title="Rekap Kehadiran">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekap Kehadiran</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan kehadiran siswa</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Filter --}}
            <form action="{{ route('walikelas.rekap') }}" method="GET" class="flex items-center gap-2">
                @include('sekertaris.partials.filter')
            </form>
            
            {{-- Export --}}
            <a href="{{ route('walikelas.rekap.export', request()->all()) }}"
               class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 h-[38px] mt-auto">
                📥 Export
            </a>
        </div>
    </div>

    {{-- Info --}}
    <div class="mb-6 rounded-xl bg-primary-50 border border-primary-100 p-4">
        <p class="text-sm text-primary-700">
            Laporan: <strong>{{ $label }}</strong>
            @if(isset($workingDays) && $workingDays > 0)
             — Total hari kerja: <strong>{{ $workingDays }} hari</strong>
            @endif
        </p>
    </div>

    {{-- Tabel Rekap --}}
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        @if($isDaily)
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Pulang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        @else
                            <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-600 uppercase tracking-wider">Terlambat</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Izin</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-purple-600 uppercase tracking-wider">Sakit</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-red-600 uppercase tracking-wider">Alfa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">%</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($rekap as $index => $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $row['student']->name }}</td>
                            
                            @if($isDaily)
                                <td class="px-4 py-3 text-sm text-center text-gray-600 font-mono">{{ $row['time_in'] }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600 font-mono">{{ $row['time_out'] }}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    @php
                                        $badges = [
                                            'hadir'     => 'bg-green-100 text-green-700',
                                            'terlambat' => 'bg-yellow-100 text-yellow-700',
                                            'izin'      => 'bg-blue-100 text-blue-700',
                                            'sakit'     => 'bg-purple-100 text-purple-700',
                                            'alfa'      => 'bg-red-100 text-red-700',
                                            '-'         => 'bg-gray-100 text-gray-500', 
                                        ];
                                        $statusClass = $badges[$row['status']] ?? 'bg-gray-100 text-gray-500';
                                    @endphp
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ ucfirst($row['status']) }}
                                    </span>
                                </td>
                            @else
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded bg-green-50 text-green-700 text-xs font-bold">{{ $row['hadir'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded bg-yellow-50 text-yellow-700 text-xs font-bold">{{ $row['terlambat'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded bg-blue-50 text-blue-700 text-xs font-bold">{{ $row['izin'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded bg-purple-50 text-purple-700 text-xs font-bold">{{ $row['sakit'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded bg-red-50 text-red-700 text-xs font-bold">{{ $row['alfa'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-center font-semibold text-gray-700">
                                    {{ $row['total_hadir'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-center font-bold {{ $row['persentase'] >= 75 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $row['persentase'] }}%
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isDaily ? 5 : 9 }}" class="px-4 py-8 text-center text-sm text-gray-400">
                                Tidak ada data siswa
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
