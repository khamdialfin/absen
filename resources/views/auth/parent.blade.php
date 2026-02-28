{{-- resources/views/auth/parent.blade.php --}}
<x-layouts.guest title="Data Orang Tua">
    <div class="text-center mb-6">
        <div class="mx-auto h-16 w-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-1">Data Orang Tua</h1>
        <p class="text-primary-200 text-sm">Langkah 3 dari 3 — Informasi kontak orang tua/wali</p>
    </div>

    {{-- Step Indicator --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        <div class="flex items-center gap-1.5">
            <div class="h-8 w-8 rounded-full bg-white/30 text-white flex items-center justify-center text-sm font-bold">✓</div>
            <span class="text-white/80 text-xs font-medium">Password</span>
        </div>
        <div class="w-8 h-0.5 bg-white/50"></div>
        <div class="flex items-center gap-1.5">
            <div class="h-8 w-8 rounded-full bg-white/30 text-white flex items-center justify-center text-sm font-bold">✓</div>
            <span class="text-white/80 text-xs font-medium">Profil</span>
        </div>
        <div class="w-8 h-0.5 bg-white/50"></div>
        <div class="flex items-center gap-1.5">
            <div class="h-8 w-8 rounded-full bg-white text-primary-700 flex items-center justify-center text-sm font-bold">3</div>
            <span class="text-white text-xs font-medium">Orang Tua</span>
        </div>
    </div>

    <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl p-6 sm:p-8">
        @if(session('error'))
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-3">
                <ul class="text-xs text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 rounded-xl bg-blue-50 border border-blue-100 p-4">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-medium mb-1">Informasi:</p>
                    <p class="text-xs text-blue-600">
                        Data orang tua akan digunakan untuk mengirim notifikasi WhatsApp saat anak melakukan presensi.
                        Nomor WhatsApp harus aktif.
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.parent.store') }}" class="space-y-4">
            @csrf

            {{-- Nama Orang Tua --}}
            <div>
                <label for="nama_ortu" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Orang Tua / Wali <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="nama_ortu" 
                       id="nama_ortu" 
                       value="{{ old('nama_ortu', $parentProfile->name ?? '') }}"
                       required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                       placeholder="Contoh: Budi Santoso">
            </div>

            {{-- No WhatsApp --}}
            <div>
                <label for="no_wa" class="block text-sm font-medium text-gray-700 mb-1">
                    Nomor WhatsApp <span class="text-red-500">*</span>
                </label>
                <div class="flex rounded-xl border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-primary-200 focus-within:border-primary-500">
                    <span class="inline-flex items-center px-3 bg-gray-50 text-gray-500 text-sm border-r border-gray-300">
                        +62
                    </span>
                    <input type="text" 
                           name="no_wa" 
                           id="no_wa" 
                           value="{{ old('no_wa', $parentProfile->phone ?? '') }}"
                           required
                           class="flex-1 px-4 py-2.5 text-sm focus:outline-none"
                           placeholder="81234567890">
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Contoh: 81234567890 (tanpa angka 0 di depan)
                </p>
            </div>

            {{-- Alamat --}}
            <div>
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat Lengkap <span class="text-red-500">*</span>
                </label>
                <textarea name="alamat" 
                          id="alamat" 
                          rows="3"
                          required
                          class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                          placeholder="Contoh: Jl. Merdeka No. 123, RT 01 RW 02, Kel. Sukajadi, Kec. Sukasari, Kota Bandung">{{ old('alamat', $parentProfile->address ?? '') }}</textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    💾 Simpan & Lanjut
                </button>
            </div>
        </form>

        <p class="text-xs text-gray-400 text-center mt-4">
            * Data orang tua hanya digunakan untuk notifikasi presensi dan tidak akan disalahgunakan
        </p>
    </div>
</x-layouts.guest>