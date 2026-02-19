<x-layouts.app title="Dashboard Siswa">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Siswa</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }} — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Status Hari Ini --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        {{-- Status Absensi --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Hari Ini</h2>

            @if($todayAttendance)
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-green-50">
                        <span class="text-sm text-gray-600">Check-in (Pagi)</span>
                        <span class="text-sm font-semibold text-green-700">
                            {{ $todayAttendance->time_in ?? '—' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-blue-50">
                        <span class="text-sm text-gray-600">Check-out (Sore)</span>
                        <span class="text-sm font-semibold text-blue-700">
                            {{ $todayAttendance->time_out ?? 'Belum' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                            {{ match($todayAttendance->final_status) {
                                'hadir' => 'bg-green-100 text-green-800',
                                'terlambat' => 'bg-yellow-100 text-yellow-800',
                                'izin' => 'bg-blue-100 text-blue-800',
                                'sakit' => 'bg-purple-100 text-purple-800',
                                'setengah_hari' => 'bg-orange-100 text-orange-800',
                                default => 'bg-red-100 text-red-800',
                            } }}">
                            {{ ucfirst(str_replace('_', ' ', $todayAttendance->final_status)) }}
                        </span>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <div class="mx-auto h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500">Belum ada data absensi hari ini</p>
                    <a href="{{ route('siswa.qr') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700">
                        Generate QR Code
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Menu</h2>
            <div class="space-y-3">
                <a href="{{ route('siswa.qr') }}"
                   class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-all group">
                    <div class="h-10 w-10 rounded-lg bg-primary-100 flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                        <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Generate QR Code</p>
                        <p class="text-xs text-gray-500">Tampilkan QR untuk absensi</p>
                    </div>
                </a>

                <a href="{{ route('siswa.riwayat') }}"
                   class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-all group">
                    <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition-colors">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Riwayat Kehadiran</p>
                        <p class="text-xs text-gray-500">Lihat catatan kehadiran</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Riwayat 7 Hari Terakhir --}}
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 pb-0">
            <h2 class="text-lg font-semibold text-gray-900">7 Hari Terakhir</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Check-in</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Check-out</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentAttendances as $att)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $att->date->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $att->time_in ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $att->time_out ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                    {{ match($att->final_status) {
                                        'hadir' => 'bg-green-100 text-green-800',
                                        'terlambat' => 'bg-yellow-100 text-yellow-800',
                                        'izin' => 'bg-blue-100 text-blue-800',
                                        'sakit' => 'bg-purple-100 text-purple-800',
                                        'setengah_hari' => 'bg-orange-100 text-orange-800',
                                        default => 'bg-red-100 text-red-800',
                                    } }}">
                                    {{ ucfirst(str_replace('_', ' ', $att->final_status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">
                                Belum ada data kehadiran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
