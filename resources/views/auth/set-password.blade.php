<x-layouts.guest title="Set Password">
    <div class="text-center mb-6">
        <div class="mx-auto h-16 w-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-1">Buat Password</h1>
        <p class="text-primary-200 text-sm">Langkah 1 dari 2 — Buat password untuk keamanan akun Anda</p>
    </div>

    {{-- Step Indicator --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        <div class="flex items-center gap-1.5">
            <div class="h-8 w-8 rounded-full bg-white text-primary-700 flex items-center justify-center text-sm font-bold">1</div>
            <span class="text-white text-xs font-medium">Password</span>
        </div>
        <div class="w-8 h-0.5 bg-white/30"></div>
        <div class="flex items-center gap-1.5">
            <div class="h-8 w-8 rounded-full bg-white/20 text-white/60 flex items-center justify-center text-sm font-bold">2</div>
            <span class="text-white/60 text-xs font-medium">Profil</span>
        </div>
    </div>

    <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl p-6 sm:p-8">
        @if($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-3">
                <ul class="text-xs text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" id="password" required minlength="8"
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                       placeholder="Minimal 8 karakter">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                       placeholder="Ulangi password">
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Lanjutkan ke Setup Profil →
            </button>
        </form>
    </div>
</x-layouts.guest>
