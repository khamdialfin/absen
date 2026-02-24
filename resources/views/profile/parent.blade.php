<x-layouts.auth title="Data Orang Tua">
<div class="max-w-md mx-auto mt-16">

    {{-- STEP INDICATOR --}}
    <div class="flex justify-center mb-6 text-sm">
        <span class="text-gray-400">1 Password</span>
        <span class="mx-2 text-gray-400">→</span>
        <span class="text-gray-400">2 Profil</span>
        <span class="mx-2 text-gray-400">→</span>
        <span class="font-semibold text-primary-600">3 Orang Tua</span>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-1">Data Orang Tua</h2>
        <p class="text-sm text-gray-500 mb-4">
            Digunakan untuk notifikasi WhatsApp presensi siswa.
        </p>

        <form method="POST" action="{{ route('profile.ortu.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Nama Orang Tua / Wali</label>
                <input name="nama_ortu" required
                    class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium">No WhatsApp</label>
                <input name="no_wa" placeholder="628xxxxxxxx" required
                    class="w-full rounded-lg border-gray-300">
                <p class="text-xs text-gray-500 mt-1">
                    Contoh: 628123456789
                </p>
            </div>

            <button
                class="w-full bg-primary-600 hover:bg-primary-700 text-white py-2 rounded-lg">
                Selesai & Masuk Dashboard
            </button>
        </form>
    </div>
</div>
</x-layouts.auth>