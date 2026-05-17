<x-layouts.customer title="Larashop | Katalog">
    <section class="space-y-6">
        <x-customer-section-title
            eyebrow="Hai, Sobat Petani Sukses!"
            title="Yuk, belanja kebutuhan pertanianmu!"
            description="Cek barang dari beragam kategori"
        />

        <form method="GET" action="{{ route('catalog') }}" class="rounded-[1.75rem] border border-stone-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_200px_200px_220px_auto]">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari produk, kategori, atau kebutuhan pertanian..."
                    class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-emerald-500 focus:bg-white"
                >
                <select
                    name="category"
                    class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none transition focus:border-emerald-500 focus:bg-white"
                >
                    <option value="all" {{ $activeCategory === 'all' ? 'selected' : '' }}>Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" {{ $activeCategory === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
                <select
                    name="status"
                    class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none transition focus:border-emerald-500 focus:bg-white"
                >
                    <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>Semua status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ $activeStatus === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                <select
                    name="sort"
                    class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none transition focus:border-emerald-500 focus:bg-white"
                >
                    <option value="default" {{ $activeSort === 'default' ? 'selected' : '' }}>Urutkan default</option>
                    <option value="price_asc" {{ $activeSort === 'price_asc' ? 'selected' : '' }}>Harga termurah</option>
                    <option value="price_desc" {{ $activeSort === 'price_desc' ? 'selected' : '' }}>Harga termahal</option>
                    <option value="name_asc" {{ $activeSort === 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                </select>
                <button class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Cari</button>
            </div>
        </form>

        <div class="flex items-center justify-between gap-4 text-sm text-stone-500">
            <p>Menampilkan {{ count($products) }} produk{{ $search !== '' || $activeCategory !== 'all' || $activeStatus !== 'all' || $activeSort !== 'default' ? ' sesuai filter' : '' }}.</p>
            @if ($search !== '' || $activeCategory !== 'all' || $activeStatus !== 'all' || $activeSort !== 'default')
                <a href="{{ route('catalog') }}" class="font-semibold text-emerald-700">Reset filter</a>
            @endif
        </div>

        @if ($search !== '' || $activeCategory !== 'all' || $activeStatus !== 'all' || $activeSort !== 'default')
            <div class="flex flex-wrap gap-2">
                @if ($search !== '')
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Cari: {{ $search }}</span>
                @endif
                @if ($activeCategory !== 'all')
                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">Kategori: {{ $activeCategory }}</span>
                @endif
                @if ($activeStatus !== 'all')
                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">Status: {{ $activeStatus }}</span>
                @endif
                @if ($activeSort === 'price_asc')
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Urutkan: Harga termurah</span>
                @elseif ($activeSort === 'price_desc')
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Urutkan: Harga termahal</span>
                @elseif ($activeSort === 'name_asc')
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Urutkan: Nama A-Z</span>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse ($products as $product)
                <a href="{{ route('products.show', array_merge(['slug' => $product['slug']], $catalogQuery)) }}" class="group block">
                    <article class="relative overflow-hidden rounded-[1.35rem] border border-stone-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                        <div class="overflow-hidden border-b border-stone-200 bg-white">
                            <div class="aspect-[0.9] bg-white p-2">
                                <img
                                    src="{{ $product['image'] }}"
                                    alt="{{ $product['name'] }}"
                                    class="h-full w-full rounded-[1rem] object-cover"
                                >
                            </div>
                        </div>

                        <div class="space-y-3 p-3">
                            <div class="space-y-2">
                                <h3 class="min-h-[3.3rem] text-[0.95rem] font-semibold leading-5 text-stone-900">
                                    {{ $product['name'] }}
                                </h3>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-end gap-2">
                                    <p class="text-[1.1rem] font-bold tracking-tight text-rose-600">{{ $product['price'] }}</p>
                                    <span class="rounded-md bg-rose-50 px-1.5 py-0.5 text-[11px] font-semibold text-rose-500">{{ $product['discount_badge'] }}</span>
                                </div>
                                <p class="text-xs text-stone-400 line-through">{{ $product['original_price'] }}</p>
                            </div>

                            <div class="border-t border-stone-100 pt-2 text-xs text-stone-500">
                                <span class="truncate">{{ $product['sold_label'] }}</span>
                            </div>
                        </div>
                    </article>
                </a>
            @empty
                <div class="col-span-full rounded-[1.75rem] border border-dashed border-stone-300 bg-stone-50 px-6 py-10 text-center text-sm text-stone-500">
                    Produk tidak ditemukan untuk kata kunci atau kategori yang dipilih.
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.customer>
