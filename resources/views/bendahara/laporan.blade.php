<x-layouts.app title="Laporan Kas">
    <style>
        @media print {
            .sidebar, .sidebar-backdrop, .mobile-header, .d-print-none, .top-navbar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            body { background: white !important; }
            .shadow-sm { box-shadow: none !important; }
        }
    </style>

    {{-- Print Header (only visible when printing) --}}
    <div class="d-none d-print-block mb-3">
        <h1 class="h4 fw-bold">Laporan Kas</h1>
        <p class="text-muted small">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4 d-print-none">
        <div>
            <h1 class="h4 fw-bold">Laporan Kas Kelas</h1>
            <p class="text-muted small">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        <form action="{{ route('bendahara.laporan') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm" style="width:auto;">
            <span class="text-muted">-</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm" style="width:auto;">
            <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
        </form>
    </div>

    {{-- Export Buttons --}}
    <div class="d-flex gap-2 mb-3 d-print-none">
        <button onclick="window.print()" class="btn btn-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-printer"></i> Print / PDF</button>
        <button onclick="exportTableToCSV('laporan-kas.csv')" class="btn btn-success btn-sm d-flex align-items-center gap-1"><i class="bi bi-file-earmark-spreadsheet"></i> Excel (CSV)</button>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <p class="small fw-medium text-muted mb-1">Total Pemasukan</p>
                <p class="h4 fw-bold text-success mb-0">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</p>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <p class="small fw-medium text-muted mb-1">Total Pengeluaran</p>
                <p class="h4 fw-bold text-danger mb-0">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</p>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <p class="small fw-medium text-muted mb-1">Saldo Periode Ini</p>
                <p class="h4 fw-bold {{ $saldoPeriode >= 0 ? '' : 'text-danger' }} mb-0">Rp {{ number_format($saldoPeriode, 0, ',', '.') }}</p>
            </div></div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="laporanTable">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">Tanggal</th>
                        <th class="small text-white fw-semibold">Keterangan</th>
                        <th class="small text-white fw-semibold">Tipe</th>
                        <th class="small text-white fw-semibold text-end">Masuk</th>
                        <th class="small text-white fw-semibold text-end">Keluar</th>
                        <th class="small text-white fw-semibold text-end d-print-none">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td class="small text-muted font-monospace">{{ $trx->date->format('d/m/Y') }}</td>
                            <td class="small fw-medium">{{ $trx->description }}</td>
                            <td class="small">
                                @if($trx->type == 'pemasukan')
                                    <span class="badge bg-success">Masuk</span>
                                @else
                                    <span class="badge bg-danger">Keluar</span>
                                @endif
                            </td>
                            <td class="small text-end fw-medium text-success">{{ $trx->type == 'pemasukan' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}</td>
                            <td class="small text-end fw-medium text-danger">{{ $trx->type == 'pengeluaran' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}</td>
                            <td class="small text-end text-muted d-print-none">{{ $trx->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4">Tidak ada data pada periode ini</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold border-top">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL</td>
                        <td class="text-end text-success">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
                        <td class="d-print-none"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("#laporanTable tr");
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                for (var j = 0; j < cols.length; j++) {
                    if (cols[j].classList.contains('d-print-none')) continue;
                    var data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/,/g, "");
                    row.push('"' + data + '"');
                }
                csv.push(row.join(","));
            }
            downloadCSV(csv.join("\n"), filename);
        }
        function downloadCSV(csv, filename) {
            var csvFile = new Blob([csv], {type: "text/csv"});
            var downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }
    </script>
</x-layouts.app>
