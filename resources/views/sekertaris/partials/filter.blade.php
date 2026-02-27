<div class="d-flex flex-wrap align-items-end gap-2 d-print-none">
    {{-- Filter Type --}}
    <div>
        <label for="filter_type" class="d-block small fw-medium mb-1">Tipe Laporan</label>
        <select name="type" id="filter_type" class="form-select form-select-sm" onchange="toggleFilterFields()">
            <option value="daily" {{ request('type', 'daily') == 'daily' ? 'selected' : '' }}>Harian</option>
            <option value="weekly" {{ request('type') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
            <option value="monthly" {{ request('type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
            <option value="yearly" {{ request('type') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
        </select>
    </div>

    {{-- Date Input (Daily) --}}
    <div id="field-daily">
        <label for="date" class="d-block small fw-medium mb-1">Tanggal</label>
        <input type="date" name="date" id="date" value="{{ request('date', now()->toDateString()) }}" class="form-control form-control-sm">
    </div>

    {{-- Week Input (Weekly) --}}
    <div id="field-weekly" style="display:none;">
        <label for="week" class="d-block small fw-medium mb-1">Minggu</label>
        <input type="week" name="week" id="week" value="{{ request('week', now()->format('Y-\WW')) }}" class="form-control form-control-sm">
    </div>

    {{-- Month Input (Monthly) --}}
    <div id="field-monthly" style="display:none;">
        <label for="month" class="d-block small fw-medium mb-1">Bulan</label>
        <select name="month" id="month" class="form-select form-select-sm">
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m)->locale('id')->isoFormat('MMMM') }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Year Input (Monthly or Yearly) --}}
    <div id="field-year" style="display:none;">
        <label for="year" class="d-block small fw-medium mb-1">Tahun</label>
        <select name="year" id="year" class="form-select form-select-sm">
            @foreach(range(2025, now()->year + 1) as $y)
                <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Submit --}}
    <div>
        <button type="submit" class="btn btn-primary btn-sm fw-medium">Tampilkan</button>
    </div>
</div>

<script>
    function toggleFilterFields() {
        var type = document.getElementById('filter_type').value;
        document.getElementById('field-daily').style.display = (type === 'daily') ? '' : 'none';
        document.getElementById('field-weekly').style.display = (type === 'weekly') ? '' : 'none';
        document.getElementById('field-monthly').style.display = (type === 'monthly') ? '' : 'none';
        document.getElementById('field-year').style.display = (type === 'monthly' || type === 'yearly') ? '' : 'none';
    }
    document.addEventListener('DOMContentLoaded', toggleFilterFields);
</script>
