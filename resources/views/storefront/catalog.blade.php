<x-layouts.customer title="Sobat Akar Tani Kimia | Katalog">
    @php $hasFilter = $search !== '' || $activeCategory !== 'all' || $activeStatus !== 'all' || $activeSort !== 'default'; @endphp

    {{-- Section title --}}
    <section class="mb-10">
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
        @forelse ($products as $product)
            <a href="{{ route('products.show', array_merge(['slug' => $product['slug']], $catalogQuery)) }}" class="group block">
                <article class="h-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest p-3 soft-warm-shadow hover-lift">
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
                        <div class="flex items-center justify-between border-t border-outline-variant/20 pt-3">
                            <span class="text-body-sm text-on-surface-variant">{{ $product['sold_label'] }}</span>
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-secondary-container/50 text-on-secondary-container transition-colors group-hover:bg-primary group-hover:text-on-primary">
                                <span class="material-symbols-outlined text-xl">shopping_cart</span>
                            </span>
                        </div>
                    </div>
                </article>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-outline-variant/50 bg-surface-container-low px-6 py-12 text-center text-body-md text-on-surface-variant">
                Produk tidak ditemukan untuk kata kunci atau kategori yang dipilih.
            </div>
        @endforelse
    </div>
</x-layouts.customer>
