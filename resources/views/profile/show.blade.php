<x-layouts.app title="Profile Saya">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Profile Saya</h1>
        <p class="text-muted small">Kelola informasi profil dan keamanan akun Anda.</p>
    </div>

    <div class="row g-4">
        {{-- Left Column: Profile Card --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body p-4">
                    <div class="position-relative d-inline-block mb-3">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle border border-3" style="width:128px;height:128px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto border border-3" style="width:128px;height:128px;font-size:2.5rem;font-weight:700;">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width:20px;height:20px;" title="Online"></span>
                    </div>
                    <h2 class="h5 fw-bold mb-1">{{ $user->name }}</h2>
                    <span class="badge bg-light text-dark text-capitalize small">{{ str_replace('_', ' ', $user->role) }}</span>

                    <div class="mt-3 border-top pt-3 text-start">
                        @if($user->nis)
                            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">NIS/NIP</span><span class="fw-medium">{{ $user->nis }}</span></div>
                        @endif
                        @if($user->kelas)
                            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Kelas</span><span class="fw-medium">{{ $user->kelas }}</span></div>
                        @endif
                        <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Email</span><span class="fw-medium text-truncate" style="max-width:150px;" title="{{ $user->email }}">{{ $user->email }}</span></div>
                        <div class="d-flex justify-content-between small"><span class="text-muted">Bergabung</span><span class="fw-medium">{{ $user->created_at->translatedFormat('d M Y') }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Edit Forms --}}
        <div class="col-md-8">
            {{-- Edit Profile --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom"><h3 class="h6 fw-bold mb-0">Edit Profil</h3><p class="text-muted mb-0" style="font-size:0.75rem;">Perbarui informasi dasar akun Anda.</p></div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PATCH')
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-medium">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium">Foto Profil</label>
                            <input type="file" name="avatar" id="avatar" accept="image/*" class="form-control">
                            <div class="form-text">JPG, PNG, atau GIF (Max. 1MB)</div>
                            @error('avatar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @if($user->avatar)
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAvatarModal">
                                        <i class="bi bi-trash"></i> Hapus Foto Profil
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="text-end"><button type="submit" class="btn btn-primary fw-semibold btn-sm">Simpan Perubahan</button></div>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom"><h3 class="h6 fw-bold mb-0">Ganti Password</h3><p class="text-muted mb-0" style="font-size:0.75rem;">Pastikan akun Anda tetap aman dengan password yang kuat.</p></div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')

                        @if(! $user->password && $user->google_id)
                            <div class="alert alert-info small py-2 mb-3">
                                Anda login menggunakan Google. Anda dapat membuat password di sini untuk login manual. Field "Password Saat Ini" boleh dikosongkan.
                            </div>
                        @endif

                        @if($user->password)
                            <div class="mb-3">
                                <label for="current_password" class="form-label small fw-medium">Password Saat Ini</label>
                                <input type="password" name="current_password" id="current_password" required class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="password" class="form-label small fw-medium">Password Baru</label>
                            <input type="password" name="password" id="password" required autocomplete="new-password" class="form-control @error('password') is-invalid @enderror">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label small fw-medium">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" class="form-control">
                        </div>
                        <div class="text-end"><button type="submit" class="btn btn-dark btn-sm fw-semibold">Update Password</button></div>
                    </form>
                </div>
            </div>
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
