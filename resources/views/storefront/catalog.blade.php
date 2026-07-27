<x-layouts.customer title="Sobat Akar Tani Kimia | Katalog">
    @php $hasFilter = $search !== '' || $activeCategory !== 'all' || $activeStatus !== 'all' || $activeSort !== 'default'; @endphp

    @if (! $hasFilter && $products->onFirstPage())
        {{-- ===== Cerita Kami — hero paling atas, gaya "Kopi Senja" ===== --}}
        <section class="mb-20 grid grid-cols-1 items-center gap-gutter lg:grid-cols-2">
            <div>
                <span class="inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700">Cerita Kami</span>
                <h2 class="mb-4 mt-4 font-headline-xl text-headline-lg-mobile leading-tight text-on-surface md:text-headline-xl">Sahabat Bertani, dari Tanam Sampai Panen</h2>
                <p class="mb-6 font-body-md text-body-md leading-relaxed text-on-surface-variant">Kami hadir untuk memudahkan petani dan pekebun mendapatkan pupuk, pestisida, dan kebutuhan pertanian lain yang tepat — tanpa ribet, tanpa perlu jauh-jauh ke toko.</p>

                <div class="mb-6 flex flex-wrap items-center gap-3">
                    <a href="#katalog"
                       class="flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 font-body-md font-bold text-on-primary shadow-lg shadow-primary/20 transition-all hover:bg-secondary active:scale-95">
                        Lihat Katalog
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                    @if (! empty($storeWhatsapp))
                        <a href="https://wa.me/{{ $storeWhatsapp }}?text={{ urlencode('Halo Akar Tani Kimia, saya ingin bertanya seputar produk.') }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-2 rounded-2xl border border-outline-variant/60 bg-surface-container-lowest px-6 py-3 font-body-md font-bold text-on-surface transition-all hover:bg-surface-container-low active:scale-95">
                            Chat via WhatsApp
                        </a>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="https://www.tiktok.com/@akartanikimia" target="_blank" rel="noopener"
                       class="flex items-center gap-1.5 rounded-full bg-[#010101] px-4 py-2 text-xs font-bold text-white transition-transform hover:scale-105">
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                        Ikuti di TikTok
                    </a>
                    <a href="https://id.shp.ee/BWUL2ZbC" target="_blank" rel="noopener"
                       class="flex items-center gap-1.5 rounded-full bg-[#EE4D2D] px-4 py-2 text-xs font-bold text-white transition-transform hover:scale-105">
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                        Belanja di Shopee
                    </a>
                </div>
            </div>

            <div class="relative mx-auto aspect-square w-full max-w-sm overflow-hidden rounded-3xl bg-gradient-to-br from-primary/90 via-secondary to-primary soft-warm-shadow">
                <div class="flex h-full w-full flex-col items-center justify-center gap-4 p-8 text-center text-on-primary">
                    <span class="material-symbols-outlined text-6xl" style="font-variation-settings: 'FILL' 1;">agriculture</span>
                    <p class="font-headline-md text-xl font-bold">Akar Tani Kimia</p>
                    <p class="font-body-sm text-sm opacity-90">Pertanian itu bisnis, bukan judi.</p>
                </div>
                <span class="absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10"></span>
                <span class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-white/10"></span>
            </div>
        </section>
    @endif

    {{-- Section title --}}
    <section class="mb-10 scroll-mt-24" id="katalog">
        <x-customer-section-title
            eyebrow="Hai, Sobat Petani Sukses!"
            title="Yuk, belanja kebutuhan pertanianmu!"
            description="Cek barang dari beragam kategori mulai dari benih unggul, pupuk organik, hingga alat pertanian modern."
        />
    </section>

    {{-- Filter card --}}
    <form method="GET" action="{{ route('catalog') }}" class="mb-8 rounded-xl border border-outline-variant/20 bg-surface-container-lowest p-5 soft-warm-shadow sm:p-6">
        <div class="flex flex-col items-center gap-4 lg:flex-row">
            <div class="relative w-full flex-grow">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari produk, kategori, atau kebutuhan pertanian..."
                    class="w-full rounded-lg border-none bg-surface-container-low py-3 pl-12 pr-4 font-body-md text-on-surface transition-all placeholder:text-outline focus:ring-2 focus:ring-primary/30"
                >
            </div>
            <div class="grid w-full grid-cols-2 gap-3 lg:flex lg:w-auto">
                <select name="category" class="min-w-[150px] appearance-none rounded-lg border-none bg-surface-container-low px-4 py-3 font-body-md text-on-surface-variant focus:ring-2 focus:ring-primary/30">
                    <option value="all" {{ $activeCategory === 'all' ? 'selected' : '' }}>Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" {{ $activeCategory === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
                <select name="status" class="min-w-[150px] appearance-none rounded-lg border-none bg-surface-container-low px-4 py-3 font-body-md text-on-surface-variant focus:ring-2 focus:ring-primary/30">
                    <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>Semua status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ $activeStatus === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                <select name="sort" class="min-w-[150px] appearance-none rounded-lg border-none bg-surface-container-low px-4 py-3 font-body-md text-on-surface-variant focus:ring-2 focus:ring-primary/30">
                    <option value="default" {{ $activeSort === 'default' ? 'selected' : '' }}>Urutkan default</option>
                    <option value="price_asc" {{ $activeSort === 'price_asc' ? 'selected' : '' }}>Harga termurah</option>
                    <option value="price_desc" {{ $activeSort === 'price_desc' ? 'selected' : '' }}>Harga termahal</option>
                    <option value="name_asc" {{ $activeSort === 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                </select>
            </div>
            <button class="flex w-full items-center justify-center gap-2 rounded-lg bg-on-background px-8 py-3 text-on-primary transition-colors hover:bg-primary lg:w-auto">
                <span class="material-symbols-outlined text-base">search</span>
                <span>Cari</span>
            </button>
        </div>
    </form>

    {{-- Results + active chips --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="font-body-md text-on-surface-variant">Menampilkan <span class="font-bold text-on-surface">{{ count($products) }} produk</span>{{ $hasFilter ? ' sesuai filter' : '' }}</span>
            @if ($hasFilter)
                <a href="{{ route('catalog') }}" class="font-bold text-primary hover:underline">Reset filter</a>
            @endif
        </div>
        @if ($hasFilter)
            <div class="flex flex-wrap gap-2">
                @if ($search !== '')
                    <span class="flex items-center gap-2 rounded-full border border-secondary-container bg-secondary-container/30 px-3 py-1 text-body-sm font-medium text-on-secondary-container">Cari: {{ $search }}</span>
                @endif
                @if ($activeCategory !== 'all')
                    <span class="flex items-center gap-2 rounded-full border border-outline-variant/40 bg-surface-container px-3 py-1 text-body-sm font-medium text-on-surface-variant">Kategori: {{ $activeCategory }}</span>
                @endif
                @if ($activeStatus !== 'all')
                    <span class="flex items-center gap-2 rounded-full border border-outline-variant/40 bg-surface-container px-3 py-1 text-body-sm font-medium text-on-surface-variant">Status: {{ $activeStatus }}</span>
                @endif
                @if ($activeSort !== 'default')
                    <span class="flex items-center gap-2 rounded-full border border-outline-variant/40 bg-surface-container px-3 py-1 text-body-sm font-medium text-on-surface-variant">
                        Urutkan: {{ ['price_asc' => 'Harga termurah', 'price_desc' => 'Harga termahal', 'name_asc' => 'Nama A-Z'][$activeSort] ?? $activeSort }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- Product grid --}}
    <div class="grid grid-cols-2 gap-gutter md:grid-cols-4">
        @php $quickAddEnabled = config('storefront.checkout_mode') === 'whatsapp'; @endphp
        @forelse ($products as $product)
            @php
                $quickAddPayload = $quickAddEnabled ? [
                    'id' => $product['id'],
                    'slug' => $product['slug'],
                    'name' => $product['name'],
                    'image' => $product['image'] ?? null,
                    'variants' => array_map(fn ($v) => ['id' => $v['id'], 'label' => $v['label'], 'price' => $v['price'], 'stock' => $v['stock']], $product['variants'] ?? []),
                ] : null;
            @endphp
            <article class="group h-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest p-3 soft-warm-shadow hover-lift">
                <a href="{{ route('products.show', array_merge(['slug' => $product['slug']], $catalogQuery)) }}" class="block">
                    <div class="relative mb-4 aspect-square overflow-hidden rounded-lg bg-gradient-to-br from-surface-container to-white">
                        @if (!empty($product['discount_badge']))
                            <span class="absolute left-2 top-2 z-10 rounded-full bg-error px-2 py-1 font-label-eyebrow text-label-eyebrow text-on-error">{{ $product['discount_badge'] }}</span>
                        @endif
                        @if (!empty($product['badge']))
                            <span class="absolute right-2 top-2 z-10 rounded-full bg-primary px-2 py-1 font-label-eyebrow text-label-eyebrow text-on-primary">{{ $product['badge'] }}</span>
                        @endif
                        <img
                            src="{{ ($product['image'] ?? '') ?: asset('images/placeholder-product.png') }}"
                            alt="{{ $product['name'] }}"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                        >
                    </div>
                    <div class="px-1">
                        <h3 class="mb-2 line-clamp-2 min-h-[3rem] font-body-md font-medium text-on-surface">{{ $product['name'] }}</h3>
                        <div class="mb-3 flex items-center gap-2">
                            <span class="font-headline-md text-xl text-error">{{ $product['price'] }}</span>
                            @if (!empty($product['original_price']))
                                <span class="text-body-sm text-outline line-through">{{ $product['original_price'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                <div class="flex items-center justify-between border-t border-outline-variant/20 px-1 pt-3">
                    <span class="text-body-sm text-on-surface-variant">{{ $product['sold_label'] }}</span>
                    @if ($quickAddPayload)
                        <button type="button" data-quick-add-trigger data-product="{{ json_encode($quickAddPayload) }}"
                            aria-label="Tambah {{ $product['name'] }} ke keranjang"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container/50 text-on-secondary-container transition-colors hover:bg-primary hover:text-on-primary group-hover:bg-primary group-hover:text-on-primary">
                            <span class="material-symbols-outlined text-xl">shopping_cart</span>
                        </button>
                    @else
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container/50 text-on-secondary-container transition-colors group-hover:bg-primary group-hover:text-on-primary">
                            <span class="material-symbols-outlined text-xl">shopping_cart</span>
                        </span>
                    @endif
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-outline-variant/50 bg-surface-container-low px-6 py-12 text-center text-body-md text-on-surface-variant">
                Produk tidak ditemukan untuk kata kunci atau kategori yang dipilih.
            </div>
        @endforelse
    </div>

    @if (! $quickAddEnabled && $products->hasPages())
        <div class="mt-8 flex items-center justify-center gap-3">
            @if ($products->onFirstPage())
                <span class="cursor-not-allowed rounded-full border border-outline-variant/40 px-5 py-2 font-body-sm text-outline">Sebelumnya</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="rounded-full border border-outline-variant/60 px-5 py-2 font-body-sm text-on-surface transition-colors hover:bg-surface-container">Sebelumnya</a>
            @endif
            <span class="font-body-sm text-on-surface-variant">Halaman {{ $products->currentPage() }} / {{ $products->lastPage() }}</span>
            @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="rounded-full border border-outline-variant/60 px-5 py-2 font-body-sm text-on-surface transition-colors hover:bg-surface-container">Berikutnya</a>
            @else
                <span class="cursor-not-allowed rounded-full border border-outline-variant/40 px-5 py-2 font-body-sm text-outline">Berikutnya</span>
            @endif
        </div>
    @endif

    @if (! $hasFilter && $products->onFirstPage())
        {{-- ===== Tentang Kami — lanjutan (nilai, cara kerja, CTA); nav "Tentang" scroll ke sini ===== --}}
        <section id="tentang" class="mt-24 scroll-mt-24">
            {{-- Kenapa pilih kami --}}
            <div class="mb-16">
                <div class="mb-8 text-center">
                    <span class="inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700">Kenapa Petani Percaya Kami</span>
                    <h2 class="mx-auto mt-4 max-w-xl font-headline-lg text-headline-lg leading-tight text-on-surface">Bukan Cuma Jualan, Kami Juga Peduli Hasil Tani Anda</h2>
                </div>

                <div class="grid grid-cols-1 gap-gutter sm:grid-cols-2 lg:grid-cols-4">
                    @php
                        $aboutValues = [
                            ['icon' => 'verified', 'title' => 'Produk Asli & Terjamin', 'desc' => 'Setiap produk yang kami jual jelas asal-usulnya, bukan barang tiruan atau tidak jelas kualitasnya.'],
                            ['icon' => 'chat', 'title' => 'Konsultasi Gratis via WhatsApp', 'desc' => 'Bingung pilih produk yang mana? Tanya dulu ke kami sebelum beli, tidak perlu ragu-ragu.'],
                            ['icon' => 'local_shipping', 'title' => 'Dikirim ke Seluruh Indonesia', 'desc' => 'Dari kota besar sampai pelosok, pesanan Anda kami usahakan sampai dengan aman dan cepat.'],
                            ['icon' => 'payments', 'title' => 'Harga Jujur & Transparan', 'desc' => 'Harga yang Anda lihat adalah harga yang Anda bayar — tanpa biaya siluman di kemudian hari.'],
                        ];
                    @endphp

                    @foreach ($aboutValues as $value)
                        <div class="rounded-3xl border border-outline-variant/30 bg-surface-container-lowest p-6 soft-warm-shadow hover-lift">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <span class="material-symbols-outlined text-2xl">{{ $value['icon'] }}</span>
                            </div>
                            <h3 class="mb-2 font-body-md font-bold text-on-surface">{{ $value['title'] }}</h3>
                            <p class="font-body-sm text-sm leading-relaxed text-on-surface-variant">{{ $value['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Cara kerja --}}
            <div class="mb-16 rounded-3xl border border-outline-variant/30 bg-surface-container-lowest p-8 soft-warm-shadow md:p-10">
                <div class="mb-8 text-center">
                    <span class="inline-block rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-700">Gampang Kok</span>
                    <h2 class="mx-auto mt-4 max-w-xl font-headline-lg text-headline-lg leading-tight text-on-surface">Tiga Langkah Menuju Panen yang Lebih Baik</h2>
                </div>

                <div class="grid grid-cols-1 gap-gutter md:grid-cols-3">
                    @php
                        $aboutSteps = [
                            ['num' => '1', 'title' => 'Pilih Produk', 'desc' => 'Cari kebutuhan Anda di katalog — pupuk, pestisida, atau alat pertanian lainnya.'],
                            ['num' => '2', 'title' => 'Konsultasi (Kalau Perlu)', 'desc' => 'Ragu produk mana yang cocok? Chat kami dulu, kami bantu carikan yang paling pas.'],
                            ['num' => '3', 'title' => 'Pesanan Meluncur', 'desc' => 'Setelah dikonfirmasi, pesanan kami proses dan kirim secepat mungkin ke alamat Anda.'],
                        ];
                    @endphp

                    @foreach ($aboutSteps as $step)
                        <div class="flex flex-col items-center text-center">
                            <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-primary font-headline-md text-lg font-bold text-on-primary">{{ $step['num'] }}</span>
                            <h3 class="mb-1 font-body-md font-bold text-on-surface">{{ $step['title'] }}</h3>
                            <p class="max-w-xs font-body-sm text-sm leading-relaxed text-on-surface-variant">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- CTA --}}
            <div class="rounded-3xl bg-gradient-to-br from-primary to-secondary p-8 text-center text-on-primary soft-warm-shadow md:p-12">
                <h2 class="mb-3 font-headline-lg text-headline-lg leading-tight">Masih Ragu? Ngobrol Dulu Aja Sama Kami</h2>
                <p class="mx-auto mb-8 max-w-lg font-body-md text-body-md opacity-90">Tim kami siap bantu jawab pertanyaan seputar produk, dosis pemakaian, sampai rekomendasi yang paling cocok untuk lahan Anda.</p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    @if (! empty($storeWhatsapp))
                        <a href="https://wa.me/{{ $storeWhatsapp }}?text={{ urlencode('Halo Akar Tani Kimia, saya ingin bertanya seputar produk.') }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-2 rounded-2xl bg-on-background px-6 py-3 font-body-md font-bold text-on-primary transition-all hover:bg-black active:scale-95">
                            <span class="material-symbols-outlined">chat</span>
                            Chat via WhatsApp
                        </a>
                    @endif
                    <a href="#katalog"
                       class="flex items-center gap-2 rounded-2xl border border-on-primary/40 bg-white/10 px-6 py-3 font-body-md font-bold text-on-primary transition-all hover:bg-white/20 active:scale-95">
                        <span class="material-symbols-outlined">storefront</span>
                        Lihat Katalog
                    </a>
                </div>
            </div>
        </section>
    @endif
</x-layouts.customer>
