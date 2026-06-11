<x-layouts.admin title="Admin Sobat Akar Tani Kimia | QRIS Pembayaran">
    <section class="space-y-6">
        <x-admin.page-header
            eyebrow="Pembayaran QRIS"
            title="Kelola QRIS (QRISLY)"
            description="Upload QRIS statis toko untuk dipakai membuat QRIS dinamis saat checkout. QRIS yang aktif dipakai untuk semua transaksi."
        />

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        {{-- Status integrasi --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-4 text-sm">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                <span>Status QRISLY:
                    @if (($meta['enabled'] ?? false))
                        <span class="font-semibold text-emerald-700">Aktif</span>
                    @else
                        <span class="font-semibold text-rose-600">Nonaktif</span> (cek <code>KOMERCE_QRISLY_ENABLED</code>)
                    @endif
                </span>
                <span>QRIS aktif:
                    <span class="font-semibold text-stone-900">{{ ($meta['active_qris_id'] ?? '') !== '' ? $meta['active_qris_id'] : 'belum ada' }}</span>
                </span>
            </div>
        </div>

        {{-- Form upload --}}
        <x-admin.form-section title="Upload QRIS baru" description="Gambar QRIS statis (PNG/JPG, maks 5MB). QRIS yang baru di-upload otomatis jadi aktif.">
            <form method="POST" action="{{ route('admin.qris.upload') }}" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-[1fr_auto] sm:items-end">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">Nama / label QRIS <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" maxlength="100" required placeholder="mis. GoPay Toko Utama"
                               class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-stone-700">Gambar QRIS <span class="text-rose-500">*</span></label>
                        <input type="file" name="qris_image" accept="image/png,image/jpeg" required
                               class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-stone-900 file:px-3 file:py-2 file:text-white">
                    </div>
                </div>
                <button type="submit" class="h-fit rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Upload & Aktifkan</button>
            </form>
        </x-admin.form-section>

        {{-- Daftar QRIS --}}
        <x-admin.form-section title="Daftar QRIS" description="QRIS yang sudah di-upload. Hanya satu yang aktif pada satu waktu.">
            @if (empty($qrisList))
                <p class="text-sm text-stone-500">Belum ada QRIS. Upload di atas.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-stone-500">
                            <tr class="border-b border-stone-200">
                                <th class="py-2 pr-4">Nama</th>
                                <th class="py-2 pr-4">Merchant</th>
                                <th class="py-2 pr-4">Provider</th>
                                <th class="py-2 pr-4">qris_id</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2 pr-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qrisList as $q)
                                <tr class="border-b border-stone-100">
                                    <td class="py-3 pr-4 font-semibold text-stone-900">{{ $q['name'] }}</td>
                                    <td class="py-3 pr-4 text-stone-600">{{ $q['merchant_name'] ?: '-' }}</td>
                                    <td class="py-3 pr-4 text-stone-600">{{ $q['provider'] ?: '-' }}</td>
                                    <td class="py-3 pr-4 font-mono text-xs text-stone-500">{{ $q['qris_id'] }}</td>
                                    <td class="py-3 pr-4">
                                        @if ($q['is_active'])
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Aktif</span>
                                        @else
                                            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-500">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4">
                                        <div class="flex items-center justify-end gap-2">
                                            @unless ($q['is_active'])
                                                <form method="POST" action="{{ route('admin.qris.activate', $q['id']) }}">
                                                    @csrf
                                                    <button type="submit" class="rounded-xl border border-emerald-300 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50">Aktifkan</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.qris.delete', $q['id']) }}" onsubmit="return confirm('Hapus QRIS ini dari daftar?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-xl border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-admin.form-section>
    </section>
</x-layouts.admin>
