@props([
    'address',
    'addresses' => [],
])

<article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow" data-checkout-selector="address" data-checkout-refresh-url="{{ route('checkout.data') }}">
    <div class="flex items-center justify-between">
        <h2 class="font-headline-md text-headline-md text-on-surface">Alamat pengiriman</h2>
        <button type="button" class="font-label-lg text-label-lg font-bold text-primary hover:underline" data-selector-open>Ubah</button>
    </div>
    <div class="mt-4 rounded-2xl bg-surface-container-low px-4 py-4 font-body-md text-body-md leading-6 text-on-surface-variant">
        <div class="flex flex-wrap items-center gap-2">
            <p class="font-semibold text-on-surface" data-address-label>{{ $address['label'] }}</p>
            <span class="{{ $address['is_primary'] ? '' : 'hidden' }} rounded-full bg-secondary-container px-3 py-1 text-[11px] font-semibold text-on-secondary-container" data-address-primary-badge>Utama</span>
        </div>
        <p class="mt-1 text-outline" data-address-contact>{{ $address['name'] }} · {{ $address['phone'] }}</p>
        <p class="mt-2" data-address-detail>{{ $address['detail'] }}</p>
        <p class="mt-2 text-xs text-outline {{ $address['note'] !== '' ? '' : 'hidden' }}" data-address-note>{{ $address['note'] }}</p>
    </div>

    <div class="fixed inset-0 z-[60] hidden p-4 backdrop-blur-sm" style="background-color: rgba(12,10,9,0.45);" data-selector-modal aria-hidden="true">
        <div class="mx-auto flex min-h-full max-w-3xl items-center justify-center">
            <div class="w-full rounded-3xl bg-surface-container-lowest p-5 shadow-2xl sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-label-eyebrow text-label-eyebrow uppercase text-primary">Checkout</p>
                        <h3 class="mt-2 font-headline-md text-2xl text-on-surface">Pilih alamat pengiriman</h3>
                    </div>
                    <button type="button" class="rounded-full border border-surface-container-highest px-3 py-2 text-xs font-semibold text-on-surface-variant" data-selector-close>Tutup</button>
                </div>

                <div class="mt-5 max-h-[60vh] space-y-3 overflow-y-auto pr-1" data-selector-options>
                    @foreach ($addresses as $shippingAddress)
                        <label class="block cursor-pointer rounded-2xl border {{ ($shippingAddress['selected'] ?? false) ? 'border-primary bg-secondary-container/20' : 'border-surface-container-highest bg-surface-container-lowest' }} px-4 py-4 transition" data-selector-option>
                            <div class="flex items-start gap-3">
                                <input
                                    type="radio"
                                    name="checkout_address"
                                    class="mt-1 text-primary focus:ring-primary"
                                    data-selector-input
                                    value="{{ $shippingAddress['id'] }}"
                                    data-label="{{ $shippingAddress['label'] }}"
                                    data-contact="{{ $shippingAddress['name'] }} · {{ $shippingAddress['phone'] }}"
                                    data-detail="{{ $shippingAddress['detail'] }}"
                                    data-note="{{ $shippingAddress['note'] }}"
                                    {{ ($shippingAddress['selected'] ?? false) ? 'checked' : '' }}
                                >
                                <div class="flex-1 font-body-md text-body-md leading-6 text-on-surface-variant">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold text-on-surface">{{ $shippingAddress['label'] }}</p>
                                        @if ($shippingAddress['is_primary'])
                                            <span class="rounded-full bg-secondary-container px-3 py-1 text-[11px] font-semibold text-on-secondary-container">Utama</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-outline">{{ $shippingAddress['name'] }} · {{ $shippingAddress['phone'] }}</p>
                                    <p class="mt-2">{{ $shippingAddress['detail'] }}</p>
                                    @if ($shippingAddress['note'] !== '')
                                        <p class="mt-2 text-xs text-outline">{{ $shippingAddress['note'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between gap-3">
                    <a href="{{ route('addresses') }}" class="font-body-md text-body-md font-bold text-primary hover:underline">Kelola alamat</a>
                    <button type="button" class="rounded-2xl bg-on-background px-5 py-3 font-body-md font-semibold text-on-primary transition hover:bg-primary" data-selector-apply>Gunakan alamat ini</button>
                </div>
            </div>
        </div>
    </div>
</article>
