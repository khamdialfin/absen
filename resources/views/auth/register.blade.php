<x-layouts.guest title="Register">
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Daftar Akun</h1>
        <p class="text-primary-200 text-sm">Buat akun baru menggunakan Google untuk mulai absensi</p>
    </div>

    <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl p-8">
        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <div class="text-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Buat Akun Baru</h2>
            <p class="text-sm text-gray-500 mt-1">Gunakan akun Google sekolah untuk mendaftar</p>
        </div>

        {{-- Info --}}
        <div class="mb-6 rounded-xl bg-blue-50 border border-blue-100 p-4">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">Cara mendaftar:</p>
                    <ol class="list-decimal list-inside space-y-1 text-xs text-blue-600">
                        <li>Klik tombol "Daftar dengan Google"</li>
                        <li>Pilih akun Google sekolah Anda</li>
                        <li>Buat password untuk akun Anda</li>
                        <li>Selesai! Anda bisa mulai absensi</li>
                    </ol>
                </div>
            </div>
        </div>

        <a href="{{ route('google.redirect') }}"
           class="group flex w-full items-center justify-center gap-3 rounded-xl border-2 border-gray-200 bg-white px-6 py-3.5 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:border-primary-300 hover:bg-primary-50 hover:shadow-md">
            <svg class="h-5 w-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span>Daftar dengan Google</span>
            <svg class="h-4 w-4 text-gray-400 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </a>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700">Login di sini</a>
            </p>
        </div>
    </div>
</x-layouts.guest>
