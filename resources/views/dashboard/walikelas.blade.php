<x-layouts.app title="Dashboard Walikelas">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Walikelas</h1>
        <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan kehadiran hari ini.</p>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        {{-- Total Siswa --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs text-gray-500 mb-1">Total Siswa</div>
            <div class="text-2xl font-bold text-gray-900">{{ $totalSiswa }}</div>
        </div>

        {{-- Hadir --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs text-gray-500 mb-1">Hadir (Tepat Waktu)</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['hadir'] }}</div>
        </div>

        {{-- Terlambat --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs text-gray-500 mb-1">Terlambat</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['terlambat'] }}</div>
        </div>

        {{-- Izin/Sakit --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs text-gray-500 mb-1">Izin / Sakit</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['izin'] + $stats['sakit'] }}</div>
        </div>

        {{-- Alfa --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="text-xs text-gray-500 mb-1">Alfa / Belum Absen</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['alfa'] }}</div>
        </div>
    </div>

    {{-- Recent Activity & Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Aktivitas Terkini</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentActivities as $activity)
                    <div class="p-4 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                            {{ substr($activity->user->name, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $activity->user->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $activity->time_in ? 'Absen Masuk: ' . $activity->time_in : ($activity->status == 'hadir' ? 'Hadir' : ucfirst($activity->status)) }}
                            </p>
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $activity->created_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500 text-sm">Belum ada aktivitas hari ini.</div>
                @endforelse
            </div>
        </div>
        
        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col gap-4 h-fit">
            <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
            <div class="flex flex-col gap-3">
                <a href="{{ route('walikelas.rekap') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 0v1.5c0 .621-.504 1.125-1.125 1.125m0 0h-7.5" />
                    </svg>
                    Lihat Rekap Bulanan
                </a>
                <a href="{{ route('walikelas.rekap.export') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border-2 border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
