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

             {{-- Data Orang Tua Section (Only for Students) --}}
                @if($user->isSiswa())
                <div class="card shadow-sm border-0 mt-4">
                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                             <div class="card-header bg-transparent border-bottom">
                                <h3 class="h6 fw-bold mb-0"> Data Orang Tua / Wali</h3>
                                <p class="text-muted mb-0" style="font-size:0.75rem;"> Informasi kontak orang tua untuk notifikasi presensi</p>
                            </div>
                            <span class="badge {{ $parentProfile ? 'bg-success' : 'bg-warning text-dark' }}
                                d-inline-flex align-items-center gap-1">
                                <i class="bi {{ $parentProfile ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                                {{ $parentProfile ? 'Terisi' : 'Belum Diisi' }}
                            </span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="card-body">
                        <form action="{{ route('profile.parent.update') }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            {{-- Nama --}}
                            <div class="mb-3">
                                <label for="parent_name" class="form-label small fw-semibold">
                                    Nama Orang Tua / Wali <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    id="parent_name"
                                    name="parent_name"
                                    value="{{ old('parent_name', $parentProfile->name ?? '') }}"
                                    class="form-control @error('parent_name') is-invalid @enderror"
                                    required>
                                @error('parent_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- WhatsApp --}}
                            <div class="mb-3">
                                <label for="parent_phone" class="form-label small fw-semibold">
                                    Nomor WhatsApp <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text"
                                        id="parent_phone"
                                        name="parent_phone"
                                        value="{{ old('parent_phone', $parentProfile->phone ?? '') }}"
                                        class="form-control @error('parent_phone') is-invalid @enderror"
                                        placeholder="81234567890"
                                        inputmode="numeric"
                                        required>
                                </div>
                                <small class="text-muted">
                                    Masukkan angka saja, tanpa 0 di depan
                                </small>
                                @error('parent_phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Alamat --}}
                            <div class="mb-4">
                                <label for="parent_address" class="form-label small fw-semibold">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea id="parent_address"
                                        name="parent_address"
                                        rows="3"
                                        class="form-control @error('parent_address') is-invalid @enderror"
                                        required>{{ old('parent_address', $parentProfile->address ?? '') }}</textarea>
                                @error('parent_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Action --}}
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                    {{ $parentProfile ? 'Update Data Orang Tua' : 'Simpan Data Orang Tua' }}
                                </button>
                            </div>
                        </form>

                        {{-- Warning --}}
                        @unless($parentProfile)
                            <div class="alert alert-warning mt-4 d-flex gap-2 align-items-start">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div class="small">
                                    Data orang tua wajib diisi untuk menerima notifikasi WhatsApp presensi.
                                </div>
                            </div>
                        @endunless
                    </div>
                </div>
                @endif  
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

