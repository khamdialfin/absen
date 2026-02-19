<x-layouts.app title="Input Manual">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Input Manual Kehadiran</h1>
        <p class="text-sm text-gray-500 mt-1">Input data izin atau sakit untuk siswa yang tidak hadir fisik</p>
    </div>

    <div class="max-w-2xl">
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <form method="POST" action="{{ route('sekertaris.attendance.store-manual') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Pilih Siswa --}}
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Siswa</label>
                    <select name="user_id" id="user_id" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="izin" {{ old('status') == 'izin' ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500" required>
                            <span class="text-sm text-gray-700">📋 Izin</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="sakit" {{ old('status') == 'sakit' ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary-600 border-gray-300 focus:ring-primary-500">
                            <span class="text-sm text-gray-700">🏥 Sakit</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none resize-none"
                              placeholder="Contoh: Demam tinggi, acara keluarga, dll">{{ old('notes') }}</textarea>
                </div>

                {{-- Upload Surat --}}
                <div>
                    <label for="letter" class="block text-sm font-medium text-gray-700 mb-1.5">Upload Surat (Opsional)</label>
                    <div class="relative">
                        <input type="file" name="letter" id="letter" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 focus:border-primary-500 focus:ring-2 focus:ring-primary-200 focus:outline-none">
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG, PDF. Maks 2MB.</p>
                    @error('letter')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="flex gap-3">
                    <button type="submit"
                            class="rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Simpan Data
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
