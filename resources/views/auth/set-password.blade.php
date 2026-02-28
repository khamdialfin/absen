<x-layouts.guest title="Set Password">
    <div class="text-center mb-4">
        <div class="mx-auto d-flex align-items-center justify-content-center rounded-3 mb-3" style="width:64px;height:64px;background:rgba(255,255,255,0.2);backdrop-filter:blur(8px);">
            <i class="bi bi-lock-fill text-white fs-3"></i>
        </div>
        <h1 class="h4 fw-bold text-white mb-1">Buat Password</h1>
        <p class="text-white-50 small">Langkah 1 dari 2 — Buat password untuk keamanan akun Anda</p>
    </div>

    {{-- Step Indicator --}}
    <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
        <div class="d-flex align-items-center gap-1">
            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold small" style="width:32px;height:32px;">1</div>
            <span class="text-white small fw-medium">Password</span>
        </div>
        <div style="width:32px;height:2px;background:rgba(255,255,255,0.3);"></div>
        <div class="d-flex align-items-center gap-1">
            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width:32px;height:32px;background:rgba(255,255,255,0.2);color:rgba(255,255,255,0.5);">2</div>
            <span class="small" style="color:rgba(255,255,255,0.5);">Profil</span>
        </div>
        <div class="w-8 h-0.5 bg-white/30"></div>
         <div class="flex items-center gap-1.5">
            <div class="h-8 w-8 rounded-full bg-white/20 text-white/60 flex items-center justify-center text-sm font-bold">3</div>
            <span class="text-white/60 text-xs font-medium">Profil parent</span>
        </div>
    </div>

    <div class="card border-0 shadow-lg" style="background:rgba(255,255,255,0.95);backdrop-filter:blur(8px);">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="password" class="form-label small fw-medium">Password Baru</label>
                    <input type="password" name="password" id="password" required minlength="8" class="form-control" placeholder="Minimal 8 karakter">
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label small fw-medium">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="form-control" placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-semibold">Lanjutkan ke Setup Profil →</button>
            </form>
        </div>
    </div>
</x-layouts.guest>
