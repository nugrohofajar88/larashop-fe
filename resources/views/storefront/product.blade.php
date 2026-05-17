<x-layouts.customer :title="'Larashop | ' . $product['name']">
    <section class="space-y-6">
        <div class="space-y-3">
            <a href="{{ route('catalog', $catalogQuery) }}" class="inline-flex items-center text-sm font-medium text-emerald-700">
                Kembali ke katalog
            </a>
            <div class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-5 lg:grid lg:grid-cols-[0.95fr_1.05fr] lg:items-start">
                    <div class="space-y-3" data-storefront-gallery>
                        <div class="rounded-[1.75rem] border border-stone-200 bg-stone-50 p-4">
                            <button type="button" class="block w-full cursor-zoom-in" data-gallery-main-trigger aria-label="Buka foto produk">
                                <img
                                    src="{{ $gallery[0]['path'] }}"
                                    alt="{{ $product['name'] }}"
                                    class="aspect-square w-full rounded-[1.5rem] object-cover"
                                    data-gallery-main-image
                                >
                            </button>
                        </div>

                        <div class="-mx-1 overflow-x-auto pb-1">
                            <div class="flex min-w-full gap-3 px-1">
                                @foreach ($gallery as $image)
                                    <button
                                        type="button"
                                        class="w-28 shrink-0 rounded-[1.1rem] border {{ $loop->first ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-stone-200' }} bg-white p-2 text-left shadow-sm transition hover:border-emerald-400 sm:w-32"
                                        data-gallery-thumb
                                        data-gallery-image="{{ $image['path'] }}"
                                        data-gallery-alt="{{ $image['label'] }} {{ $product['name'] }}"
                                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                    >
                                        <img
                                            src="{{ $image['path'] }}"
                                            alt="{{ $image['label'] }} {{ $product['name'] }}"
                                            class="aspect-square w-full rounded-[0.9rem] object-cover"
                                        >
                                        <p class="mt-2 line-clamp-2 text-[11px] font-medium leading-4 text-stone-600">{{ $image['label'] }}</p>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="fixed inset-0 z-50 hidden bg-stone-950/88 p-4 backdrop-blur-sm" data-gallery-lightbox aria-hidden="true">
                            <div class="flex h-full flex-col justify-center gap-4">
                                <div class="flex items-center justify-between text-white">
                                    <p class="text-sm font-medium" data-gallery-lightbox-label>{{ $gallery[0]['label'] }}</p>
                                    <button
                                        type="button"
                                        class="rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white"
                                        data-gallery-close
                                    >
                                        Tutup
                                    </button>
                                </div>

                                <div class="relative">
                                    <button
                                        type="button"
                                        class="absolute left-2 top-1/2 z-10 -translate-y-1/2 rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white"
                                        data-gallery-prev
                                    >
                                        Prev
                                    </button>
                                    <img
                                        src="{{ $gallery[0]['path'] }}"
                                        alt="{{ $product['name'] }}"
                                        class="mx-auto max-h-[75vh] w-full rounded-[1.5rem] object-contain"
                                        data-gallery-lightbox-image
                                    >
                                    <button
                                        type="button"
                                        class="absolute right-2 top-1/2 z-10 -translate-y-1/2 rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white"
                                        data-gallery-next
                                    >
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">{{ $product['category'] }}</p>
                                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">{{ $product['name'] }}</h1>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $product['badge'] }}</span>
                        </div>

                        <p class="text-sm leading-7 text-stone-600">{{ $product['description'] }}</p>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-stone-50 px-4 py-3">
                                <p class="text-xs text-stone-500">Harga</p>
                                <p class="mt-1 text-lg font-semibold text-emerald-700">{{ $product['default_variant']['price'] ?? $product['price'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-stone-50 px-4 py-3">
                                <p class="text-xs text-stone-500">Varian default</p>
                                <p class="mt-1 text-sm font-semibold text-stone-900">{{ $product['default_variant']['label'] ?? $product['weight'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-stone-50 px-4 py-3">
                                <p class="text-xs text-stone-500">Total stok</p>
                                <p class="mt-1 text-sm font-semibold text-stone-900">{{ $product['stock'] }}</p>
                            </div>
                        </div>

                        @if (! empty($product['variants']))
                            <form method="POST" action="{{ route('cart.items.store') }}" class="space-y-4">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-semibold text-stone-900">Pilih varian</p>
                                        <p class="text-xs text-stone-500">Harga, stok, dan berat checkout akan mengikuti varian yang dipilih.</p>
                                    </div>
                                    <div class="grid gap-3">
                                        @foreach ($product['variants'] as $variant)
                                            <label class="block cursor-pointer rounded-2xl border {{ $variant['is_default'] ? 'border-emerald-300 bg-emerald-50/60' : 'border-stone-200 bg-white' }} px-4 py-4">
                                                <div class="flex items-start gap-3">
                                                    <input
                                                        type="radio"
                                                        name="product_variant_id"
                                                        value="{{ $variant['id'] }}"
                                                        class="mt-1 h-4 w-4 border-stone-300 text-emerald-600 focus:ring-emerald-500"
                                                        {{ old('product_variant_id', $product['default_variant']['id'] ?? null) == $variant['id'] ? 'checked' : '' }}
                                                    >
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                                            <div>
                                                                <div class="flex flex-wrap items-center gap-2">
                                                                    <p class="font-semibold text-stone-900">{{ $variant['label'] }}</p>
                                                                    @if ($variant['is_default'])
                                                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">Default</span>
                                                                    @endif
                                                                </div>
                                                                <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $variant['sku'] }}</p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-sm font-semibold text-emerald-700">{{ $variant['price'] }}</p>
                                                                <p class="mt-1 text-xs text-stone-500">Stok {{ $variant['stock'] }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                                                            <div class="rounded-2xl bg-stone-50 px-3 py-3">
                                                                <p class="text-[11px] uppercase tracking-[0.18em] text-stone-500">Berat</p>
                                                                <p class="mt-2 text-sm font-semibold text-stone-900">{{ $variant['weight_grams'] ? number_format($variant['weight_grams'], 0, ',', '.') . ' gram' : '-' }}</p>
                                                            </div>
                                                            <div class="rounded-2xl bg-stone-50 px-3 py-3">
                                                                <p class="text-[11px] uppercase tracking-[0.18em] text-stone-500">Dimensi</p>
                                                                <p class="mt-2 text-sm font-semibold text-stone-900">{{ $variant['dimension'] }}</p>
                                                            </div>
                                                            <div class="rounded-2xl bg-stone-50 px-3 py-3">
                                                                <p class="text-[11px] uppercase tracking-[0.18em] text-stone-500">Harga coret</p>
                                                                <p class="mt-2 text-sm font-semibold text-stone-900">{{ $variant['original_price'] ?? '-' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('product_variant_id')
                                        <p class="text-sm text-rose-700">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex flex-wrap items-end gap-3">
                                    <div class="w-28">
                                        <label class="mb-2 block text-sm font-medium text-stone-700">Qty</label>
                                        <input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                                    </div>

                                    <div class="flex flex-1 gap-3">
                                        <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3.5 text-sm font-semibold text-white">
                                            Tambah ke keranjang
                                        </button>
                                        <button type="submit" name="redirect_to" value="checkout" class="inline-flex items-center justify-center rounded-2xl border border-stone-300 px-5 py-3.5 text-sm font-semibold text-stone-800">
                                            Checkout
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        <div class="space-y-3">
                            <p class="text-sm font-semibold text-stone-900">Keunggulan produk</p>
                            <div class="grid gap-3">
                                @foreach ($product['highlights'] as $highlight)
                                    <div class="rounded-2xl border border-stone-200 px-4 py-3 text-sm text-stone-700">
                                        {{ $highlight }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <section class="space-y-4">
            <x-customer-section-title
                eyebrow="Produk Lainnya"
                title="Pilihan lain yang relevan untuk customer"
                description="Bagian ini nantinya bisa diisi rekomendasi produk berdasarkan kategori atau perilaku belanja."
            />

            <div class="grid gap-4 lg:grid-cols-3">
                @foreach ($relatedProducts as $related)
                    <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                        <img
                            src="{{ $related['image'] }}"
                            alt="{{ $related['name'] }}"
                            class="aspect-[1.1] w-full rounded-[1.25rem] object-cover"
                        >
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-stone-500">{{ $related['category'] }}</p>
                        <h3 class="mt-3 text-lg font-semibold text-stone-950">{{ $related['name'] }}</h3>
                        <p class="mt-2 text-sm text-stone-600">{{ $related['price'] }} • {{ $related['weight'] }}</p>
                        <a href="{{ route('products.show', array_merge(['slug' => $related['slug']], $catalogQuery)) }}" class="mt-5 inline-flex rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white">
                            Lihat detail
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    </section>
</x-layouts.customer>
