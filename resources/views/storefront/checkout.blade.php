<x-layouts.customer title="Sobat Akar Tani Kimia | Checkout">
    <section data-checkout-page>
        <div class="mb-10">
            <x-customer-section-title
                eyebrow="Checkout"
                title="Selesaikan pesananmu"
                description="Periksa kembali alamat pengiriman dan pilihan ongkir sebelum membuat pesanan."
            />
        </div>

        @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-2xl border border-error-container bg-error-container/40 px-4 py-3 font-body-sm text-body-sm text-on-error-container">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <div class="space-y-1">
                    <p class="font-semibold">Pesanan belum bisa dibuat:</p>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mb-6 hidden items-center gap-3 rounded-2xl border border-secondary-container bg-secondary-container/30 px-4 py-3 font-body-sm text-body-sm font-medium text-on-secondary-container" data-checkout-loading>
            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            Sedang mencari layanan kurir...
        </div>

        <div class="grid grid-cols-1 items-start gap-gutter lg:grid-cols-[1fr_0.95fr]">
            <div class="space-y-6">
                <article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                    <h2 class="font-headline-md text-headline-md text-on-surface">Item pesanan</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($items as $item)
                            <div class="flex items-start justify-between gap-4 {{ !$loop->last ? 'border-b border-dashed border-surface-container-highest pb-3' : '' }}">
                                <div>
                                    <p class="font-body-md font-semibold text-on-surface">{{ $item['name'] }}</p>
                                    @if (!empty($item['variant_label']))
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $item['variant_label'] }}</p>
                                    @endif
                                    <p class="mt-0.5 font-body-sm text-body-sm text-on-surface-variant">{{ $item['quantity'] }} × {{ $item['price'] }}</p>
                                </div>
                                <p class="shrink-0 font-body-md font-semibold text-on-surface">{{ $item['subtotal'] }}</p>
                            </div>
                        @empty
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Belum ada item terpilih di keranjang. Pilih produk dulu di keranjang ya.</p>
                        @endforelse
                    </div>
                </article>

                <x-customer.checkout-address-selector :address="$address" :addresses="$addresses" />
                <x-customer.checkout-shipping-selector :shipping-options="$shippingOptions" />

                @if (!empty($shipmentOrigin))
                    <article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Dikirim dari</h2>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-surface-container-low px-4 py-4">
                                <p class="font-label-eyebrow text-label-eyebrow uppercase text-outline">Gudang aktif</p>
                                <p class="mt-2 font-body-md font-semibold text-on-surface">{{ $shipmentOrigin['label'] }}</p>
                                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">{{ $shipmentOrigin['contact_name'] }} • {{ $shipmentOrigin['contact_phone'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-surface-container-low px-4 py-4">
                                <p class="font-label-eyebrow text-label-eyebrow uppercase text-outline">Lokasi origin</p>
                                <p class="mt-2 font-body-md font-semibold text-on-surface">{{ $shipmentOrigin['city'] }}, {{ $shipmentOrigin['province'] }}</p>
                                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">{{ $shipmentOrigin['postal_code'] }}</p>
                            </div>
                        </div>
                    </article>
                @endif

                <article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                    <h2 class="font-headline-md text-headline-md text-on-surface">Informasi pembayaran</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-surface-container-low px-4 py-4">
                            <p class="font-label-eyebrow text-label-eyebrow uppercase text-outline">Metode</p>
                            <p class="mt-2 font-body-md font-semibold text-on-surface">Transfer manual</p>
                        </div>
                        <div class="rounded-2xl bg-surface-container-low px-4 py-4">
                            <p class="font-label-eyebrow text-label-eyebrow uppercase text-outline">Konfirmasi</p>
                            <p class="mt-2 font-body-md font-semibold text-on-surface">WhatsApp admin</p>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow lg:sticky lg:top-28 md:p-8">
                <h2 class="font-headline-md text-headline-md text-on-surface">Ringkasan akhir</h2>
                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Nominal transfer menyesuaikan ongkir dan kode unik pembayaran.</p>

                <div
                    class="mt-5 space-y-4 font-body-md text-body-md"
                    data-checkout-summary
                    data-items-total-value="{{ $paymentSummary['items_total_value'] }}"
                    data-unique-code-value="{{ $paymentSummary['unique_code_value'] }}"
                    data-used-unique-code-value="{{ $paymentSummary['used_unique_code_value'] ?? 0 }}"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-on-surface-variant">Total produk</span>
                        <span class="font-semibold text-on-surface">{{ $paymentSummary['items_total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-on-surface-variant">Ongkir</span>
                        <span class="font-semibold text-on-surface" data-summary-shipping>{{ $paymentSummary['shipping_total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between {{ ($paymentSummary['unique_code_value'] ?? 0) > 0 ? '' : 'hidden' }}" data-summary-unique-code-row>
                        <span class="text-on-surface-variant">Kode unik</span>
                        <span class="font-semibold text-on-surface" data-summary-unique-code>{{ $paymentSummary['unique_code'] }}</span>
                    </div>
                    @if (($paymentSummary['unique_code_enabled'] ?? false) && (($paymentSummary['available_unique_code_balance_value'] ?? 0) > 0))
                        <label class="flex items-start gap-3 rounded-2xl border border-secondary-container bg-secondary-container/20 px-4 py-4">
                            <input type="checkbox" class="mt-1 rounded border-surface-container-highest text-primary focus:ring-primary" data-use-unique-code-balance {{ !empty($paymentSummary['use_unique_code_balance']) ? 'checked' : '' }}>
                            <span class="flex-1">
                                <span class="block font-semibold text-on-secondary-container">Gunakan saldo kode unik</span>
                                <span class="mt-1 block font-body-sm text-body-sm leading-6 text-on-secondary-container">
                                    Saldo tersedia {{ $paymentSummary['available_unique_code_balance'] }}. Boleh pakai sekarang atau simpan untuk pembayaran berikutnya.
                                </span>
                            </span>
                        </label>
                    @endif
                    <div class="flex items-center justify-between {{ ($paymentSummary['used_unique_code_value'] ?? 0) > 0 ? '' : 'hidden' }}" data-summary-used-unique-code-row>
                        <span class="text-on-surface-variant">Potongan saldo kode unik</span>
                        <span class="font-semibold text-primary" data-summary-used-unique-code>-{{ $paymentSummary['used_unique_code'] ?? 'Rp0' }}</span>
                    </div>
                    <div class="border-t border-dashed border-surface-container-highest pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-on-surface">Total transfer</span>
                            <span class="font-headline-md text-xl font-bold text-primary" data-summary-grand-total>{{ $paymentSummary['grand_total'] }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('checkout.place-order') }}" class="mt-6" data-checkout-order-form>
                    @csrf
                    <input type="hidden" name="address_id" value="{{ $address['id'] ?? '' }}" data-checkout-address-id>
                    <input type="hidden" name="shipping_option_id" value="{{ collect($shippingOptions)->firstWhere('selected', true)['id'] ?? ($shippingOptions[0]['id'] ?? '') }}" data-checkout-shipping-option-id>
                    <input type="hidden" name="use_unique_code_balance" value="{{ !empty($paymentSummary['use_unique_code_balance']) ? '1' : '0' }}" data-checkout-use-unique-code-balance>
                    <button class="inline-flex w-full items-center justify-center rounded-2xl bg-primary px-5 py-4 font-body-md font-bold text-on-primary shadow-lg shadow-primary/20 transition hover:bg-secondary active:scale-95">
                        Buat Pesanan
                    </button>
                </form>

                <p class="mt-4 font-body-sm text-xs leading-5 text-on-surface-variant">
                    Setelah order dibuat, customer dapat melanjutkan transfer dan mengirim bukti pembayaran ke WhatsApp admin.
                </p>
            </aside>
        </div>
    </section>
</x-layouts.customer>
