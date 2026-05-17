<x-layouts.customer title="Larashop | Keranjang">
    <section
        class="space-y-6"
        data-cart-page
        data-cart-update-template="{{ route('cart.items.update', ['id' => '__ID__']) }}"
        data-cart-delete-template="{{ route('cart.items.destroy', ['id' => '__ID__']) }}"
        data-cart-csrf="{{ csrf_token() }}"
    >
        <x-customer-section-title
            eyebrow="Keranjang"
            title="Pilih item yang ingin dibawa ke checkout"
            description="Centang item yang ingin diproses, lalu atur jumlahnya langsung dari keranjang sebelum lanjut checkout."
        />

        <div class="flex justify-end">
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-2xl border border-stone-300 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-50"
                data-cart-toggle-all
            >
                Pilih semua
            </button>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
            <div class="space-y-4">
                @forelse ($items as $item)
                    <article
                        class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm"
                        data-cart-item
                        data-item-id="{{ $item['id'] }}"
                        data-item-price-value="{{ $item['price_value'] }}"
                        data-item-stock="{{ $item['stock'] }}"
                    >
                        <div class="flex items-start gap-4">
                            <label class="pt-1">
                                <input
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500"
                                    data-cart-select
                                    {{ $item['selected'] ? 'checked' : '' }}
                                >
                            </label>

                            <div class="h-20 w-20 overflow-hidden rounded-2xl border border-stone-200 bg-stone-50 p-2">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-contain">
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-stone-950">{{ $item['name'] }}</h3>
                                        <p class="mt-1 text-sm text-stone-500">{{ $item['variant'] }}</p>
                                        <p class="mt-2 text-xs font-medium text-stone-500">Stok tersedia: {{ $item['stock'] }}</p>
                                    </div>

                                    <div class="rounded-2xl bg-stone-50 px-3 py-2.5 text-right">
                                        <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Harga</p>
                                        <p class="mt-1 text-sm font-semibold text-stone-900">{{ $item['price'] }}</p>
                                    </div>
                                </div>

                                <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="inline-flex items-center rounded-2xl border border-stone-200 bg-white p-1">
                                            <button
                                                type="button"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-lg font-semibold text-stone-700 transition hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-40"
                                                data-cart-decrease
                                            >
                                                -
                                            </button>
                                            <span class="inline-flex min-w-12 items-center justify-center px-3 text-sm font-semibold text-stone-900" data-cart-qty>{{ $item['qty'] }}</span>
                                            <button
                                                type="button"
                                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-lg font-semibold text-stone-700 transition hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-40"
                                                data-cart-increase
                                            >
                                                +
                                            </button>
                                        </div>

                                        <button
                                            type="button"
                                            class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                            data-cart-remove
                                            aria-label="Hapus item"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18" />
                                                <path d="M8 6V4h8v2" />
                                                <path d="M19 6l-1 14H6L5 6" />
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="rounded-2xl bg-emerald-50 px-3 py-2.5 text-right">
                                            <p class="text-xs uppercase tracking-[0.18em] text-emerald-700">Subtotal</p>
                                            <p class="mt-1 text-sm font-semibold text-emerald-700" data-cart-subtotal>{{ $item['subtotal'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="rounded-[1.75rem] border border-dashed border-stone-200 bg-white px-6 py-10 text-center text-sm text-stone-500 shadow-sm">
                        Keranjang masih kosong. Tambahkan produk dulu sebelum lanjut checkout.
                    </article>
                @endforelse
            </div>

            <aside class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-stone-950">Ringkasan pembayaran</h2>
                <p class="mt-1 text-sm text-stone-500">Ringkasan ini hanya menghitung item yang sedang dicentang di keranjang.</p>

                <div
                    class="mt-5 space-y-4 text-sm"
                    data-cart-summary
                    data-selected-product-count="{{ $summary['selected_product_count'] }}"
                    data-selected-total-value="{{ $summary['selected_total_value'] }}"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-stone-500">Jumlah produk dipilih</span>
                        <span class="font-semibold text-stone-900" data-cart-summary-count>{{ $summary['selected_product_count'] }}</span>
                    </div>
                    <div class="border-t border-dashed border-stone-200 pt-4">
                        <div class="flex items-center justify-between">
                            <span class="text-stone-600">Total harga</span>
                            <span class="text-lg font-semibold text-emerald-700" data-cart-summary-total>{{ $summary['selected_total'] }}</span>
                        </div>
                    </div>
                </div>

                <a
                    href="{{ route('checkout') }}"
                    class="mt-6 inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3.5 text-sm font-semibold text-white transition disabled:pointer-events-none disabled:opacity-40"
                    data-cart-checkout
                >
                    Lanjut checkout
                </a>
            </aside>
        </div>
    </section>
</x-layouts.customer>
