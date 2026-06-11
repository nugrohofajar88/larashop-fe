<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Pengaturan Pembayaran">
    @php($inputClass = 'w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white')
    @php($labelClass = 'mb-1 block text-xs font-semibold uppercase tracking-wider text-stone-500')

    <div class="mx-auto max-w-3xl space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-3 rounded-3xl border border-stone-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-stone-900">Rekening Pembayaran</h1>
                <p class="mt-1 text-sm text-stone-500">Rekening aktif akan ditampilkan ke pelanggan di detail pesanan & balasan WhatsApp.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="space-y-1 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Pengaturan toko --}}
        <form method="POST" action="{{ route('admin.payments.store-settings.update') }}" class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-semibold text-stone-900">Pengaturan Toko</h2>
            <p class="mt-1 text-sm text-stone-500">Dipakai untuk konfirmasi WhatsApp pelanggan & booking ekspedisi (Komerce).</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Nomor WhatsApp Toko</label>
                    <input type="text" name="store_whatsapp" value="{{ old('store_whatsapp', $settings['store_whatsapp'] ?? '') }}" placeholder="0812xxxxxxxx" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Nama Toko / Brand</label>
                    <input type="text" name="store_brand" value="{{ old('store_brand', $settings['store_brand'] ?? '') }}" placeholder="Akar Tani Kimia" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Email Toko</label>
                    <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email'] ?? '') }}" placeholder="admin@tokomu.id" class="{{ $inputClass }}">
                </div>
            </div>

            <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                <input type="checkbox" name="unique_code_enabled" value="1" {{ old('unique_code_enabled', ($settings['unique_code_enabled'] ?? true)) ? 'checked' : '' }} class="mt-0.5 h-5 w-5 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500">
                <span>
                    <span class="text-sm font-medium text-stone-900">Aktifkan Kode Unik</span>
                    <span class="block text-xs text-stone-500">Menambah angka unik (mis. Rp101–999) ke total pesanan untuk memudahkan verifikasi transfer. Matikan jika tidak dipakai.</span>
                </span>
            </label>

            {{-- Metode pembayaran yang ditawarkan ke pelanggan (mempengaruhi info bayar di pesan WA & checkout) --}}
            <div class="mt-5">
                <p class="{{ $labelClass }}">Metode Pembayaran</p>
                <div class="mt-2 grid gap-3 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                        <input type="checkbox" name="payment_transfer_enabled" value="1" {{ old('payment_transfer_enabled', ($settings['payment_transfer_enabled'] ?? true)) ? 'checked' : '' }} class="mt-0.5 h-5 w-5 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500">
                        <span>
                            <span class="text-sm font-medium text-stone-900">Transfer Bank</span>
                            <span class="block text-xs text-stone-500">Tampilkan rekening untuk transfer manual.</span>
                        </span>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                        <input type="checkbox" name="payment_qris_enabled" value="1" {{ old('payment_qris_enabled', ($settings['payment_qris_enabled'] ?? true)) ? 'checked' : '' }} class="mt-0.5 h-5 w-5 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500">
                        <span>
                            <span class="text-sm font-medium text-stone-900">QRIS</span>
                            <span class="block text-xs text-stone-500">Kirim QR pembayaran (perlu QRIS aktif di menu QRIS).</span>
                        </span>
                    </label>
                </div>
                <p class="mt-2 text-xs text-stone-400">Boleh aktif dua-duanya. Kalau keduanya mati, sistem otomatis pakai Transfer Bank.</p>
            </div>

            <button type="submit" class="mt-4 rounded-2xl bg-stone-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-stone-800">Simpan Pengaturan</button>
            <p class="mt-2 text-xs text-stone-400">WhatsApp boleh 0812.../62812...; email untuk booking ekspedisi (opsional). Koordinat gudang kini diatur di Shipment Settings.</p>
        </form>

        {{-- Tambah rekening (collapsible) --}}
        <details class="rounded-3xl border border-emerald-200 bg-emerald-50/40 p-5" {{ $errors->any() ? 'open' : '' }}>
            <summary class="inline-flex cursor-pointer select-none items-center gap-2 text-sm font-semibold text-emerald-800">
                <span class="text-lg leading-none">+</span> Tambah Rekening
            </summary>
            <form method="POST" action="{{ route('admin.payments.accounts.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="{{ $labelClass }}">Bank</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" required placeholder="mis. BCA" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">No. Rekening</label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}" required placeholder="1234567890" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Atas Nama</label>
                    <input type="text" name="account_holder" value="{{ old('account_holder') }}" required placeholder="Nama pemilik rekening" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Catatan (opsional)</label>
                    <input type="text" name="note" value="{{ old('note') }}" placeholder="mis. konfirmasi via WA" class="{{ $inputClass }}">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-2xl bg-stone-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-stone-800">Simpan Rekening</button>
                </div>
            </form>
        </details>

        {{-- Daftar rekening (kartu) --}}
        <div class="space-y-4">
            @forelse ($accounts as $account)
                <div data-account-card class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-stone-400">{{ $account['bank_name'] }}</p>
                            <p class="mt-1 text-xl font-bold tracking-wide text-stone-900">{{ $account['account_number'] }}</p>
                            <p class="mt-0.5 text-sm text-stone-500">a.n. {{ $account['account_holder'] }}</p>
                            @if (!empty($account['note']))
                                <p class="mt-1 text-xs text-stone-400">{{ $account['note'] }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-full px-3 py-1 text-[11px] font-semibold {{ $account['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500' }}">
                            {{ $account['is_active'] ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    {{-- Aksi: Edit & Hapus sebaris --}}
                    <div class="mt-4 flex items-center gap-2 border-t border-stone-100 pt-4">
                        <button type="button"
                            onclick="this.closest('[data-account-card]').querySelector('[data-edit-form]').classList.toggle('hidden')"
                            class="inline-flex items-center gap-1 rounded-2xl border border-stone-200 px-4 py-2 text-sm font-semibold text-stone-700 transition hover:bg-stone-50">Edit</button>
                        <form method="POST" action="{{ route('admin.payments.accounts.destroy', $account['id']) }}" onsubmit="return confirm('Hapus rekening {{ $account['bank_name'] }} ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-2xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
                        </form>
                    </div>

                    {{-- Form edit (toggle, full width di bawah) --}}
                    <div data-edit-form class="mt-4 hidden">
                        <form method="POST" action="{{ route('admin.payments.accounts.update', $account['id']) }}" class="grid gap-3 sm:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="{{ $labelClass }}">Bank</label>
                                <input type="text" name="bank_name" value="{{ $account['bank_name'] }}" required class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">No. Rekening</label>
                                <input type="text" name="account_number" value="{{ $account['account_number'] }}" required class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Atas Nama</label>
                                <input type="text" name="account_holder" value="{{ $account['account_holder'] }}" required class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Catatan</label>
                                <input type="text" name="note" value="{{ $account['note'] }}" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">Urutan</label>
                                <input type="number" name="sort_order" value="{{ $account['sort_order'] }}" min="0" class="{{ $inputClass }}">
                            </div>
                            <label class="flex items-center gap-2 pt-7 text-sm text-stone-700">
                                <input type="checkbox" name="is_active" value="1" {{ $account['is_active'] ? 'checked' : '' }} class="h-5 w-5 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500">
                                Aktif (tampil ke pelanggan)
                            </label>
                            <div class="sm:col-span-2">
                                <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">Belum ada rekening. Tambahkan lewat tombol di atas.</div>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
