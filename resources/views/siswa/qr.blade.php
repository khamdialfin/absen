<x-layouts.app title="QR Code">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Generate QR Code</h1>
        <p class="text-sm text-gray-500 mt-1">QR Code untuk absensi — hanya aktif jika Anda berada di area sekolah</p>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="rounded-2xl bg-white p-8 shadow-sm border border-gray-100 text-center">
            {{-- Loading State --}}
            <div id="qr-loading" class="py-12">
                <div class="animate-spin h-10 w-10 rounded-full border-4 border-primary-200 border-t-primary-600 mx-auto mb-4"></div>
                <p class="text-sm text-gray-500">Mengambil lokasi Anda...</p>
                <p class="text-xs text-gray-400 mt-1">Izinkan akses lokasi di browser Anda</p>
            </div>

            {{-- Error State --}}
            <div id="qr-error" class="hidden py-12">
                <div class="mx-auto h-16 w-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
                <p id="error-message" class="text-sm font-semibold text-red-700 mb-2"></p>
                <p id="error-detail" class="text-xs text-gray-500"></p>
                <button onclick="requestLocation()"
                        class="mt-4 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
                    🔄 Coba Lagi
                </button>
            </div>

            {{-- QR Code Display --}}
            <div id="qr-display" class="hidden">
                <div class="mb-4">
                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                        ✅ Lokasi valid — Jarak: <span id="distance-value">0</span>m
                    </span>
                </div>
                <div id="qr-code" class="flex items-center justify-center p-4 bg-white rounded-xl border border-gray-100 mx-auto" style="max-width: 320px;"></div>
                <div id="qr-token" class="mt-3 text-center">
                    <p class="text-xs text-gray-400 mb-1">Kode:</p>
                    <span id="token-text" class="font-mono text-2xl font-bold tracking-[0.3em] text-gray-800"></span>
                </div>
                <p class="text-xs text-gray-400 mt-4">QR Code berlaku selama 5 menit. Tunjukkan ke Sekertaris untuk di-scan.</p>
                <button onclick="refreshQr()"
                        class="mt-4 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition-colors">
                    🔄 Refresh QR Code
                </button>
            </div>
        </div>
    </div>

    <script>
        const schoolLat = {{ $schoolLat }};
        const schoolLng = {{ $schoolLng }};
        const schoolRadius = {{ $schoolRadius }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function requestLocation() {
            document.getElementById('qr-loading').classList.remove('hidden');
            document.getElementById('qr-error').classList.add('hidden');
            document.getElementById('qr-display').classList.add('hidden');

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
                    document.getElementById('qr-loading').classList.add('hidden');
                    document.getElementById('qr-display').classList.remove('hidden');
                    document.getElementById('qr-code').innerHTML = data.qr_svg;
                    document.getElementById('distance-value').textContent = data.distance;
                    document.getElementById('token-text').textContent = data.token || '';
                } else {
                    showError(data.message, `Jarak Anda: ${data.distance}m | Radius: ${schoolRadius}m`);
                }
            } catch (error) {
                showError('Gagal menghubungi server', 'Periksa koneksi internet Anda');
            }
        }

        function onPositionError(error) {
            const messages = {
                1: ['Izin lokasi ditolak', 'Aktifkan izin lokasi di pengaturan browser Anda'],
                2: ['Lokasi tidak tersedia', 'Pastikan GPS aktif di perangkat Anda'],
                3: ['Timeout', 'Waktu permintaan habis. Coba lagi di area terbuka'],
            };
            const [title, detail] = messages[error.code] || ['Gagal mendapatkan lokasi', error.message];
            showError(title, detail);
        }

        function showError(message, detail) {
            document.getElementById('qr-loading').classList.add('hidden');
            document.getElementById('qr-error').classList.remove('hidden');
            document.getElementById('error-message').textContent = message;
            document.getElementById('error-detail').textContent = detail || '';
        }

        function refreshQr() {
            requestLocation();
        }

        // Auto-request location on page load
        requestLocation();
    </script>
</x-layouts.app>
