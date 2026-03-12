<x-layouts.guest title="Profil Orang Tua">
    {{-- Header --}}
    <div class="text-center mb-4">
        <div class="mx-auto rounded-circle overflow-hidden border border-2 mb-2"
             style="width:64px;height:64px;border-color:rgba(255,255,255,0.3)!important;">
            <div class="w-100 h-100 d-flex align-items-center justify-content-center"
                 style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-people-fill text-white fs-3"></i>
            </div>
        </div>

        <h1 class="h4 fw-bold text-white mb-1">Profil Orang Tua</h1>
        <p class="text-white-50 small">
            Langkah 3 dari 3 — Data orang tua / wali
        </p>
    </div>

    {{-- Step Indicator (MENYESUAIKAN STEP 2) --}}
    <div class="d-flex align-items-center justify-content-center gap-2 mb-4">

        {{-- Step 1 --}}
        <div class="d-flex align-items-center gap-1">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold small text-white"
                 style="width:32px;height:32px;background:rgba(255,255,255,0.3);">
                ✓
            </div>
            <span class="small" style="color:rgba(255,255,255,0.7);">Password</span>
        </div>

        <div style="width:32px;height:2px;background:rgba(255,255,255,0.5);"></div>

        {{-- Step 2 --}}
        <div class="d-flex align-items-center gap-1">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold small text-white"
                 style="width:32px;height:32px;background:rgba(255,255,255,0.3);">
                ✓
            </div>
            <span class="small" style="color:rgba(255,255,255,0.7);">Profil</span>
        </div>

        <div style="width:32px;height:2px;background:rgba(255,255,255,0.5);"></div>

        {{-- Step 3 --}}
        <div class="d-flex align-items-center gap-1">
            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold small"
                 style="width:32px;height:32px;">
                3
            </div>
            <span class="text-white small fw-medium">Profil Orang Tua</span>
        </div>
    </div>

    {{-- Card --}}
    <div class="card border-0 shadow-lg"
         style="background:rgba(255,255,255,0.95);backdrop-filter:blur(8px);">
        <div class="card-body p-4">

            {{-- Error --}}
            @if(session('error'))
                <div class="alert alert-danger py-2 small">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Info --}}
            <div class="alert alert-info small">
                📢 Nomor WhatsApp orang tua akan digunakan untuk notifikasi presensi siswa.
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('profile.parent.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-medium">
                        Nama Orang Tua / Wali
                    </label>
                    <input type="text"
                           name="nama_ortu"
                           class="form-control"
                           value="{{ old('nama_ortu', $parentProfile->name ?? '') }}"
                           required
                           placeholder="Contoh: Budi Santoso">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-medium">
                        Nomor WhatsApp
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">+62</span>
                        <input type="text"
                               name="no_wa"
                               class="form-control"
                               value="{{ old('no_wa', $parentProfile->phone ?? '') }}"
                               required
                               placeholder="81234567890">
                    </div>
                    <small class="text-muted">
                        Tanpa angka 0 di depan
                    </small>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-medium">
                        Alamat Lengkap
                    </label>
                    <textarea name="alamat"
                              class="form-control"
                              rows="3"
                              required
                              placeholder="Alamat lengkap orang tua">{{ old('alamat', $parentProfile->address ?? '') }}</textarea>
                </div>

                <button type="submit"
                        class="btn btn-primary w-100 fw-semibold">
                     Simpan & Selesai
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>