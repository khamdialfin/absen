<x-layouts.app title="Kelola Akun Kelas">
    <div class="mb-4">
        <h1 class="h4 fw-bold">Kelola Akun Kelas {{ $kelas }}</h1>
        <p class="text-muted small">Daftar akun yang terdaftar di kelas Anda.</p>
    </div>

    {{-- Create Account Form --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom">
            <h2 class="h6 fw-bold mb-0"><i class="bi bi-person-plus"></i> Buat Akun Baru</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('walikelas.users.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="name" class="form-label small fw-semibold">Nama</label>
                        <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="email" class="form-label small fw-semibold">Email</label>
                        <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="email@contoh.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="password" class="form-label small fw-semibold">Password</label>
                        <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Min. 6 karakter">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="role" class="form-label small fw-semibold">Role</label>
                        <select class="form-select form-select-sm @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">Pilih...</option>
                            <option value="sekertaris" {{ old('role') == 'sekertaris' ? 'selected' : '' }}>Sekretaris</option>
                            <option value="bendahara" {{ old('role') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus-lg"></i> Buat Akun
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Sekretaris Section --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-bold mb-0"><i class="bi bi-person-badge"></i> Sekretaris</h2>
            <span class="badge bg-primary bg-opacity-75">{{ $sekretaris->count() }} akun</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">No</th>
                        <th class="small text-white fw-semibold">Nama</th>
                        <th class="small text-white fw-semibold">Email</th>
                        <th class="small text-white fw-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sekretaris as $user)
                        <tr>
                            <td class="small text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:28px;height:28px;font-size:0.65rem;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                    @endif
                                    <span class="small fw-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="small text-muted">{{ $user->email }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted small py-3">Belum ada sekretaris terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bendahara Section --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-bold mb-0"><i class="bi bi-wallet2"></i> Bendahara</h2>
            <span class="badge bg-warning bg-opacity-75 text-dark">{{ $bendahara->count() }} akun</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">No</th>
                        <th class="small text-white fw-semibold">Nama</th>
                        <th class="small text-white fw-semibold">Email</th>
                        <th class="small text-white fw-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bendahara as $user)
                        <tr>
                            <td class="small text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;">
                                    @else
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fw-bold" style="width:28px;height:28px;font-size:0.65rem;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                    @endif
                                    <span class="small fw-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="small text-muted">{{ $user->email }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted small py-3">Belum ada bendahara terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Students Section --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-bold mb-0"><i class="bi bi-people"></i> Siswa</h2>
            <span class="badge bg-success bg-opacity-75">{{ $students->count() }} siswa</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="small text-white fw-semibold">No</th>
                        <th class="small text-white fw-semibold">Nama</th>
                        <th class="small text-white fw-semibold">Email</th>
                        <th class="small text-white fw-semibold">NIS</th>
                        <th class="small text-white fw-semibold">L/P</th>
                        <th class="small text-white fw-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $user)
                        <tr>
                            <td class="small text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;">
                                    @else
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold" style="width:28px;height:28px;font-size:0.65rem;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                    @endif
                                    <span class="small fw-medium">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="small text-muted">{{ $user->email }}</td>
                            <td class="small text-muted">{{ $user->nis ?? '—' }}</td>
                            <td class="small text-muted">{{ $user->gender ?? '—' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}" title="Hapus akun">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted small py-4"><i class="bi bi-people fs-3 d-block mb-2"></i>Belum ada siswa terdaftar di kelas ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Delete Confirmation Modals --}}
    @foreach($sekretaris->merge($bendahara)->merge($students) as $user)
        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 text-danger" style="width:56px;height:56px;font-size:1.5rem;">
                                <i class="bi bi-person-x"></i>
                            </span>
                        </div>
                        <h5 class="fw-bold mb-2">Hapus Akun?</h5>
                        <p class="text-muted small mb-1">Apakah Anda yakin ingin menghapus akun:</p>
                        <p class="fw-semibold mb-1">{{ $user->name }}</p>
                        <p class="text-muted small mb-3">{{ $user->email }}</p>
                        <p class="text-danger small mb-3"><i class="bi bi-exclamation-triangle"></i> Semua data kehadiran akun ini juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-light btn-sm fw-medium" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ route('walikelas.users.destroy', $user) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm fw-medium">Ya, Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-layouts.app>
