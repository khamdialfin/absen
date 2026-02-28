<x-layouts.app title="Scan QR Code">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-0">Scanner QR Code</h1>
            <p class="text-muted small mb-0">Scan QR Code siswa untuk absensi</p>
        </div>
        <a href="{{ route('sekertaris.recap') }}" class="btn btn-outline-secondary btn-sm fw-semibold bg-primary text-white"><i class="bi bi-clipboard-data"></i> Lihat Rekapan</a>
    </div>

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-6">
            {{-- Session Status (Read-Only) --}}
            <div class="card border-0 shadow-sm mb-3 rounded-2">
                <div class="card-body">
                    <h2 class="small fw-semibold text-muted mb-3"><i class="bi bi-info-circle me-1"></i>Status Sesi Absensi</h2>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="rounded-3 border border-2 p-3 text-center {{ $morningActive ? 'border-success bg-success bg-opacity-10' : 'border-secondary-subtle bg-light' }}">
                                <span class="fs-2"><i class="bi bi-sunrise-fill"></i></span>
                                <p class="small fw-semibold mb-0 mt-0">Sesi Pagi</p>
                                <p class="mb-0 {{ $morningActive ? 'text-success fw-semibold' : 'text-muted' }}" style="font-size:0.7rem;">
                                    {{ $morningActive ? '● AKTIF' : '○ NONAKTIF' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-3 border border-2 p-3 text-center {{ $afternoonActive ? 'border-primary bg-primary bg-opacity-10' : 'border-secondary-subtle bg-light' }}">
                                <span class="fs-2"><i class="bi bi-sunset-fill"></i></span>
                                <p class="small fw-semibold mb-0 mt-0">Sesi Sore</p>
                                <p class="mb-0 {{ $afternoonActive ? 'text-primary fw-semibold' : 'text-muted' }}" style="font-size:0.7rem;">
                                    {{ $afternoonActive ? '● AKTIF' : '○ NONAKTIF' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-light rounded-3 p-2 text-center mt-3">
                        <p class="mb-0 text-muted" style="font-size:0.75rem;">
                            Sudah absen: <strong class="text-dark">{{ $todayAttendances->count() }}</strong> / {{ $totalSiswa }} siswa
                        </p>
                    </div>
                    @if(!$morningActive && !$afternoonActive)
                        <div class="alert alert-warning small py-2 mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i> Belum ada sesi aktif. Hubungi Walikelas untuk membuka sesi absensi.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Scanner Area --}}
            @if($morningActive || $afternoonActive)
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="mb-2">
                        <span class="badge {{ $morningActive ? 'bg-success' : 'bg-primary' }}">Sesi {{ $morningActive ? 'Pagi' : 'Sore' }} aktif</span>
                    </div>

                    {{-- Tab: Kamera / Manual --}}
                    <div class="d-flex bg-light rounded-3 p-1 mb-3">
                        <button type="button" onclick="switchScanMode('camera')" id="tab-camera" class="flex-fill btn btn-sm rounded-3 bg-white text-dark shadow-sm fw-semibold border-0">📷 Kamera</button>
                        <button type="button" onclick="switchScanMode('manual')" id="tab-manual" class="flex-fill btn btn-sm rounded-3 text-muted fw-semibold border-0">⌨️ Input Manual</button>
                    </div>

                    {{-- QR Reader (Camera) --}}
                    <div id="panel-camera">
                        <div id="qr-reader" class="rounded-3 overflow-hidden bg-dark" style="min-height:300px;"></div>
                    </div>

                    {{-- Manual Input --}}
                    <div id="panel-manual" style="display:none;">
                        <div class="border rounded-3 p-3">
                            <label class="form-label small fw-medium">Masukkan Kode QR</label>
                            <div class="d-flex gap-2">
                                <input type="text" id="manual-token" placeholder="Contoh: AB12CD34" maxlength="8" class="form-control text-uppercase font-monospace" style="letter-spacing:0.15em;">
                                <button type="button" onclick="submitManualToken()" class="btn btn-primary fw-semibold">Scan</button>
                            </div>
                        </div>
                    </div>

                    {{-- Result Area --}}
                    <div id="scan-result" class="mt-3" style="display:none;">
                        <div id="scan-result-content" class="rounded-3 p-3 small"></div>
                    </div>
                </div>
            </div>
            @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <span class="fs-1"><i class="bi bi-camera"></i></span>
                    <p class="small fw-medium mt-2 mb-0">Scanner tidak aktif</p>
                    <p class="mb-0" style="font-size:0.75rem;">Mulai sesi absensi untuk mengaktifkan scanner</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column: Scan Log --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h2 class="h6 fw-semibold mb-0">Log Kehadiran Hari Ini</h2>
                    <p class="text-muted mb-0" style="font-size:0.75rem;">Total: {{ $todayAttendances->count() }} / {{ $totalSiswa }} siswa</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 rounded-2">
                        <thead class="table-dark">
                            <tr>
                                <th class="small text-white fw-semibold">Nama</th>
                                <th class="small text-white fw-semibold">Masuk</th>
                                <th class="small text-white fw-semibold">Pulang</th>
                                <th class="small text-white fw-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAttendances as $att)
                                <tr>
                                    <td class="small fw-medium">{{ $att->user->name }}</td>
                                    <td class="small text-muted">{{ $att->time_in ?? '—' }}</td>
                                    <td class="small text-muted">{{ $att->time_out ?? '—' }}</td>
                                    <td>
                                        @php
                                            $cls = match($att->status) {
                                                'hadir' => 'bg-success',
                                                'terlambat' => 'bg-warning text-dark',
                                                'izin' => 'bg-info',
                                                'sakit' => 'bg-secondary',
                                                'alfa' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $cls }}">{{ ucfirst($att->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted small py-4">Belum ada data kehadiran hari ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @if($morningActive || $afternoonActive)
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @endif

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;



        // ── Scanner (only if session active) ──
        @if($morningActive || $afternoonActive)
        let html5QrCode = null;
        let isProcessing = false;

        const activeSession = '{{ $morningActive ? "morning" : "afternoon" }}';

        function switchScanMode(mode) {
            document.getElementById('panel-camera').style.display = (mode === 'camera') ? '' : 'none';
            document.getElementById('panel-manual').style.display = (mode === 'manual') ? '' : 'none';

            var cameraTab = document.getElementById('tab-camera');
            var manualTab = document.getElementById('tab-manual');

            if (mode === 'camera') {
                cameraTab.className = 'flex-fill btn btn-sm rounded-3 bg-white text-dark shadow-sm fw-semibold border-0';
                manualTab.className = 'flex-fill btn btn-sm rounded-3 text-muted fw-semibold border-0';
            } else {
                manualTab.className = 'flex-fill btn btn-sm rounded-3 bg-white text-dark shadow-sm fw-semibold border-0';
                cameraTab.className = 'flex-fill btn btn-sm rounded-3 text-muted fw-semibold border-0';
            }
        }

        function startScanner() {
            html5QrCode = new Html5Qrcode("qr-reader");

            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                onScanSuccess,
                () => {}
            ).catch(err => {
                console.error('Camera error:', err);
                showResult(false, 'Gagal mengakses kamera. Gunakan tab "Input Manual".');
            });
        }

        async function onScanSuccess(decodedText) {
            if (isProcessing) return;
            await processScan(decodedText.trim());
        }

        function submitManualToken() {
            const token = document.getElementById('manual-token').value.trim().toUpperCase();
            if (!token) {
                showResult(false, 'Masukkan kode QR terlebih dahulu');
                return;
            }
            processScan(token);
        }

        async function processScan(qrData) {
            isProcessing = true;

            try {
                const response = await fetch('{{ route("sekertaris.attendance.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        qr_data: qrData,
                        session: activeSession,
                    }),
                });

                const data = await response.json();
                showResult(data.success, data.message);

                if (data.success) {
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                showResult(false, 'Terjadi kesalahan saat memproses scan.');
            }

            setTimeout(() => { isProcessing = false; }, 3000);
        }

        function showResult(success, message) {
            const resultDiv = document.getElementById('scan-result');
            const contentDiv = document.getElementById('scan-result-content');

            resultDiv.style.display = '';
            contentDiv.className = `rounded-3 p-3 small fw-medium ${success ? 'bg-success bg-opacity-10 text-success border border-success-subtle' : 'bg-danger bg-opacity-10 text-danger border border-danger-subtle'}`;
            contentDiv.textContent = success ? '✅ ' + message : '❌ ' + message;
        }

        // Auto-start scanner
        document.addEventListener('DOMContentLoaded', startScanner);
        @endif
    </script>
</x-layouts.app>
