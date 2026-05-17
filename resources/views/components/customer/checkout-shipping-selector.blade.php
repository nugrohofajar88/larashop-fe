@props([
    'shippingOptions' => [],
])

@php($selectedShipping = collect($shippingOptions)->firstWhere('selected', true) ?? $shippingOptions[0] ?? [
    'service' => 'Opsi pengiriman belum tersedia',
    'price' => 'Rp0',
    'estimate' => 'belum tersedia',
    'selected' => false,
])

<article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm" data-checkout-selector="shipping">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-stone-950">Layanan pengiriman</h2>
        @if (count($shippingOptions) > 0)
            <button type="button" class="text-sm font-semibold text-emerald-700" data-selector-open>Ubah</button>
        @else
            <button type="button" class="hidden text-sm font-semibold text-emerald-700" data-selector-open>Ubah</button>
        @endif
    </div>
    <div class="mt-4 rounded-2xl bg-stone-50 px-4 py-4">
        <div class="flex items-center justify-between gap-4">
            <p class="font-semibold text-stone-900" data-shipping-service>{{ $selectedShipping['service'] }}</p>
            <p class="font-semibold text-emerald-700" data-shipping-price>{{ $selectedShipping['price'] }}</p>
        </div>
        <p class="mt-1 text-sm text-stone-500" data-shipping-estimate>Estimasi {{ $selectedShipping['estimate'] }}</p>
    </div>

    <div class="fixed inset-0 z-50 hidden bg-stone-950/45 p-4 backdrop-blur-sm" data-selector-modal aria-hidden="true">
        <div class="mx-auto flex min-h-full max-w-2xl items-center justify-center">
            <div class="w-full rounded-[2rem] bg-white p-5 shadow-2xl sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Checkout</p>
                        <h3 class="mt-2 text-2xl font-semibold text-stone-950">Pilih layanan pengiriman</h3>
                    </div>
                    <button type="button" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-semibold text-stone-700" data-selector-close>
                        Tutup
                    </button>
                </div>

                <div class="mt-5 max-h-[60vh] space-y-3 overflow-y-auto pr-1" data-selector-options>
                    @forelse ($shippingOptions as $option)
                        <label class="block cursor-pointer rounded-2xl border {{ $option['selected'] ? 'border-emerald-500 bg-emerald-50/50' : 'border-stone-200 bg-white' }} px-4 py-4 transition" data-selector-option>
                            <div class="flex items-start gap-3">
                                <input
                                type="radio"
                                name="shipping_service"
                                class="mt-1"
                                data-selector-input
                                value="{{ $option['service'] }}"
                                data-option-id="{{ $option['id'] }}"
                                data-service="{{ $option['service'] }}"
                                data-price="{{ $option['price'] }}"
                                data-price-value="{{ $option['price_value'] }}"
                                    data-estimate="Estimasi {{ $option['estimate'] }}"
                                    {{ $option['selected'] ? 'checked' : '' }}
                                >
                                <div class="flex-1">
                                    <div class="flex items-center justify-between gap-4">
                                        <p class="font-semibold text-stone-900">{{ $option['service'] }}</p>
                                        <p class="font-semibold text-emerald-700">{{ $option['price'] }}</p>
                                    </div>
                                    <p class="mt-1 text-sm text-stone-500">Estimasi {{ $option['estimate'] }}</p>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="rounded-2xl border border-dashed border-stone-200 bg-stone-50 px-4 py-5 text-sm text-stone-500">
                            Opsi pengiriman akan muncul setelah alamat tujuan dan origin aktif siap dipakai untuk perhitungan ongkir.
                        </div>
                    @endforelse
                </div>

                @if (count($shippingOptions) > 0)
                    <div class="mt-6 flex justify-end">
                        <button type="button" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white" data-selector-apply>
                            Gunakan layanan ini
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</article>
