@props([
    'title' => 'Alamat pengiriman',
    'description' => null,
    'addresses' => [],
    'mode' => 'editable',
    'inputName' => 'shipping_addresses',
    'inputValue' => null,
    'emptyText' => 'Belum ada alamat pengiriman tersimpan.',
    'actionLabel' => 'Tambah alamat baru',
])

<x-admin.form-section data-address-book data-address-book-mode="{{ $mode }}" :title="$title" :description="$description">
    @if (isset($actions) || $mode === 'editable')
        <div class="mb-4 flex flex-wrap justify-end gap-3">
            @if (isset($actions))
                {{ $actions }}
            @else
                <button type="button" class="rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white" data-address-add>
                    {{ $actionLabel }}
                </button>
            @endif
        </div>
    @endif

    <script type="application/json" data-address-book-state>@json($addresses)</script>

    @if ($mode === 'editable')
        <input type="hidden" name="{{ $inputName }}" value="{{ $inputValue ?? json_encode($addresses) }}" data-address-book-input>
    @endif

    <div class="space-y-4" data-address-book-list></div>

    <div data-address-book-empty class="rounded-[1.5rem] border border-dashed border-stone-300 bg-stone-50 px-5 py-8 text-sm text-stone-500">
        {{ $emptyText }}
    </div>

    @if ($mode === 'editable')
        @error($inputName)
            <p class="text-sm text-rose-700">{{ $message }}</p>
        @enderror
    @endif

    <div class="fixed inset-0 z-50 hidden overflow-y-auto bg-stone-950/45 p-4 backdrop-blur-sm" data-address-modal aria-hidden="true">
        <div class="mx-auto flex min-h-full max-w-3xl items-center justify-center py-4">
            <div class="w-full rounded-[2rem] bg-white p-5 shadow-2xl sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Alamat Pengiriman</p>
                        <h3 class="mt-2 text-2xl font-semibold text-stone-950" data-address-modal-title>{{ $mode === 'readonly' ? 'Detail Alamat' : 'Alamat Baru' }}</h3>
                    </div>
                    <button type="button" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-semibold text-stone-700" data-address-modal-close>
                        Tutup
                    </button>
                </div>

                <div class="mt-5 {{ $mode === 'readonly' ? '' : 'hidden' }}" data-address-detail-panel></div>

                @if ($mode === 'editable')
                    {{-- Sengaja <div>, bukan <form>: komponen ini sering dipakai DI DALAM
                         form halaman (mis. customer create/edit). Form bersarang dilarang
                         HTML → tag <form> dalam akan dibuang browser dan field jadi tak ter-wire.
                         Data alamat disubmit via JS (input hidden shipping_addresses). --}}
                    <div class="mt-5 space-y-4" data-address-form>
                        <input type="hidden" name="address_id">
                        <input type="hidden" name="destination_id">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700">Nama penerima</label>
                                <input type="text" name="recipient_name" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Nama lengkap">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700">Nomor telepon</label>
                                <input type="text" name="recipient_phone" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Nomor telepon">
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700">Tandai sebagai</label>
                            <div class="flex flex-wrap gap-2" data-address-labels>
                                @foreach (['Rumah', 'Kantor', 'Gudang', 'Toko'] as $label)
                                    <button type="button" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-emerald-400 data-[active=true]:border-emerald-500 data-[active=true]:bg-emerald-50 data-[active=true]:text-emerald-700" data-address-label-option="{{ $label }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                            <input type="hidden" name="label" value="Rumah">
                        </div>

                        <div class="relative">
                            <label class="mb-2 block text-sm font-medium text-stone-700">Cari wilayah (kecamatan / kelurahan)</label>
                            <input type="text" data-address-search autocomplete="off"
                                   data-search-url="{{ route('admin.shipments.settings.destination-search') }}"
                                   class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white"
                                   placeholder="Ketik min. 3 huruf, mis. 'Cibinong' atau 'Sukajadi'">
                            <p class="mt-2 text-xs text-stone-500">Pilih wilayah dari hasil pencarian agar ongkir &amp; booking ekspedisi (Komerce) bisa jalan.</p>
                            <div data-address-search-results class="absolute z-10 mt-1 hidden max-h-64 w-full overflow-auto rounded-2xl border border-stone-200 bg-white shadow-lg"></div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700">Provinsi</label>
                                <input type="text" name="province" readonly class="w-full rounded-2xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-700 outline-none" placeholder="Otomatis dari pencarian">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700">Kota / Kabupaten</label>
                                <input type="text" name="city" readonly class="w-full rounded-2xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-700 outline-none" placeholder="Otomatis dari pencarian">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700">Kecamatan</label>
                                <input type="text" name="district" readonly class="w-full rounded-2xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-700 outline-none" placeholder="Otomatis dari pencarian">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700">Kode pos</label>
                                <input type="text" name="postal_code" readonly class="w-full rounded-2xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-700 outline-none" placeholder="Otomatis dari pencarian">
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700">Kelurahan / Desa</label>
                            <input type="text" name="subdistrict" readonly class="w-full rounded-2xl border border-stone-200 bg-stone-100 px-4 py-3 text-sm text-stone-700 outline-none" placeholder="Otomatis dari pencarian">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700">Alamat jalan</label>
                            <textarea name="address_line" rows="3" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Nama jalan, gedung, nomor rumah"></textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700">Detail lain / patokan</label>
                            <textarea name="address_note" rows="2" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Blok, unit, patokan, atau catatan untuk kurir"></textarea>
                        </div>

                        <label class="flex items-center gap-3 rounded-2xl bg-stone-50 px-4 py-3 text-sm text-stone-700">
                            <input type="checkbox" name="is_primary" class="h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500">
                            Jadikan alamat utama
                        </label>
                    </div>
                @endif

                <div class="mt-6 flex items-center justify-end gap-3">
                    @if ($mode === 'editable')
                        <button type="button" class="rounded-2xl border border-stone-300 px-4 py-3 text-sm font-medium text-stone-700" data-address-modal-cancel>
                            Nanti saja
                        </button>
                        <button type="button" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white" data-address-save>
                            Simpan alamat
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin.form-section>
