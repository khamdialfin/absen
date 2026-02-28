<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'HadirIN') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .hero-section {
            min-height: calc(100vh - 72px);
            background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 40%, #2563eb 70%, #3b82f6 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(255,255,255,0.03);
        }
        .feature-card {
            border: none;
            border-radius: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }
        .navbar { backdrop-filter: blur(12px); background: rgba(255,255,255,0.95) !important; }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg border-bottom shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/">
                <img src="{{ asset('images/logo.png') }}" alt="HadirIN" style="height:52px;">
                <span class="text-dark">HadirIN</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm fw-semibold px-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                </a>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="hero-section d-flex align-items-center">
        <div class="container position-relative" style="z-index:1;">
            <div class="row align-items-center">
                <div class="col-lg-7 text-white mb-5 mb-lg-0">
                    <span class="badge bg-white bg-opacity-25 text-white mb-3 px-3 py-2 fw-medium" style="font-size:0.8rem;">
                        <i class="bi bi-stars me-1"></i> Sistem Presensi Digital
                    </span>
                    <h1 class="display-4 fw-800 mb-3" style="line-height:1.1;">
                        HadirIN<br>
                        <span style="color:rgba(255,255,255,0.7);">Berbasis QR Code</span>
                    </h1>
                    <p class="lead mb-4" style="color:rgba(255,255,255,0.8);max-width:500px;">
                        Kelola kehadiran siswa dengan mudah menggunakan teknologi QR Code. Cepat, akurat, dan paperless.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-semibold px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Log In
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <div class="bg-white bg-opacity-10 rounded-4 p-4" style="backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.15);">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center text-white">
                                    <i class="bi bi-qr-code-scan fs-1 mb-2 d-block"></i>
                                    <span class="small fw-medium">Scan QR</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center text-white">
                                    <i class="bi bi-clock-history fs-1 mb-2 d-block"></i>
                                    <span class="small fw-medium">Rekap Otomatis</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center text-white">
                                    <i class="bi bi-file-earmark-bar-graph fs-1 mb-2 d-block"></i>
                                    <span class="small fw-medium">Laporan</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-white bg-opacity-10 rounded-3 p-3 text-center text-white">
                                    <i class="bi bi-wallet2 fs-1 mb-2 d-block"></i>
                                    <span class="small fw-medium">Kas Kelas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="h3 fw-bold">Fitur Utama</h2>
                <p class="text-muted">Semua yang dibutuhkan untuk mengelola kehadiran siswa</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
                                <i class="bi bi-qr-code-scan text-primary fs-4"></i>
                            </div>
                            <h5 class="fw-bold">Absensi QR Code</h5>
                            <p class="text-muted small mb-0">Siswa generate QR dari HP, sekretaris tinggal scan. Anti titip absen karena ada validasi lokasi GPS.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
                                <i class="bi bi-file-earmark-spreadsheet text-success fs-4"></i>
                            </div>
                            <h5 class="fw-bold">Rekap & Laporan</h5>
                            <p class="text-muted small mb-0">Rekap harian, mingguan, bulanan otomatis. Export ke CSV dan print langsung dari browser.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;">
                                <i class="bi bi-wallet2 text-warning fs-4"></i>
                            </div>
                            <h5 class="fw-bold">Kas Kelas</h5>
                            <p class="text-muted small mb-0">Catat pemasukan & pengeluaran kas kelas. Bendahara bisa kelola keuangan dengan mudah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-4 border-top bg-white">
        <div class="container text-center">
            <p class="text-muted small mb-0">&copy; {{ date('Y') }} HadirIN. Sistem Presensi Digital Berbasis QR Code.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
