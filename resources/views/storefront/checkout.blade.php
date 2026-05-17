<x-layouts.customer title="Larashop | Checkout">
    <section class="space-y-6" data-checkout-page>
        <x-customer-section-title
            eyebrow="Checkout"
            title=""
            description="Periksalah kembali alamat pengiriman dan pilihan ongkir sebelum membuat pesanan. "
        />

        <div class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700" data-checkout-loading>
            Sedang mencari layanan kurir...
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_0.95fr]">
            <div class="space-y-4">
                <x-customer.checkout-address-selector :address="$address" :addresses="$addresses" />
                <x-customer.checkout-shipping-selector :shipping-options="$shippingOptions" />

                @if (!empty($shipmentOrigin))
                    <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-stone-950">Dikirim dari</h2>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-stone-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Gudang aktif</p>
                                <p class="mt-2 text-sm font-semibold text-stone-900">{{ $shipmentOrigin['label'] }}</p>
                                <p class="mt-1 text-sm text-stone-600">{{ $shipmentOrigin['contact_name'] }} • {{ $shipmentOrigin['contact_phone'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-stone-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Lokasi origin</p>
                                <p class="mt-2 text-sm font-semibold text-stone-900">{{ $shipmentOrigin['city'] }}, {{ $shipmentOrigin['province'] }}</p>
                                <p class="mt-1 text-sm text-stone-600">{{ $shipmentOrigin['postal_code'] }}</p>
                            </div>
                        </div>
                    </article>
                @endif

                <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-stone-950">Informasi pembayaran</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Metode</p>
                            <p class="mt-2 text-sm font-semibold text-stone-900">Transfer manual</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.22em] text-stone-500">Konfirmasi</p>
                            <p class="mt-2 text-sm font-semibold text-stone-900">WhatsApp admin</p>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-stone-950">Ringkasan akhir</h2>
                <p class="mt-1 text-sm text-stone-500">Nominal transfer akan menyesuaikan ongkir dan kode unik pembayaran.</p>

                <div
                    class="mt-5 space-y-4 text-sm"
                    data-checkout-summary
                    data-items-total-value="{{ $paymentSummary['items_total_value'] }}"
                    data-unique-code-value="{{ $paymentSummary['unique_code_value'] }}"
                    data-used-unique-code-value="{{ $paymentSummary['used_unique_code_value'] ?? 0 }}"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Total produk</span>
                        <span class="font-semibold text-stone-900">{{ $paymentSummary['items_total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Ongkir</span>
                        <span class="font-semibold text-stone-900" data-summary-shipping>{{ $paymentSummary['shipping_total'] }}</span>
                    </div>
                    <div
                        class="flex items-center justify-between {{ ($paymentSummary['unique_code_value'] ?? 0) > 0 ? '' : 'hidden' }}"
                        data-summary-unique-code-row
                    >
                        <span class="text-stone-500">Kode unik</span>
                        <span class="font-semibold text-stone-900" data-summary-unique-code>{{ $paymentSummary['unique_code'] }}</span>
                    </div>
                    @if (($paymentSummary['unique_code_enabled'] ?? false) && (($paymentSummary['available_unique_code_balance_value'] ?? 0) > 0))
                        <label class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/70 px-4 py-4">
                            <input
                                type="checkbox"
                                class="mt-1 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500"
                                data-use-unique-code-balance
                                {{ !empty($paymentSummary['use_unique_code_balance']) ? 'checked' : '' }}
                            >
                            <span class="flex-1">
                                <span class="block font-semibold text-emerald-900">Gunakan saldo kode unik</span>
                                <span class="mt-1 block text-sm leading-6 text-emerald-800">
                                    Saldo tersedia {{ $paymentSummary['available_unique_code_balance'] }}.
                                    Kamu boleh pakai sekarang atau simpan untuk pembayaran berikutnya.
                                </span>
                            </span>
                        </label>
                    @endif
                    <div
                        class="flex items-center justify-between {{ ($paymentSummary['used_unique_code_value'] ?? 0) > 0 ? '' : 'hidden' }}"
                        data-summary-used-unique-code-row
                    >
                        <span class="text-stone-500">Potongan saldo kode unik</span>
                        <span class="font-semibold text-emerald-700" data-summary-used-unique-code>
                            -{{ $paymentSummary['used_unique_code'] ?? 'Rp0' }}
                        </span>
                    </div>
                    <div class="border-t border-dashed border-stone-200 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-stone-600">Total transfer</span>
                            <span class="text-lg font-semibold text-emerald-700" data-summary-grand-total>{{ $paymentSummary['grand_total'] }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('checkout.place-order') }}" class="mt-6" data-checkout-order-form>
                    @csrf
                    <input type="hidden" name="address_id" value="{{ $address['id'] ?? '' }}" data-checkout-address-id>
                    <input type="hidden" name="shipping_option_id" value="{{ collect($shippingOptions)->firstWhere('selected', true)['id'] ?? ($shippingOptions[0]['id'] ?? '') }}" data-checkout-shipping-option-id>
                    <input type="hidden" name="use_unique_code_balance" value="{{ !empty($paymentSummary['use_unique_code_balance']) ? '1' : '0' }}" data-checkout-use-unique-code-balance>
                    <button class="inline-flex w-full items-center justify-center rounded-2xl bg-stone-900 px-5 py-3.5 text-sm font-semibold text-white">
                        Buat pesanan
                    </button>
                </form>

                <p class="mt-4 text-xs leading-5 text-stone-500">
                    Setelah order dibuat, customer dapat melanjutkan transfer dan mengirim bukti pembayaran ke WhatsApp admin.
                </p>
            </aside>
        </div>
    </section>
</x-layouts.customer>
