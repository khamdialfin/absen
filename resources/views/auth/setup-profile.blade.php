<x-layouts.guest title="Setup Profil">
    <div class="text-center mb-6">
        <div class="mx-auto h-16 w-16 rounded-full overflow-hidden border-2 border-white/30 mb-3">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" alt="Avatar" class="h-full w-full object-cover">
            @else
                <div class="h-full w-full bg-white/20 flex items-center justify-center">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0" />
                    </svg>
                </div>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-white mb-1">Lengkapi Profil</h1>
        <p class="text-primary-200 text-sm">Langkah 2 dari 2 — Isi data pribadi Anda</p>
    </div>

    {{-- Step Indicator --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        <div class="flex items-center gap-1.5">
            <div class="h-8 w-8 rounded-full bg-white/30 text-white flex items-center justify-center text-sm font-bold">✓</div>
            <span class="text-white/80 text-xs font-medium">Password</span>
        </div>
        <div class="w-8 h-0.5 bg-white/50"></div>
        <div class="flex items-center gap-1.5">
            <div class="h-8 w-8 rounded-full bg-white text-primary-700 flex items-center justify-center text-sm font-bold">2</div>
            <span class="text-white text-xs font-medium">Profil</span>
        </div>
    </div>

    <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl p-6 sm:p-8">
        @if(session('success'))
            <div class="mb-5 rounded-lg bg-green-50 border border-green-200 p-3">
                <p class="text-sm text-green-700">✅ {{ session('success') }}</p>
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

        <form method="POST" action="{{ route('profile.store') }}" class="space-y-4">
            @csrf

            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                       placeholder="Nama lengkap Anda">
            </div>

            {{-- NIS --}}
            <div>
                <label for="nis" class="block text-sm font-medium text-gray-700 mb-1">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" id="nis" value="{{ old('nis', $user->nis) }}" required
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                       placeholder="Contoh: 12345678">
            </div>

            {{-- Kelas & No Absen --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <select name="kelas" id="kelas" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none">
                        <option value="">Pilih</option>
                        @foreach(['X-1','X-2','X-3','X-4','X-5','XI-1','XI-2','XI-3','XI-4','XI-5','XII-1','XII-2','XII-3','XII-4','XII-5'] as $k)
                            <option value="{{ $k }}" {{ old('kelas', $user->kelas) == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="no_absen" class="block text-sm font-medium text-gray-700 mb-1">No. Absen</label>
                    <input type="text" name="no_absen" id="no_absen" value="{{ old('no_absen', $user->no_absen) }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none"
                           placeholder="Contoh: 12">
                </div>
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer rounded-xl border border-gray-200 px-4 py-2.5 hover:bg-gray-50 transition-colors flex-1">
                        <input type="radio" name="gender" value="L" {{ old('gender', $user->gender) == 'L' ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500" required>
                        <span class="text-sm text-gray-700">👦 Laki-laki</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer rounded-xl border border-gray-200 px-4 py-2.5 hover:bg-gray-50 transition-colors flex-1">
                        <input type="radio" name="gender" value="P" {{ old('gender', $user->gender) == 'P' ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">👧 Perempuan</span>
                    </label>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-all hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                ✅ Simpan & Masuk Dashboard
            </button>
        </form>
    </div>
</x-layouts.guest>
