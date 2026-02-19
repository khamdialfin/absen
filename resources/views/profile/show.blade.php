<x-layouts.app title="Profile Saya">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profile Saya</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Left Column: Profile Card --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                    <div class="relative inline-block mb-4">
                        @if($user->avatar)
                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-32 w-32 rounded-full object-cover ring-4 ring-gray-50">
                        @else
                            <div class="h-32 w-32 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 text-4xl font-bold mx-auto ring-4 ring-gray-50">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="absolute bottom-1 right-1 h-6 w-6 bg-green-500 border-2 border-white rounded-full" title="Online"></span>
                    </div>
                    
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 capitalize px-3 py-1 bg-gray-100 rounded-full inline-block mt-2 font-medium">
                        {{ str_replace('_', ' ', $user->role) }}
                    </p>

                    <div class="mt-6 border-t border-gray-100 pt-6 space-y-3 text-left">
                        @if($user->nis)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">NIS/NIP</span>
                                <span class="font-medium text-gray-900">{{ $user->nis }}</span>
                            </div>
                        @endif
                        
                        @if($user->kelas)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Kelas</span>
                                <span class="font-medium text-gray-900">{{ $user->kelas }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Email</span>
                            <span class="font-medium text-gray-900 truncate max-w-[150px]" title="{{ $user->email }}">{{ $user->email }}</span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Bergabung</span>
                            <span class="font-medium text-gray-900">{{ $user->created_at->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Edit Forms --}}
            <div class="md:col-span-2 space-y-6">
                
                {{-- Edit Profile Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Edit Profil</h3>
                        <p class="text-sm text-gray-500">Perbarui informasi dasar akun Anda.</p>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                                <div class="mt-1 flex items-center gap-4">
                                    <div class="flex-1">
                                        <input type="file" name="avatar" id="avatar" accept="image/*"
                                            class="block w-full text-sm text-gray-500
                                            file:mr-4 file:py-2.5 file:px-4
                                            file:rounded-lg file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-primary-50 file:text-primary-700
                                            hover:file:bg-primary-100
                                            transition-colors cursor-pointer">
                                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau GIF (Max. 1MB)</p>
                                    </div>
                                </div>
                                @error('avatar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Change Password Form --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Ganti Password</h3>
                        <p class="text-sm text-gray-500">Pastikan akun Anda tetap aman dengan password yang kuat.</p>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            @if(! $user->password && $user->google_id)
                                <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm mb-4">
                                    Anda login menggunakan Google. Anda dapat membuat password di sini untuk login manual.
                                    Field "Password Saat Ini" boleh dikosongkan.
                                </div>
                            @endif

                            @if($user->password)
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                                    <input type="password" name="current_password" id="current_password" required
                                        class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors @error('current_password') border-red-500 @enderror">
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" name="password" id="password" required autocomplete="new-password"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                                    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm transition-colors">
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
