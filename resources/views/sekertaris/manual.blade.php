<x-layouts.app title="Input Manual">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Input Manual Kehadiran</h1>
        <p class="text-muted small">Input data izin atau sakit untuk siswa yang tidak hadir fisik</p>
    </div>

    <div style="max-width:640px;">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('sekertaris.attendance.store-manual') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Pilih Siswa --}}
                    <div class="mb-3">
                        <label for="user_id" class="form-label small fw-medium">Nama Siswa</label>
                        <select name="user_id" id="user_id" required class="form-select">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Status</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" name="status" value="izin" {{ old('status') == 'izin' ? 'checked' : '' }} class="form-check-input" id="status-izin" required>
                                <label class="form-check-label small" for="status-izin"><i class="bi bi-clipboard-minus"></i> Izin</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="status" value="sakit" {{ old('status') == 'sakit' ? 'checked' : '' }} class="form-check-input" id="status-sakit">
                                <label class="form-check-label small" for="status-sakit"><i class="bi bi-hospital"></i> Sakit</label>
                            </div>
                        </div>
                        @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label for="notes" class="form-label small fw-medium">Keterangan (Opsional)</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Contoh: Demam tinggi, acara keluarga, dll">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Upload Surat --}}
                    <div class="mb-3">
                        <label for="letter" class="form-label small fw-medium">Upload Surat (Opsional)</label>
                        <input type="file" name="letter" id="letter" accept=".jpg,.jpeg,.png,.pdf" class="form-control">
                        <div class="form-text">Format: JPG, PNG, PDF. Maks 2MB.</div>
                        @error('letter') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-semibold">Simpan Data</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
