<x-layouts.guest title="Setup Profil">
    <div class="text-center mb-4">
        <div class="mx-auto rounded-circle overflow-hidden border border-2 mb-2" style="width:64px;height:64px;border-color:rgba(255,255,255,0.3)!important;">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" alt="Avatar" class="w-100 h-100" style="object-fit:cover;">
            @else
                <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background:rgba(255,255,255,0.2);">
                    <i class="bi bi-person-fill text-white fs-3"></i>
                </div>
            @endif
        </div>
        <h1 class="h4 fw-bold text-white mb-1">Lengkapi Profil</h1>
        <p class="text-white-50 small">Langkah 2 dari 2 — Isi data pribadi Anda</p>
    </div>

    {{-- Step Indicator --}}
    <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
        <div class="d-flex align-items-center gap-1">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold small text-white" style="width:32px;height:32px;background:rgba(255,255,255,0.3);">✓</div>
            <span class="small" style="color:rgba(255,255,255,0.7);">Password</span>
        </div>
        <div style="width:32px;height:2px;background:rgba(255,255,255,0.5);"></div>
        <div class="d-flex align-items-center gap-1">
            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold small" style="width:32px;height:32px;">2</div>
            <span class="text-white small fw-medium">Profil</span>
        </div>
         <div style="width:32px;height:2px;background:rgba(255,255,255,0.5);"></div>
          <div class="d-flex align-items-center gap-1">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width:32px;height:32px;background:rgba(255,255,255,0.2);color:rgba(255,255,255,0.5);">3</div>
            <span class="small" style="color:rgba(255,255,255,0.5);">Profil Parent</span>
        </div>
    </div>

    <div class="card border-0 shadow-lg" style="background:rgba(255,255,255,0.95);backdrop-filter:blur(8px);">
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success py-2 small">✅ {{ session('success') }}</div>
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

            <form method="POST" action="{{ route('profile.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label small fw-medium">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="form-control" placeholder="Nama lengkap Anda">
                </div>

                <div class="mb-3">
                    <label for="nis" class="form-label small fw-medium">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" id="nis" value="{{ old('nis', $user->nis) }}" required class="form-control" placeholder="Contoh: 12345678">
                </div>

                <div class="row mb-3 g-2">
                    <div class="col-6">
                        <label for="kelas" class="form-label small fw-medium">Kelas</label>
                        <select name="kelas" id="kelas" required class="form-select">
                            <option value="">Pilih</option>
                            @foreach(['XII AKL 1','XII AKL 2','XII AKL 3','XII AKL 4','XII MP 1','XII MP 2','XII MP 3','XII MP 4','XII BD 1','XII BR 1','XII BR 2','XII BR 3','XII TKJ 1','XII TKJ 2','XII TKJ 3','XII TKJ 4','XII PSPT 1','XII PSPT 2','XII RPL 1','XII RPL 2'] as $k)
                                <option value="{{ $k }}" {{ old('kelas', $user->kelas) == $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="no_absen" class="form-label small fw-medium">No. Absen</label>
                        <input type="text" name="no_absen" id="no_absen" value="{{ old('no_absen', $user->no_absen) }}" required class="form-control" placeholder="Contoh: 12">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-medium">Jenis Kelamin</label>
                    <div class="d-flex gap-3">
                        <div class="form-check border rounded-3 px-4 py-2 flex-fill">
                            <input type="radio" name="gender" value="L" {{ old('gender', $user->gender) == 'L' ? 'checked' : '' }} class="form-check-input" id="gender-l" required>
                            <label class="form-check-label small" for="gender-l">👦 Laki-laki</label>
                        </div>
                        <div class="form-check border rounded-3 px-4 py-2 flex-fill">
                            <input type="radio" name="gender" value="P" {{ old('gender', $user->gender) == 'P' ? 'checked' : '' }} class="form-check-input" id="gender-p">
                            <label class="form-check-label small" for="gender-p">👧 Perempuan</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold">✅ Simpan & Masuk Dashboard</button>
            </form>
        </div>
    </div>
</x-layouts.guest>
