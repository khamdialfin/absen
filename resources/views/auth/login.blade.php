<x-layouts.guest title="Login">
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Absensi Sekolah</h1>
        <p class="text-primary-200 text-sm">Sistem Presensi Digital Berbasis QR Code</p>
    </div>

    <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl p-8">
        @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        {{-- Tab Switcher --}}
        <div class="flex rounded-xl bg-gray-100 p-1 mb-6">
            <button type="button" onclick="switchTab('siswa')" id="tab-siswa"
                    class="flex-1 rounded-lg py-2 text-sm font-semibold transition-all bg-white text-gray-900 shadow-sm">
                🎒 Siswa
            </button>
            <button type="button" onclick="switchTab('staff')" id="tab-staff"
                    class="flex-1 rounded-lg py-2 text-sm font-semibold transition-all text-gray-500">
                🏫 Staff
            </button>
        </div>

        {{-- Siswa Login (Google) --}}
        <div id="panel-siswa">
            <div class="text-center mb-4">
                <p class="text-sm text-gray-500">Login menggunakan akun Google sekolah</p>
            </div>

            <a href="{{ route('google.redirect') }}"
               class="group flex w-full items-center justify-center gap-3 rounded-xl border-2 border-gray-200 bg-white px-6 py-3.5 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:border-primary-300 hover:bg-primary-50 hover:shadow-md">
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>Login dengan Google</span>
            </a>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700">Daftar di sini</a>
                </p>
            </div>
        </div>

        {{-- Staff Login (Email + Password) --}}
        <div id="panel-staff" class="hidden">
            <div class="text-center mb-4">
                <p class="text-sm text-gray-500">Login untuk Walikelas & Sekertaris</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                           placeholder="email@sekolah.test">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                           placeholder="••••••••">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>
                <button type="submit"
                        class="w-full rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-all hover:shadow-md">
                    Masuk
                </button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">
                Dengan login, Anda menyetujui kebijakan penggunaan sistem
            </p>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const siswaPanel = document.getElementById('panel-siswa');
            const staffPanel = document.getElementById('panel-staff');
            const siswaTab = document.getElementById('tab-siswa');
            const staffTab = document.getElementById('tab-staff');

            if (tab === 'siswa') {
                siswaPanel.classList.remove('hidden');
                staffPanel.classList.add('hidden');
                siswaTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                siswaTab.classList.remove('text-gray-500');
                staffTab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                staffTab.classList.add('text-gray-500');
            } else {
                staffPanel.classList.remove('hidden');
                siswaPanel.classList.add('hidden');
                staffTab.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
                staffTab.classList.remove('text-gray-500');
                siswaTab.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
                siswaTab.classList.add('text-gray-500');
            }
        }
    </script>
</x-layouts.guest>
