<x-layouts.customer :title="'Sobat Akar Tani Kimia | ' . $product['name']">
    @php
        // Fallback kalau produk belum punya foto -> pakai placeholder (cegah error $gallery[0]).
        $gallery = ! empty($gallery) ? $gallery : [[
            'path' => asset('images/placeholder-product.png'),
            'label' => 'Foto belum tersedia',
        ]];
    @endphp

    {{-- Breadcrumb --}}
    <nav class="mb-8 flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant">
        <a href="{{ route('home') }}" class="transition-colors hover:text-primary">Home</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <a href="{{ route('catalog', $catalogQuery) }}" class="transition-colors hover:text-primary">Katalog</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="font-medium text-on-surface">{{ $product['name'] }}</span>
    </nav>

    <div class="mb-20 grid grid-cols-1 gap-12 lg:grid-cols-2">
        {{-- Gallery --}}
        <div class="space-y-6" data-storefront-gallery>
            <div class="group aspect-square overflow-hidden rounded-3xl bg-surface-container-lowest soft-warm-shadow">
                <button type="button" class="block h-full w-full cursor-zoom-in" data-gallery-main-trigger aria-label="Buka foto produk">
                    <img src="{{ $gallery[0]['path'] }}" alt="{{ $product['name'] }}"
                        class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110" data-gallery-main-image>
                </button>
            </div>

            <div class="grid grid-cols-4 gap-4">
                @foreach ($gallery as $image)
                    <button type="button"
                        class="aspect-square overflow-hidden rounded-2xl border-2 bg-surface-container-lowest p-1 soft-warm-shadow transition-all hover:border-primary {{ $loop->first ? 'border-primary' : 'border-surface-container-highest' }}"
                        data-gallery-thumb
                        data-gallery-image="{{ $image['path'] }}"
                        data-gallery-alt="{{ $image['label'] }} {{ $product['name'] }}"
                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                        <img src="{{ $image['path'] }}" alt="{{ $image['label'] }} {{ $product['name'] }}" class="h-full w-full rounded-xl object-cover">
                    </button>
                @endforeach
            </div>

            {{-- Lightbox --}}
            <div class="fixed inset-0 z-[60] hidden bg-larashop-stone/90 p-4 backdrop-blur-sm" data-gallery-lightbox aria-hidden="true" style="--tw-bg-opacity:1; background-color: rgba(12,10,9,0.9);">
                <div class="flex h-full flex-col justify-center gap-4">
                    <div class="flex items-center justify-between text-white">
                        <p class="font-body-sm text-body-sm font-medium" data-gallery-lightbox-label>{{ $gallery[0]['label'] }}</p>
                        <button type="button" class="rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white" data-gallery-close>Tutup</button>
                    </div>
                    <div class="relative">
                        <button type="button" class="absolute left-2 top-1/2 z-10 -translate-y-1/2 rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white" data-gallery-prev>Prev</button>
                        <img src="{{ $gallery[0]['path'] }}" alt="{{ $product['name'] }}" class="mx-auto max-h-[75vh] w-full rounded-3xl object-contain" data-gallery-lightbox-image>
                        <button type="button" class="absolute right-2 top-1/2 z-10 -translate-y-1/2 rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white" data-gallery-next>Next</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="flex flex-col">
            <div class="mb-4">
                <span class="inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700">{{ $product['category'] }}</span>
            </div>
            <h1 class="mb-4 font-headline-lg text-headline-lg leading-tight text-on-surface">{{ $product['name'] }}</h1>

            @php
                $defaultVariantId = $product['default_variant']['id'] ?? ($product['variants'][0]['id'] ?? null);
                $selectedId = old('product_variant_id', $defaultVariantId);
                $activeVariant = collect($product['variants'] ?? [])->firstWhere('id', $selectedId) ?? ($product['variants'][0] ?? []);
            @endphp

            <div class="mb-6 flex items-center gap-4">
                <div class="flex flex-col">
                    <span class="font-headline-md text-4xl font-bold text-larashop-rose" data-variant-main-price>{{ $activeVariant['price'] ?? $product['price'] }}</span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant line-through {{ empty($activeVariant['original_price']) ? 'hidden' : '' }}" data-variant-main-original>{{ $activeVariant['original_price'] ?? '' }}</span>
                </div>
                <div class="mx-2 h-10 w-px bg-surface-container-highest"></div>
                <div class="flex flex-col">
                    <span class="flex items-center rounded-full bg-primary/5 px-3 py-1 text-xs font-bold text-primary">
                        <span class="material-symbols-outlined mr-1 text-[14px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        {{ $product['badge'] ?? 'Tersedia' }}
                    </span>
                    <span class="ml-1 mt-1 font-body-sm text-xs text-on-surface-variant">Stok: {{ $product['stock'] }}</span>
                </div>
            </div>

            <p class="mb-6 font-body-md text-body-md leading-relaxed text-on-surface-variant">{{ $product['description'] }}</p>

            @if (! empty($product['variants']))
                <form method="POST" action="{{ route('cart.items.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">

                    <div>
                        <label class="mb-3 block font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Pilih varian</label>

                        {{-- Chip varian (bisa dipilih) --}}
                        <div class="flex flex-wrap gap-2.5">
                            @foreach ($product['variants'] as $variant)
                                <label class="cursor-pointer">
                                    <input type="radio" name="product_variant_id" value="{{ $variant['id'] }}" class="peer sr-only"
                                        data-variant
                                        data-label="{{ $variant['label'] }}"
                                        data-sku="{{ $variant['sku'] }}"
                                        data-price="{{ $variant['price'] }}"
                                        data-original="{{ $variant['original_price'] ?? '' }}"
                                        data-stock="{{ $variant['stock'] }}"
                                        data-weight="{{ $variant['weight_grams'] ? number_format($variant['weight_grams'], 0, ',', '.') . ' gram' : '-' }}"
                                        data-dimension="{{ $variant['dimension'] }}"
                                        {{ $selectedId == $variant['id'] ? 'checked' : '' }}>
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-surface-container-highest bg-surface-container-lowest px-5 py-2.5 font-body-md text-sm font-semibold text-on-surface transition-all hover:border-primary peer-checked:border-primary peer-checked:bg-primary peer-checked:text-on-primary peer-checked:shadow-md">
                                        {{ $variant['label'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        {{-- Card keterangan varian terpilih (berubah sesuai pilihan) --}}
                        <div class="mt-4 rounded-2xl border border-primary/30 bg-secondary-container/15 p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="font-body-md font-semibold text-on-surface" data-variant-label>{{ $activeVariant['label'] ?? '-' }}</p>
                                    <p class="mt-0.5 font-label-eyebrow text-[11px] uppercase tracking-[0.18em] text-outline" data-variant-sku>{{ $activeVariant['sku'] ?? '' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-headline-md text-xl font-bold text-larashop-rose" data-variant-price>{{ $activeVariant['price'] ?? $product['price'] }}</p>
                                    <p class="mt-0.5 font-body-sm text-xs text-on-surface-variant">Stok <span data-variant-stock>{{ $activeVariant['stock'] ?? $product['stock'] }}</span></p>
                                </div>
                            </div>
                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                <div class="rounded-xl bg-surface-container-low px-3 py-2">
                                    <p class="text-[10px] uppercase tracking-[0.18em] text-outline">Berat</p>
                                    <p class="mt-1 font-body-sm text-sm font-semibold text-on-surface" data-variant-weight>{{ ($activeVariant['weight_grams'] ?? 0) ? number_format($activeVariant['weight_grams'], 0, ',', '.') . ' gram' : '-' }}</p>
                                </div>
                                <div class="rounded-xl bg-surface-container-low px-3 py-2">
                                    <p class="text-[10px] uppercase tracking-[0.18em] text-outline">Dimensi</p>
                                    <p class="mt-1 font-body-sm text-sm font-semibold text-on-surface" data-variant-dimension>{{ $activeVariant['dimension'] ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        @error('product_variant_id')
                            <p class="mt-2 font-body-sm text-body-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-end gap-4">
                        <div class="w-28">
                            <label class="mb-2 block font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Qty</label>
                            <input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}"
                                class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none focus:border-primary focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="flex flex-1 gap-3">
                            <button type="submit" name="redirect_to" value="checkout"
                                class="flex h-14 flex-1 items-center justify-center rounded-2xl bg-primary font-body-md text-lg font-bold text-on-primary shadow-lg shadow-primary/20 transition-all hover:bg-secondary active:scale-95">
                                Beli Sekarang
                            </button>
                            <button type="submit"
                                class="flex h-14 items-center justify-center gap-2 rounded-2xl border border-surface-container-highest bg-surface-container-lowest px-6 font-body-md text-lg font-bold text-on-surface soft-warm-shadow transition-all hover:bg-surface-container-low active:scale-95">
                                <span class="material-symbols-outlined">shopping_cart</span>
                                Keranjang
                            </button>
                        </div>
                    </div>
                </form>
            @endif

            {{-- Highlights --}}
            @if (!empty($product['highlights']))
                <div class="mt-8 grid grid-cols-1 gap-3 rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow md:grid-cols-2">
                    @foreach ($product['highlights'] as $highlight)
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">check</span>
                            </div>
                            <span class="font-body-sm text-sm font-semibold text-on-surface">{{ $highlight }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Related products --}}
    @if (!empty($relatedProducts))
        <section class="mt-4">
            <div class="mb-8">
                <h2 class="font-headline-lg text-headline-lg text-on-surface">Produk Lainnya</h2>
                <p class="mt-1 font-body-md text-on-surface-variant">Lengkapi kebutuhan pertanian Anda</p>
            </div>
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                @foreach ($relatedProducts as $related)
                    <a href="{{ route('products.show', array_merge(['slug' => $related['slug']], $catalogQuery)) }}" class="group block">
                        <article class="overflow-hidden rounded-3xl border border-surface-container-highest bg-surface-container-lowest soft-warm-shadow hover-lift">
                            <div class="aspect-square overflow-hidden bg-surface-container-low">
                                <img src="{{ ($related['image'] ?? '') ?: asset('images/placeholder-product.png') }}" alt="{{ $related['name'] }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>
                            <div class="p-6">
                                <p class="mb-2 font-label-eyebrow text-xs font-bold uppercase tracking-widest text-primary">{{ $related['category'] }}</p>
                                <h3 class="mb-3 line-clamp-1 font-headline-md text-xl text-on-surface">{{ $related['name'] }}</h3>
                                <div class="flex items-center justify-between">
                                    <span class="font-body-md text-xl font-bold text-larashop-rose">{{ $related['price'] }}</span>
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-on-background text-on-primary transition-colors group-hover:bg-primary">
                                        <span class="material-symbols-outlined">arrow_forward</span>
                                    </span>
                                </div>
                            </div>
                        </article>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <script>
    (function () {
        const radios = document.querySelectorAll('input[data-variant]');
        if (!radios.length) return;
        const setText = (sel, val) => { const el = document.querySelector(sel); if (el) el.textContent = val; };
        function apply(r) {
            const d = r.dataset;
            setText('[data-variant-label]', d.label);
            setText('[data-variant-sku]', d.sku);
            setText('[data-variant-price]', d.price);
            setText('[data-variant-stock]', d.stock);
            setText('[data-variant-weight]', d.weight);
            setText('[data-variant-dimension]', d.dimension);
            setText('[data-variant-main-price]', d.price);
            const orig = document.querySelector('[data-variant-main-original]');
            if (orig) {
                if (d.original && d.original.trim() !== '') { orig.textContent = d.original; orig.classList.remove('hidden'); }
                else { orig.classList.add('hidden'); }
            }
        }
        radios.forEach((r) => r.addEventListener('change', () => { if (r.checked) apply(r); }));
        const checked = document.querySelector('input[data-variant]:checked') || radios[0];
        if (checked) { checked.checked = true; apply(checked); }
    })();
    </script>
</x-layouts.customer>
