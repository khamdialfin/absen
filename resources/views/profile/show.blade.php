<x-layouts.app title="Profile Saya">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profile Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Left Column: Profile Card --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                    <div class="relative inline-block mb-4">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-32 w-32 rounded-full object-cover ring-4 ring-gray-50">
                        @else
                            <div class="h-32 w-32 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-4xl font-bold mx-auto ring-4 ring-gray-50">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="absolute bottom-1 right-1 h-6 w-6 bg-green-500 border-2 border-white rounded-full" title="Online"></span>
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 capitalize px-3 py-1 bg-gray-100 rounded-full inline-block mt-2 font-medium">
                        {{ str_replace('_', ' ', $user->role) }}
                    </p>

                    <div class="mt-6 border-t border-gray-100 pt-6 space-y-3 text-left">
                        @if($user->nis)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">NIS/NIP</span>
                                <span class="font-medium text-gray-900">{{ $user->nis }}</span>
                            </div>
                        @endif
                        
                        @if($user->kelas)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Kelas</span>
                                <span class="font-medium text-gray-900">{{ $user->kelas }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Email</span>
                            <span class="font-medium text-gray-900 truncate max-w-[150px]" title="{{ $user->email }}">{{ $user->email }}</span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Bergabung</span>
                            <span class="font-medium text-gray-900">{{ $user->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Edit Forms --}}
            <div class="md:col-span-2 space-y-6">
                
                {{-- Edit Profile Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Edit Profil</h3>
                        <p class="text-sm text-gray-500">Perbarui informasi dasar akun Anda.</p>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                                <div class="mt-1 flex items-center gap-4">
                                    <div class="flex-1">
                                        <input type="file" name="avatar" id="avatar" accept="image/*"
                                            class="block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2.5 file:px-4
                                            file:rounded-lg file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-primary-50 file:text-primary-700
                                            hover:file:bg-primary-100
                                            transition-colors cursor-pointer">
                                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau GIF (Max. 1MB)</p>
                                    </div>
                                </div>
                                @error('avatar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Data Orang Tua Section (Only for Students) --}}
                @if($user->isSiswa())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Data Orang Tua / Wali</h3>
                                <p class="text-sm text-gray-500">Informasi kontak orang tua untuk notifikasi presensi.</p>
                            </div>
                            @if($parentProfile)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Terisi
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Belum Diisi
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('profile.parent.update') }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="parent_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Orang Tua / Wali <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="parent_name" 
                                       id="parent_name" 
                                       value="{{ old('parent_name', $parentProfile->name ?? '') }}"
                                       required
                                       class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors @error('parent_name') border-red-500 @enderror"
                                       placeholder="Contoh: Budi Santoso">
                                @error('parent_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="parent_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nomor WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <div class="flex rounded-lg border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                                    <span class="inline-flex items-center px-3 bg-gray-50 text-gray-500 text-sm border-r border-gray-300">
                                        +62
                                    </span>
                                    <input type="text" 
                                           name="parent_phone" 
                                           id="parent_phone" 
                                           value="{{ old('parent_phone', $parentProfile->phone ?? '') }}"
                                           required
                                           class="flex-1 px-4 py-2.5 text-sm focus:outline-none"
                                           placeholder="81234567890"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Contoh: 81234567890 (cukup masukkan angka, tanpa 0 di depan)
                                </p>
                                @error('parent_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="parent_address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Alamat Lengkap <span class="text-red-500">*</span>
                                </label>
                                <textarea name="parent_address" 
                                          id="parent_address" 
                                          rows="3"
                                          required
                                          class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors @error('parent_address') border-red-500 @enderror"
                                          placeholder="Contoh: Jl. Merdeka No. 123, RT 01 RW 02, Kel. Sukajadi, Kec. Sukasari, Kota Bandung">{{ old('parent_address', $parentProfile->address ?? '') }}</textarea>
                                @error('parent_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ $parentProfile ? 'Update Data Orang Tua' : 'Simpan Data Orang Tua' }}
                                </button>
                            </div>
                        </form>

                        @if(!$parentProfile)
                            <div class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            Data orang tua diperlukan untuk mengirim notifikasi WhatsApp saat Anda melakukan presensi.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Change Password Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Ganti Password</h3>
                        <p class="text-sm text-gray-500">Pastikan akun Anda tetap aman dengan password yang kuat.</p>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            @if(! $user->password && $user->google_id)
                                <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm mb-4">
                                    Anda login menggunakan Google. Anda dapat membuat password di sini untuk login manual.
                                    Field "Password Saat Ini" boleh dikosongkan.
                                </div>
                            @endif

                            @if($user->password)
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                                    <input type="password" name="current_password" id="current_password" required
                                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors @error('current_password') border-red-500 @enderror">
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" name="password" id="password" required autocomplete="new-password"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors">
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- JavaScript untuk membersihkan nomor telepon --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('parent_phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    // Hanya izinkan angka
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Batasi panjang maksimal 15 digit (termasuk kode 62)
                    if (this.value.length > 15) {
                        this.value = this.value.slice(0, 15);
                    }
                });
            }
        });
    </script>
</x-layouts.app>