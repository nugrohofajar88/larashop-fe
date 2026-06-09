<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Shipment Settings">
    <section class="space-y-6" data-admin-shipment-settings data-destination-search-url="{{ route('admin.shipments.settings.destination-search') }}">
        <x-admin.page-header
            eyebrow="Shipment Settings"
            title="Atur origin shipment dan gudang aktif"
            description="Flow origin dibuat konsisten dengan alamat customer: pilih wilayah dari hasil pencarian destinasi, lalu data wilayah tersimpan otomatis untuk kebutuhan ongkir."
        >
            <x-slot:actions>
                <a href="{{ route('admin.shipments.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke monitoring
                </a>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.shipments.index') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700">Monitoring Shipment</a>
            <a href="{{ route('admin.shipments.settings') }}" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white">Shipment Settings</a>
        </div>

        <form method="POST" action="{{ route('admin.shipments.settings.update') }}" class="space-y-6" data-admin-shipment-settings-form>
            @csrf
            @method('PUT')

            <input type="hidden" name="origin_id" value="{{ old('origin_id', $settings['origin_id'] ?? '') }}" data-admin-shipment-origin-id>
            <input type="hidden" name="province" value="{{ old('province', $settings['province'] ?? '') }}" data-admin-shipment-province>
            <input type="hidden" name="city" value="{{ old('city', $settings['city'] ?? '') }}" data-admin-shipment-city>
            <input type="hidden" name="district" value="{{ old('district', $settings['district'] ?? '') }}" data-admin-shipment-district>
            <input type="hidden" name="subdistrict" value="{{ old('subdistrict', $settings['subdistrict'] ?? '') }}" data-admin-shipment-subdistrict>
            <input type="hidden" name="postal_code" value="{{ old('postal_code', $settings['postal_code'] ?? '') }}" data-admin-shipment-postal-code>

            <x-admin.form-section title="Origin default" description="Gudang atau lokasi asal kirim utama yang akan dipakai untuk perhitungan shipping.">
                <div class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" data-admin-shipment-feedback></div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Label origin <span class="text-rose-500">*</span></label>
                        <input type="text" name="label" value="{{ old('label', $settings['label'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Gudang Utama Malang" required data-admin-shipment-label>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Origin ID</label>
                        <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-semibold text-stone-900">
                            {{ $settings['origin_id'] ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Nama PIC <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_name" value="{{ old('contact_name', $settings['contact_name'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Nama penanggung jawab gudang" required data-admin-shipment-contact-name>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Nomor PIC <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="08xxxxxxxxxx" required data-admin-shipment-contact-phone>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-stone-700">Courier tersedia <span class="text-rose-500">*</span></label>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @php
                                $selectedCouriers = old('selected_couriers', $settings['selected_couriers'] ?? ['jnt']);
                            @endphp
                            @foreach (($settings['available_couriers'] ?? []) as $courier)
                                <label class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm font-medium text-stone-700">
                                    <input
                                        type="checkbox"
                                        name="selected_couriers[]"
                                        value="{{ $courier }}"
                                        class="h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500"
                                        @checked(in_array($courier, (array) $selectedCouriers, true))
                                    >
                                    <span>{{ strtoupper($courier) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-stone-500">Pilihan akan disimpan dalam format gabungan seperti <span class="font-semibold">jnt:jne:sicepat</span>.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-stone-700">Provinsi, kota, kecamatan, kode pos <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input
                                type="text"
                                value="{{ old('destination_label', $settings['destination_label'] ?? '') }}"
                                placeholder="Ketik kode pos atau nama wilayah"
                                class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white"
                                autocomplete="off"
                                required
                                data-admin-shipment-destination-input
                            >
                            <div class="mt-2 hidden rounded-2xl border border-stone-200 bg-white shadow-lg" data-admin-shipment-destination-results></div>
                            <p class="mt-2 text-xs text-stone-500" data-admin-shipment-destination-hint>Mulai ketik minimal 3 karakter lalu tunggu sebentar untuk mencari destinasi.</p>
                            <p class="mt-2 text-xs font-medium text-amber-700" data-admin-shipment-destination-status>Wilayah belum dipilih.</p>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-stone-700">Alamat lengkap <span class="text-rose-500">*</span></label>
                        <textarea name="address_line" rows="3" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Nama jalan, nomor rumah, area gudang, patokan" required data-admin-shipment-address-line>{{ old('address_line', $settings['address_line'] ?? '') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-stone-700">Koordinat gudang (lat, long)</label>
                        <input type="text" name="pin_point" value="{{ old('pin_point', $settings['pin_point'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="-7.968106, 112.676096">
                        <p class="mt-1 text-xs text-stone-400">Titik jemput (pickup) gudang untuk hitung ongkir Komerce. Opsional.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-stone-700">Catatan</label>
                        <textarea name="note" rows="2" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Opsional, misalnya area pickup atau jam operasional gudang" data-admin-shipment-note>{{ old('note', $settings['note'] ?? '') }}</textarea>
                    </div>
                </div>
            </x-admin.form-section>

            <div class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white" data-admin-shipment-submit>
                    Simpan shipment settings
                </button>
            </div>
        </form>
    </section>
</x-layouts.admin>
