<div x-data="{ type: '{{ request('type', 'daily') }}' }" class="flex flex-wrap items-end gap-3 print:hidden">
    {{-- Filter Type --}}
    <div>
        <label for="filter_type" class="block text-xs font-medium text-gray-700 mb-1">Tipe Laporan</label>
        <select name="type" id="filter_type" x-model="type"
                class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
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
               class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
    </div>

    {{-- Week Input (Weekly) --}}
    <div x-show="type === 'weekly'" style="display: none;">
        <label for="week" class="block text-xs font-medium text-gray-700 mb-1">Minggu</label>
        <input type="week" name="week" id="week" value="{{ request('week', now()->format('Y-\WW')) }}"
               class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
    </div>

    {{-- Month Input (Monthly) --}}
    <div x-show="type === 'monthly'" style="display: none;">
        <label for="month" class="block text-xs font-medium text-gray-700 mb-1">Bulan</label>
        <select name="month" id="month"
                class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
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
                class="rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm">
            @foreach(range(2025, now()->year + 1) as $y)
                <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Submit Button --}}
    <div>
        <button type="submit"
                class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
            Tampilkan
        </button>
    </div>
</div>
