@props([
    'shippingOptions' => [],
])

@php($selectedShipping = collect($shippingOptions)->firstWhere('selected', true) ?? $shippingOptions[0] ?? [
    'service' => 'Opsi pengiriman belum tersedia',
    'price' => 'Rp0',
    'estimate' => '',
    'selected' => false,
])

<article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow" data-checkout-selector="shipping">
    <div class="flex items-center justify-between">
        <h2 class="font-headline-md text-headline-md text-on-surface">Layanan pengiriman</h2>
        <button type="button" class="{{ count($shippingOptions) > 0 ? '' : 'hidden' }} font-label-lg text-label-lg font-bold text-primary hover:underline" data-selector-open>Ubah</button>
    </div>
    <div class="mt-4 rounded-2xl bg-surface-container-low px-4 py-4">
        <div class="flex items-center justify-between gap-4">
            <p class="font-body-md font-semibold text-on-surface" data-shipping-service>{{ $selectedShipping['service'] }}</p>
            <p class="font-body-md font-semibold text-primary" data-shipping-price>{{ $selectedShipping['price'] }}</p>
        </div>
        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant" data-shipping-estimate>{{ ($selectedShipping['estimate'] ?? '') !== '' ? 'Estimasi '.$selectedShipping['estimate'] : '' }}</p>
    </div>

    <div class="fixed inset-0 z-[60] hidden p-4 backdrop-blur-sm" style="background-color: rgba(12,10,9,0.45);" data-selector-modal aria-hidden="true">
        <div class="mx-auto flex min-h-full max-w-2xl items-center justify-center">
            <div class="w-full rounded-3xl bg-surface-container-lowest p-5 shadow-2xl sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-label-eyebrow text-label-eyebrow uppercase text-primary">Checkout</p>
                        <h3 class="mt-2 font-headline-md text-2xl text-on-surface">Pilih layanan pengiriman</h3>
                    </div>
                    <button type="button" class="rounded-full border border-surface-container-highest px-3 py-2 text-xs font-semibold text-on-surface-variant" data-selector-close>Tutup</button>
                </div>

                <div class="mt-5 max-h-[60vh] space-y-3 overflow-y-auto pr-1" data-selector-options>
                    @forelse ($shippingOptions as $option)
                        <label class="block cursor-pointer rounded-2xl border {{ $option['selected'] ? 'border-primary bg-secondary-container/20' : 'border-surface-container-highest bg-surface-container-lowest' }} px-4 py-4 transition" data-selector-option>
                            <div class="flex items-start gap-3">
                                <input
                                    type="radio"
                                    name="shipping_service"
                                    class="mt-1 text-primary focus:ring-primary"
                                    data-selector-input
                                    value="{{ $option['service'] }}"
                                    data-option-id="{{ $option['id'] }}"
                                    data-service="{{ $option['service'] }}"
                                    data-price="{{ $option['price'] }}"
                                    data-price-value="{{ $option['price_value'] }}"
                                    data-estimate="{{ ($option['estimate'] ?? '') !== '' ? 'Estimasi '.$option['estimate'] : '' }}"
                                    data-is-cod="{{ ($option['is_cod'] ?? false) ? '1' : '0' }}"
                                    {{ $option['selected'] ? 'checked' : '' }}
                                >
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-4">
                                        <p class="font-body-md font-semibold text-on-surface">{{ $option['service'] }}</p>
                                        <p class="font-body-md font-semibold text-primary">{{ $option['price'] }}</p>
                                    </div>
                                    @if (($option['estimate'] ?? '') !== '')
                                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Estimasi {{ $option['estimate'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="rounded-2xl border border-dashed border-surface-container-highest bg-surface-container-low px-4 py-5 font-body-sm text-body-sm text-on-surface-variant">
                            Opsi pengiriman akan muncul setelah alamat tujuan dan origin aktif siap dipakai untuk perhitungan ongkir.
                        </div>
                    @endforelse
                </div>

                @if (count($shippingOptions) > 0)
                    <div class="mt-6 flex justify-end">
                        <button type="button" class="rounded-2xl bg-on-background px-5 py-3 font-body-md font-semibold text-on-primary transition hover:bg-primary" data-selector-apply>Gunakan layanan ini</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</article>
