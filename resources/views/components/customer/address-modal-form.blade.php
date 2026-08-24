@props([
    'formMode' => 'create',
    'formAddressId' => '',
])

<div class="fixed inset-0 z-[60] hidden overflow-y-auto p-3 backdrop-blur-sm sm:p-6" style="background-color: rgba(12,10,9,0.35);" data-address-modal aria-hidden="true">
    <div class="mx-auto flex min-h-full max-w-2xl items-start justify-center py-6 sm:items-center">
        <section class="w-full overflow-hidden rounded-3xl border border-surface-container-highest bg-surface-container-lowest shadow-2xl" data-address-modal-panel>
            <div class="flex items-start justify-between gap-4 border-b border-surface-container-highest px-5 py-5 sm:px-6">
                <div>
                    <h3 class="font-headline-md text-headline-md text-on-surface" data-address-modal-title>Tambah alamat baru</h3>
                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Isi data singkat, lalu pilih wilayah dari hasil pencarian destinasi.</p>
                </div>
                <button type="button" class="rounded-full border border-surface-container-highest px-3 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-primary hover:text-primary" data-address-modal-close>
                    Tutup
                </button>
            </div>

            <div class="px-5 py-5 sm:px-6">
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

                    <div class="hidden rounded-2xl border border-error-container bg-error-container/40 px-4 py-3 font-body-sm text-body-sm text-on-error-container" data-address-form-feedback></div>

                    <div>
                        <label class="mb-2 block font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Label alamat <span class="text-error">*</span></label>
                        <input type="text" name="label" value="{{ old('label') }}" placeholder="Rumah, Kantor, Gudang" required class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary" data-address-label-input>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Nama Penerima <span class="text-error">*</span></label>
                            <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" required class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary" data-address-recipient-name-input>
                        </div>
                        <div>
                            <label class="mb-2 block font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Nomor HP Penerima <span class="text-error">*</span></label>
                            <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" required class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary" data-address-recipient-phone-input>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="mb-2 block font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Provinsi, kota, kecamatan, kode pos <span class="text-error">*</span></label>
                        <input
                            type="text"
                            value="{{ old('destination_label') }}"
                            placeholder="Ketik kode pos atau nama wilayah"
                            required
                            class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary"
                            autocomplete="off"
                            data-address-destination-input
                        >
                        <div class="mt-2 hidden overflow-hidden rounded-xl border border-surface-container-highest bg-surface-container-lowest shadow-lg" data-address-destination-results></div>
                        <p class="mt-2 font-body-sm text-xs text-on-surface-variant" data-address-destination-hint>Mulai ketik minimal 3 karakter lalu tunggu sebentar untuk mencari destinasi.</p>
                        <p class="mt-2 font-body-sm text-xs font-medium text-amber-700" data-address-destination-status>Wilayah belum dipilih.</p>
                    </div>

                    <div>
                        <label class="mb-2 block font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Alamat lengkap <span class="text-error">*</span></label>
                        <textarea
                            name="address_line"
                            rows="3"
                            placeholder="Nama jalan, nomor rumah, gedung, patokan"
                            required
                            class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary"
                            data-address-line-input
                        >{{ old('address_line') }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Catatan kurir</label>
                        <textarea
                            name="note"
                            rows="2"
                            placeholder="Opsional, misalnya rumah pagar hitam atau titip satpam"
                            class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-2 focus:ring-primary"
                            data-address-note-input
                        >{{ old('note') }}</textarea>
                    </div>

                    <label class="flex items-center gap-3 rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-body-md text-on-surface-variant">
                        <input
                            type="checkbox"
                            name="is_primary"
                            value="1"
                            class="h-4 w-4 rounded border-surface-container-highest text-primary focus:ring-primary"
                            data-address-is-primary-input
                            {{ old('is_primary') ? 'checked' : '' }}
                        >
                        Jadikan alamat utama
                    </label>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-primary px-6 py-3 font-body-md font-bold text-on-primary shadow-lg shadow-primary/20 transition hover:bg-secondary active:scale-95" data-address-submit-button>
                            Simpan alamat
                        </button>
                        <button type="button" class="inline-flex items-center justify-center rounded-2xl border border-surface-container-highest px-6 py-3 font-body-md font-medium text-on-surface-variant transition hover:bg-surface-container-low" data-address-modal-close>
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
