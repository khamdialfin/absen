<x-layouts.guest title="Register">
    <div class="text-center mb-4">
        <div class="mx-auto d-flex align-items-center justify-content-center rounded-3 mb-3" style="width:64px;height:64px;background:rgba(255,255,255,0.2);backdrop-filter:blur(8px);">
            <i class="bi bi-person-plus-fill text-white fs-3"></i>
        </div>
        <h1 class="h3 fw-bold text-white mb-1">Daftar Akun</h1>
        <p class="text-white-50 small">Buat akun baru menggunakan Google untuk mulai absensi</p>
    </div>

    <div class="card border-0 shadow-lg" style="background:rgba(255,255,255,0.95);backdrop-filter:blur(8px);">
        <div class="card-body p-4">
            @if(session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif

            <h2 class="h5 fw-semibold text-center mb-1">Buat Akun Baru</h2>
            <p class="text-center text-muted small mb-3">Gunakan akun Google sekolah untuk mendaftar</p>

            <div class="alert alert-info small mb-3">
                <p class="fw-medium mb-1">Cara mendaftar:</p>
                <ol class="mb-0 ps-3" style="font-size:0.8rem;">
                    <li>Klik tombol "Daftar dengan Google"</li>
                    <li>Pilih akun Google sekolah Anda</li>
                    <li>Buat password untuk akun Anda</li>
                    <li>Selesai! Anda bisa mulai absensi</li>
                </ol>
            </div>

            <a href="{{ route('google.redirect') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 py-2 w-100">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span class="fw-semibold">Daftar dengan Google</span>
            </a>

            <p class="text-center small text-muted mt-3 mb-0">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none">Login di sini</a>
            </p>
        </div>
    </div>
</x-layouts.guest>
