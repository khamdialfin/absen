<x-layouts.app title="QR Code">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Generate QR Code</h1>
        <p class="text-muted small">QR Code untuk absensi — hanya aktif jika Anda berada di area sekolah</p>
    </div>


    <div class="mx-auto" style="max-width:480px;">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body p-4">
                {{-- Loading State --}}
                <div id="qr-loading" class="py-5">
                    <div class="spinner-border text-primary mb-3" role="status"><span class="visually-hidden">Loading...</span></div>
                    <p class="text-muted small">Mengambil lokasi Anda...</p>
                    <p class="text-muted" style="font-size:0.7rem;">Izinkan akses lokasi di browser Anda</p>
                </div>

                {{-- Error State --}}
                <div id="qr-error" class="py-5" style="display:none;">
                    <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 mb-3" style="width:64px;height:64px;">
                        <i class="bi bi-geo-alt-fill text-danger fs-3"></i>
                    </div>
                    <p id="error-message" class="small fw-semibold text-danger mb-1"></p>
                    <p id="error-detail" class="text-muted" style="font-size:0.75rem;"></p>
                    <button onclick="requestLocation()" class="btn btn-primary btn-sm mt-2">🔄 Coba Lagi</button>
                </div>

                {{-- QR Code Display --}}
                <div id="qr-display" style="display:none;">
                    <div class="mb-3">
                        <span class="badge bg-success">✅ Lokasi valid — Jarak: <span id="distance-value">0</span>m</span>
                    </div>
                    <div id="qr-code" class="d-flex align-items-center justify-content-center p-3 bg-white rounded-3 border mx-auto" style="max-width:320px;"></div>
                    <div id="qr-token" class="mt-2 text-center">
                        <p class="text-muted mb-0" style="font-size:0.7rem;">Kode:</p>
                        <span id="token-text" class="font-monospace fs-4 fw-bold" style="letter-spacing:0.3em;"></span>
                    </div>
                    <p class="text-muted mt-3" style="font-size:0.75rem;">QR Code berlaku selama 5 menit. Tunjukkan ke Sekertaris untuk di-scan.</p>
                    <button onclick="refreshQr()" class="btn btn-primary btn-sm">🔄 Refresh QR Code</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const schoolLat = {{ $schoolLat }};
        const schoolLng = {{ $schoolLng }};
        const schoolRadius = {{ $schoolRadius }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function requestLocation() {
            document.getElementById('qr-loading').style.display = '';
            document.getElementById('qr-error').style.display = 'none';
            document.getElementById('qr-display').style.display = 'none';

            if (!navigator.geolocation) {
                showError('Browser Anda tidak mendukung Geolocation', 'Gunakan browser modern seperti Chrome atau Firefox');
                return;
            }

            navigator.geolocation.getCurrentPosition(onPositionSuccess, onPositionError, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0,
            });
        }

        async function onPositionSuccess(position) {
            const { latitude, longitude } = position.coords;

            try {
                const response = await fetch('{{ route("siswa.qr.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ latitude, longitude }),
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('qr-loading').style.display = 'none';
                    document.getElementById('qr-display').style.display = '';
                    document.getElementById('qr-code').innerHTML = data.qr_svg;
                    document.getElementById('distance-value').textContent = data.distance;
                    document.getElementById('token-text').textContent = data.token || '';
                } else {
                    showError(data.message, 'Jarak Anda: ' + data.distance + 'm | Radius: ' + schoolRadius + 'm');
                }
            } catch (error) {
                showError('Gagal menghubungi server', 'Periksa koneksi internet Anda');
            }
        }

        function onPositionError(error) {
            var messages = {
                1: ['Izin lokasi ditolak', 'Aktifkan izin lokasi di pengaturan browser Anda'],
                2: ['Lokasi tidak tersedia', 'Pastikan GPS aktif di perangkat Anda'],
                3: ['Timeout', 'Waktu permintaan habis. Coba lagi di area terbuka'],
            };
            var m = messages[error.code] || ['Gagal mendapatkan lokasi', error.message];
            showError(m[0], m[1]);
        }

        function showError(message, detail) {
            document.getElementById('qr-loading').style.display = 'none';
            document.getElementById('qr-error').style.display = '';
            document.getElementById('error-message').textContent = message;
            document.getElementById('error-detail').textContent = detail || '';
        }

        function refreshQr() { requestLocation(); }

        // Auto-request location on page load
        requestLocation();
    </script>
</x-layouts.app>
