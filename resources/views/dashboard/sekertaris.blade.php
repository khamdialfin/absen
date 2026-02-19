<x-layouts.app title="Dashboard Sekertaris">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Sekertaris</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola presensi siswa untuk hari ini — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Status Sesi --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        {{-- Sesi Pagi --}}
        <div class="rounded-2xl p-6 shadow-sm border {{ $isMorningSession ? 'bg-green-50 border-green-200' : 'bg-white border-gray-100' }}">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-3 w-3 rounded-full {{ $isMorningSession ? 'bg-green-500 animate-pulse' : 'bg-gray-300' }}"></div>
                <span class="text-sm font-semibold {{ $isMorningSession ? 'text-green-700' : 'text-gray-500' }}">
                    Sesi Pagi (06:00 - 08:00)
                </span>
            </div>
            <p class="text-xs {{ $isMorningSession ? 'text-green-600' : 'text-gray-400' }}">
                {{ $isMorningSession ? '✅ Sesi aktif — silakan scan QR' : '⏸ Sesi tidak aktif' }}
            </p>
        </div>

        {{-- Sesi Sore --}}
        <div class="rounded-2xl p-6 shadow-sm border {{ $isAfternoonSession ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-100' }}">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-3 w-3 rounded-full {{ $isAfternoonSession ? 'bg-blue-500 animate-pulse' : 'bg-gray-300' }}"></div>
                <span class="text-sm font-semibold {{ $isAfternoonSession ? 'text-blue-700' : 'text-gray-500' }}">
                    Sesi Sore (15:00 - 16:00)
                </span>
            </div>
            <p class="text-xs {{ $isAfternoonSession ? 'text-blue-600' : 'text-gray-400' }}">
                {{ $isAfternoonSession ? '✅ Sesi aktif — silakan scan QR' : '⏸ Sesi tidak aktif' }}
            </p>
        </div>

        {{-- Total Scan Hari Ini (Overview) --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <p class="text-3xl font-bold text-primary-600">{{ $stats['hadir'] + $stats['terlambat'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Siswa Hadir Hari Ini</p>
        </div>
    </div>

    {{-- Statistik Lengkap --}}
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistik Kehadiran</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        {{-- Hadir --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="text-xs text-gray-500 mb-1">Hadir (Tepat Waktu)</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['hadir'] }}</div>
        </div>
        {{-- Terlambat --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="text-xs text-gray-500 mb-1">Terlambat</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['terlambat'] }}</div>
        </div>
        {{-- Izin --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="text-xs text-gray-500 mb-1">Izin</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['izin'] }}</div>
        </div>
        {{-- Sakit --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="text-xs text-gray-500 mb-1">Sakit</div>
            <div class="text-2xl font-bold text-purple-600">{{ $stats['sakit'] }}</div>
        </div>
        {{-- Alfa --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="text-xs text-gray-500 mb-1">Alfa / Belum Absen</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['alfa'] }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Recent Activity --}}
        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
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

        {{-- Action Buttons (Moved here) --}}
        <div class="flex flex-col gap-4">
            <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col gap-3">
                <a href="{{ route('sekertaris.scan') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors {{ !$isMorningSession && !$isAfternoonSession ? 'opacity-50 pointer-events-none' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                    </svg>
                    Buka Scanner QR
                </a>
                <a href="{{ route('sekertaris.attendance.manual') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border-2 border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Input Manual (Izin/Sakit)
                </a>
            </div>
        </div>
    </div>

    {{-- Info Waktu --}}
    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
        <p class="text-sm text-gray-500">
            Waktu saat ini: <strong class="text-gray-900">{{ $currentTime }}</strong>
        </p>
    </div>
</x-layouts.app>
