<div class="d-flex flex-wrap align-items-end gap-2 d-print-none">
    {{-- Filter Type --}}
    <div>
        <label for="filter_type" class="block text-xs font-medium text-gray-700 mb-1">Tipe Laporan</label>
        <select name="type" id="filter_type" x-model="type"
               class="h-10 rounded-lg border border-gray-300 bg-white pl-2 pr-3 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="daily">Harian</option>
            <option value="weekly">Mingguan</option>
            <option value="monthly">Bulanan</option>
            <option value="yearly">Tahunan</option>
        </select>
    </div>

    {{-- Date Input (Daily) --}}
    <div x-show="type === 'daily'">
        <label for="date" class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
        <input type="date" name="date" id="date" value="{{ request('date', now()->toDateString()) }}"
class="h-10 rounded-lg border border-gray-300 bg-white px-2 text-sm leading-10 shadow-sm focus:border-primary-500 focus:ring-primary-500">
</div>

    {{-- Week Input (Weekly) --}}
    <div x-show="type === 'weekly'" style="display: none;">
        <label for="week" class="block text-xs font-medium text-gray-700 mb-1">Minggu</label>
        <input type="week" name="week" id="week" value="{{ request('week', now()->format('Y-\WW')) }}"
               class="h-10 rounded-lg border border-gray-300 bg-white px-2 text-sm leading-10 shadow-sm focus:border-primary-500 focus:ring-primary-500">
    </div>
    
    
    {{-- Month Input (Monthly) --}}
    <div x-show="type === 'monthly'" style="display: none;">
        <label for="month" class="block text-xs font-medium text-gray-700 mb-1">Bulan</label>
        <select name="month" id="month"
                class="h-10 rounded-lg border border-gray-300 bg-white px-2 text-sm leading-10 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m)->locale('id')->isoFormat('MMMM') }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Year Input (Monthly or Yearly) --}}
    <div x-show="type === 'monthly' || type === 'yearly'" style="display: none;">
        <label for="year" class="block text-xs font-medium text-gray-700 mb-1">Tahun</label>
        <select name="year" id="year"
                class="h-10 rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-base px-2">
            @foreach(range(2025, now()->year + 1) as $y)
                <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Submit --}}
    <div>
        <button type="submit"
                class="h-10 rounded-lg bg-primary-600 px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            Tampilkan
        </button>
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
