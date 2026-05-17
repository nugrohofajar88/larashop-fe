@props([
    'address',
    'addresses' => [],
])

<article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm" data-checkout-selector="address" data-checkout-refresh-url="{{ route('checkout.data') }}">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-stone-950">Alamat pengiriman</h2>
        <button type="button" class="text-sm font-semibold text-emerald-700" data-selector-open>Ubah</button>
    </div>
    <div class="mt-4 rounded-2xl bg-stone-50 px-4 py-4 text-sm leading-6 text-stone-700">
        <div class="flex flex-wrap items-center gap-2">
            <p class="font-semibold text-stone-950" data-address-label>{{ $address['label'] }}</p>
            @if ($address['is_primary'])
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700" data-address-primary-badge>Utama</span>
            @else
                <span class="hidden rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700" data-address-primary-badge>Utama</span>
            @endif
        </div>
        <p class="mt-1 text-stone-500" data-address-contact>{{ $address['name'] }} · {{ $address['phone'] }}</p>
        <p class="mt-2" data-address-detail>{{ $address['detail'] }}</p>
        @if ($address['note'] !== '')
            <p class="mt-2 text-xs text-stone-500" data-address-note>{{ $address['note'] }}</p>
        @else
            <p class="mt-2 hidden text-xs text-stone-500" data-address-note></p>
        @endif
    </div>

    <div class="fixed inset-0 z-50 hidden bg-stone-950/45 p-4 backdrop-blur-sm" data-selector-modal aria-hidden="true">
        <div class="mx-auto flex min-h-full max-w-3xl items-center justify-center">
            <div class="w-full rounded-[2rem] bg-white p-5 shadow-2xl sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Checkout</p>
                        <h3 class="mt-2 text-2xl font-semibold text-stone-950">Pilih alamat pengiriman</h3>
                    </div>
                    <button type="button" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-semibold text-stone-700" data-selector-close>
                        Tutup
                    </button>
                </div>

                <div class="mt-5 max-h-[60vh] space-y-3 overflow-y-auto pr-1" data-selector-options>
                    @foreach ($addresses as $shippingAddress)
                        <label class="block cursor-pointer rounded-2xl border {{ ($shippingAddress['selected'] ?? false) ? 'border-emerald-500 bg-emerald-50/50' : 'border-stone-200 bg-white' }} px-4 py-4 transition" data-selector-option>
                            <div class="flex items-start gap-3">
                                <input
                                    type="radio"
                                    name="checkout_address"
                                    class="mt-1"
                                    data-selector-input
                                    value="{{ $shippingAddress['id'] }}"
                                    data-label="{{ $shippingAddress['label'] }}"
                                    data-contact="{{ $shippingAddress['name'] }} · {{ $shippingAddress['phone'] }}"
                                    data-detail="{{ $shippingAddress['detail'] }}"
                                    data-note="{{ $shippingAddress['note'] }}"
                                    {{ ($shippingAddress['selected'] ?? false) ? 'checked' : '' }}
                                >
                                <div class="flex-1 text-sm leading-6 text-stone-700">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold text-stone-950">{{ $shippingAddress['label'] }}</p>
                                        @if ($shippingAddress['is_primary'])
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">Utama</span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-stone-500">{{ $shippingAddress['name'] }} · {{ $shippingAddress['phone'] }}</p>
                                    <p class="mt-2">{{ $shippingAddress['detail'] }}</p>
                                    @if ($shippingAddress['note'] !== '')
                                        <p class="mt-2 text-xs text-stone-500">{{ $shippingAddress['note'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center justify-between gap-3">
                    <a href="{{ route('addresses') }}" class="text-sm font-semibold text-emerald-700">Kelola alamat</a>
                    <button type="button" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white" data-selector-apply>
                        Gunakan alamat ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</article>
