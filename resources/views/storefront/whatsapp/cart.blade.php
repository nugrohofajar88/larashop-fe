<x-layouts.customer title="Sobat Akar Tani Kimia | Keranjang">
    <section
        data-cart-v2-page
        data-cart-v2-update-template="{{ route('cart.items.update', ['id' => '__ID__']) }}"
        data-cart-v2-delete-template="{{ route('cart.items.destroy', ['id' => '__ID__']) }}"
        data-cart-v2-csrf="{{ csrf_token() }}"
    >
        {{-- Header --}}
        <div class="mb-10">
            <x-customer-section-title
                eyebrow="Keranjang"
                title="Cek pesanan sebelum lanjut ke WhatsApp"
            />
        </div>

        <div class="grid grid-cols-1 items-start gap-gutter lg:grid-cols-[1.5fr_1fr]">
            {{-- Items --}}
            <div class="space-y-6">
                @forelse ($items as $item)
                    <div
                        class="flex items-start gap-5 rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-5 soft-warm-shadow transition-all duration-300 sm:items-center sm:gap-6 sm:p-6"
                        data-cart-v2-item
                        data-item-id="{{ $item['id'] }}"
                        data-item-price-value="{{ $item['price_value'] }}"
                        data-item-stock="{{ $item['stock'] }}"
                    >
                        <div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl bg-surface-container">
                            <img src="{{ ($item['image'] ?? '') ?: asset('images/placeholder-product.png') }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover">
                        </div>

                        <div class="min-w-0 flex-grow">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="mb-1 truncate font-headline-md text-[20px] leading-tight text-on-surface">{{ $item['name'] }}</h3>
                                    <p class="font-body-sm text-on-surface-variant">{{ $item['variant'] }}</p>
                                    @if ($item['available'])
                                        <p class="mt-1 font-body-sm text-xs text-on-surface-variant">Stok tersedia: {{ $item['stock'] }}</p>
                                    @else
                                        <p class="mt-1 font-body-sm text-xs text-error">Produk ini sudah tidak tersedia</p>
                                    @endif
                                </div>
                                <button type="button" class="p-1 text-stone-400 transition-colors hover:text-error" data-cart-v2-remove aria-label="Hapus item">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>

                            @if ($item['available'])
                                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                                    <div class="flex flex-col">
                                        <span class="font-body-sm text-stone-400">{{ $item['price'] }} / unit</span>
                                        <span class="font-headline-md font-bold text-larashop-rose" data-cart-v2-subtotal>{{ $item['subtotal'] }}</span>
                                    </div>

                                    <div class="flex items-center rounded-full bg-surface-container-low px-2 py-1">
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface transition-colors hover:bg-surface-container-lowest active:scale-90 disabled:cursor-not-allowed disabled:opacity-40" data-cart-v2-decrease>−</button>
                                        <span class="w-10 text-center font-bold text-on-surface" data-cart-v2-qty>{{ $item['qty'] }}</span>
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full text-on-surface transition-colors hover:bg-surface-container-lowest active:scale-90 disabled:cursor-not-allowed disabled:opacity-40" data-cart-v2-increase>+</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-surface-container-highest bg-surface-container-lowest px-6 py-16 text-center soft-warm-shadow">
                        <span class="material-symbols-outlined mb-3 text-5xl text-outline-variant">shopping_cart</span>
                        <p class="font-body-md text-on-surface-variant">Keranjang masih kosong. Tambahkan produk dulu sebelum lanjut checkout.</p>
                        <a href="{{ route('home') }}" class="mt-6 inline-flex rounded-full bg-primary px-6 py-3 font-body-md font-bold text-on-primary transition hover:bg-secondary">Mulai belanja</a>
                    </div>
                @endforelse
            </div>

            {{-- Summary --}}
            <aside class="lg:sticky lg:top-28">
                <div class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow md:p-8">
                    <h2 class="mb-2 font-headline-md text-headline-md text-on-surface">Ringkasan</h2>
                    <p class="mb-6 font-body-sm text-body-sm text-on-surface-variant">Ongkos kirim & pembayaran diatur lewat WhatsApp.</p>

                    <div class="space-y-4" data-cart-v2-summary>
                        <div class="flex items-center justify-between font-body-md text-on-surface-variant">
                            <span>Jumlah produk</span>
                            <span class="font-semibold text-on-surface" data-cart-v2-summary-count>{{ $summary['selected_product_count'] }}</span>
                        </div>
                        <div class="flex items-center justify-between border-t border-dashed border-surface-container-highest pt-4">
                            <span class="font-body-md text-on-surface">Total harga</span>
                            <span class="font-headline-md text-xl font-bold text-primary" data-cart-v2-summary-total>{{ $summary['selected_total'] }}</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout') }}"
                        class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-primary px-5 py-4 font-body-md font-bold text-on-primary shadow-lg shadow-primary/20 transition hover:bg-secondary active:scale-95 {{ $summary['selected_product_count'] === 0 ? 'pointer-events-none opacity-40' : '' }}"
                        aria-disabled="{{ $summary['selected_product_count'] === 0 ? 'true' : 'false' }}"
                        data-cart-v2-checkout>
                        Lanjut Checkout
                    </a>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.customer>
