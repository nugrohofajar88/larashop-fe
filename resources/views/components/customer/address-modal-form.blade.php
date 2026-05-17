@props([
    'formMode' => 'create',
    'formAddressId' => '',
])

<div class="fixed inset-0 z-40 hidden bg-stone-950/35 p-3 backdrop-blur-sm sm:p-6" data-address-modal aria-hidden="true">
    <div class="mx-auto flex min-h-full max-w-2xl items-center justify-center">
        <section class="max-h-[92vh] w-full overflow-hidden rounded-[1.8rem] border border-stone-200 bg-white shadow-2xl" data-address-modal-panel>
            <div class="flex items-start justify-between gap-4 border-b border-stone-100 px-5 py-5 sm:px-6">
                <div>
                    <h3 class="text-lg font-semibold text-stone-950" data-address-modal-title>Tambah alamat baru</h3>
                    <p class="mt-1 text-sm text-stone-500">Isi data singkat, lalu pilih wilayah dari hasil pencarian destinasi.</p>
                </div>
                <button type="button" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-semibold text-stone-700" data-address-modal-close>
                    Tutup
                </button>
            </div>

            <div class="max-h-[calc(92vh-84px)] overflow-y-auto px-5 py-5 sm:px-6">
                <form method="POST" action="{{ route('addresses.save') }}" class="grid gap-4" data-address-modal-form>
                    @csrf
                    <input type="hidden" name="_address_mode" value="{{ $formMode }}" data-address-mode-field>
                    <input type="hidden" name="_address_id" value="{{ $formAddressId }}" data-address-id-field>
                    <input type="hidden" name="destination_id" value="{{ old('destination_id') }}" data-address-destination-id-field>
                    <input type="hidden" name="province" value="{{ old('province') }}" data-address-province-field>
                    <input type="hidden" name="city" value="{{ old('city') }}" data-address-city-field>
                    <input type="hidden" name="district" value="{{ old('district') }}" data-address-district-field>
                    <input type="hidden" name="subdistrict" value="{{ old('subdistrict') }}" data-address-subdistrict-field>
                    <input type="hidden" name="postal_code" value="{{ old('postal_code') }}" data-address-postal-code-field>

                    <div class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" data-address-form-feedback></div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Label alamat <span class="text-rose-500">*</span></label>
                        <input type="text" name="label" value="{{ old('label') }}" placeholder="Rumah, Kantor, Gudang" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" data-address-label-input>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700">Nama penerima <span class="text-rose-500">*</span></label>
                            <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" data-address-recipient-name-input>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700">Nomor penerima <span class="text-rose-500">*</span></label>
                            <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" data-address-recipient-phone-input>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="mb-2 block text-sm font-medium text-stone-700">Provinsi, kota, kecamatan, kode pos <span class="text-rose-500">*</span></label>
                        <input
                            type="text"
                            value="{{ old('destination_label') }}"
                            placeholder="Ketik kode pos atau nama wilayah"
                            required
                            class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white"
                            autocomplete="off"
                            data-address-destination-input
                        >
                        <div class="mt-2 hidden rounded-2xl border border-stone-200 bg-white shadow-lg" data-address-destination-results></div>
                        <p class="mt-2 text-xs text-stone-500" data-address-destination-hint>Mulai ketik minimal 3 karakter lalu tunggu sebentar untuk mencari destinasi.</p>
                        <p class="mt-2 text-xs font-medium text-amber-700" data-address-destination-status>Wilayah belum dipilih.</p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Alamat lengkap <span class="text-rose-500">*</span></label>
                        <textarea
                            name="address_line"
                            rows="3"
                            placeholder="Nama jalan, nomor rumah, gedung, patokan"
                            required
                            class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white"
                            data-address-line-input
                        >{{ old('address_line') }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Catatan kurir</label>
                        <textarea
                            name="note"
                            rows="2"
                            placeholder="Opsional, misalnya rumah pagar hitam atau titip satpam"
                            class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white"
                            data-address-note-input
                        >{{ old('note') }}</textarea>
                    </div>

                    <label class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-700">
                        <input
                            type="checkbox"
                            name="is_primary"
                            value="1"
                            class="h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500"
                            data-address-is-primary-input
                            {{ old('is_primary') ? 'checked' : '' }}
                        >
                        Jadikan alamat utama
                    </label>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white" data-address-submit-button>
                            Simpan alamat
                        </button>
                        <button type="button" class="inline-flex items-center justify-center rounded-2xl border border-stone-300 px-5 py-3 text-sm font-medium text-stone-700" data-address-modal-close>
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
