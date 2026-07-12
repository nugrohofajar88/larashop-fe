<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Produk">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Products</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Kelola katalog produk dengan ringkas dan terstruktur</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Halaman ini disiapkan untuk operasional admin: cek stok, update informasi pengiriman, dan menjaga katalog customer tetap akurat.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Import data
                </button>
                <a href="{{ route('admin.products.create') }}" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Tambah produk
                </a>
            </div>
        </div>

        <div>
            <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-950">Daftar produk</h2>
                        <p class="mt-1 text-sm text-stone-500">Daftar produk katalog beserta stok dan statusnya.</p>
                    </div>

                    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-3">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama produk atau SKU..."
                            class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none placeholder:text-stone-400 focus:border-emerald-500 focus:bg-white lg:w-72"
                        >
                        <select name="category" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                            <option value="all" {{ $activeCategory === 'all' ? 'selected' : '' }}>Semua kategori</option>
                            @foreach ($categories as $categoryKey => $categoryLabel)
                                <option value="{{ $categoryKey }}" {{ $activeCategory === $categoryKey ? 'selected' : '' }}>{{ $categoryLabel }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                            <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>Semua status</option>
                            @foreach ($statuses as $statusKey => $statusLabel)
                                <option value="{{ $statusKey }}" {{ $activeStatus === $statusKey ? 'selected' : '' }}>{{ $statusLabel }}</option>
                            @endforeach
                        </select>
                        <select name="sort" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                            <option value="default" {{ $activeSort === 'default' ? 'selected' : '' }}>Urutkan default</option>
                            <option value="price_desc" {{ $activeSort === 'price_desc' ? 'selected' : '' }}>Harga tertinggi</option>
                            <option value="price_asc" {{ $activeSort === 'price_asc' ? 'selected' : '' }}>Harga terendah</option>
                            <option value="stock_asc" {{ $activeSort === 'stock_asc' ? 'selected' : '' }}>Stok paling tipis</option>
                            <option value="stock_desc" {{ $activeSort === 'stock_desc' ? 'selected' : '' }}>Stok terbanyak</option>
                            <option value="name_asc" {{ $activeSort === 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                        </select>
                        <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Terapkan</button>
                    </form>
                </div>

                <div class="mt-4 flex items-center justify-between gap-4 text-sm text-stone-500">
                    <p>Menampilkan {{ $products->total() }} produk{{ $search !== '' || $activeCategory !== 'all' || $activeStatus !== 'all' || $activeSort !== 'default' ? ' sesuai filter' : '' }}.</p>
                    @if ($search !== '' || $activeCategory !== 'all' || $activeStatus !== 'all' || $activeSort !== 'default')
                        <a href="{{ route('admin.products.index') }}" class="font-semibold text-emerald-700">Reset filter</a>
                    @endif
                </div>

                @if ($search !== '' || $activeCategory !== 'all' || $activeStatus !== 'all' || $activeSort !== 'default')
                    <div class="mt-3 flex flex-wrap gap-2">
                        @if ($search !== '')
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Cari: {{ $search }}</span>
                        @endif
                        @if ($activeCategory !== 'all')
                            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">Kategori: {{ $categories[$activeCategory] ?? $activeCategory }}</span>
                        @endif
                        @if ($activeStatus !== 'all')
                            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">Status: {{ $statuses[$activeStatus] ?? $activeStatus }}</span>
                        @endif
                        @if ($activeSort === 'price_desc')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Urutkan: Harga tertinggi</span>
                        @elseif ($activeSort === 'price_asc')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Urutkan: Harga terendah</span>
                        @elseif ($activeSort === 'stock_asc')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Urutkan: Stok paling tipis</span>
                        @elseif ($activeSort === 'stock_desc')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Urutkan: Stok terbanyak</span>
                        @elseif ($activeSort === 'name_asc')
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Urutkan: Nama A-Z</span>
                        @endif
                    </div>
                @endif

                {{-- Desktop: tabel --}}
                <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-stone-50 text-stone-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Produk</th>
                                <th class="px-4 py-3 font-medium">Harga</th>
                                <th class="px-4 py-3 font-medium">Stok</th>
                                <th class="px-4 py-3 font-medium">Berat / Dimensi</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-4 py-4 align-top">
                                        <div class="flex items-start gap-3">
                                            <img
                                                src="{{ $product['image'] }}"
                                                alt="{{ $product['name'] }}"
                                                class="h-14 w-14 shrink-0 rounded-2xl border border-stone-200 object-cover"
                                            >
                                            <div class="min-w-0">
                                                <p class="font-semibold text-stone-900">{{ $product['name'] }}</p>
                                                <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $product['category'] }} / {{ $product['sku'] }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 align-top text-stone-700">{{ $product['price'] }}</td>
                                    <td class="px-4 py-4 align-top">
                                        <span class="font-semibold {{ $product['stock'] <= 12 ? 'text-amber-700' : 'text-stone-900' }}">{{ $product['stock'] }}</span>
                                        <p class="mt-1 text-xs text-stone-500">{{ $product['variant_count'] }} varian</p>
                                    </td>
                                    <td class="px-4 py-4 align-top text-stone-600">
                                        <p>{{ $product['default_variant']['label'] ?? $product['weight'] }}</p>
                                        <p class="mt-1 text-xs text-stone-500">{{ $product['dimension'] }}</p>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <span class="w-fit rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">
                                            {{ $product['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('admin.products.show', $product['sku']) }}" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-medium text-stone-700">
                                                Detail
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product['sku']) }}" class="rounded-full bg-stone-900 px-3 py-2 text-xs font-medium text-white">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.products.destroy', $product['sku']) }}"
                                                  data-confirm="Hapus produk {{ $product['name'] }}? Produk yang pernah diorder akan diarsipkan, sisanya dihapus permanen."
                                                  data-confirm-title="Hapus produk"
                                                  data-confirm-ok="Ya, hapus">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-100">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada produk yang cocok dengan filter saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile: kartu --}}
                <div class="mt-5 space-y-3 md:hidden">
                    @forelse ($products as $product)
                        <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                            <div class="flex items-start gap-3">
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="h-14 w-14 shrink-0 rounded-2xl border border-stone-200 object-cover">
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-stone-900">{{ $product['name'] }}</p>
                                    <p class="mt-0.5 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $product['category'] }} / {{ $product['sku'] }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700">{{ $product['status'] }}</span>
                            </div>
                            <dl class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                                <div>
                                    <dt class="text-stone-500">Harga</dt>
                                    <dd class="font-medium text-stone-900">{{ $product['price'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-stone-500">Stok</dt>
                                    <dd class="font-semibold {{ $product['stock'] <= 12 ? 'text-amber-700' : 'text-stone-900' }}">{{ $product['stock'] }} <span class="text-xs font-normal text-stone-500">({{ $product['variant_count'] }} varian)</span></dd>
                                </div>
                                <div class="col-span-2">
                                    <dt class="text-stone-500">Berat / Dimensi</dt>
                                    <dd class="text-stone-700">{{ $product['default_variant']['label'] ?? $product['weight'] }} · {{ $product['dimension'] }}</dd>
                                </div>
                            </dl>
                            <div class="mt-3 flex gap-2">
                                <a href="{{ route('admin.products.show', $product['sku']) }}" class="flex-1 rounded-full border border-stone-300 px-3 py-2 text-center text-xs font-medium text-stone-700">Detail</a>
                                <a href="{{ route('admin.products.edit', $product['sku']) }}" class="flex-1 rounded-full bg-stone-900 px-3 py-2 text-center text-xs font-medium text-white">Edit</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product['sku']) }}" class="flex-1"
                                      data-confirm="Hapus produk {{ $product['name'] }}? Produk yang pernah diorder akan diarsipkan, sisanya dihapus permanen."
                                      data-confirm-title="Hapus produk"
                                      data-confirm-ok="Ya, hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-center text-xs font-medium text-rose-600 hover:bg-rose-100">Hapus</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada produk yang cocok dengan filter saat ini.</p>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <div class="mt-5 flex items-center justify-between gap-3 text-sm">
                        <span class="text-stone-500">Halaman {{ $products->currentPage() }} dari {{ $products->lastPage() }}</span>
                        <div class="flex gap-2">
                            @if ($products->onFirstPage())
                                <span class="cursor-not-allowed rounded-xl border border-stone-200 px-4 py-2 text-stone-300">Sebelumnya</span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}" class="rounded-xl border border-stone-300 px-4 py-2 font-medium text-stone-700 hover:bg-stone-50">Sebelumnya</a>
                            @endif
                            @if ($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}" class="rounded-xl border border-stone-300 px-4 py-2 font-medium text-stone-700 hover:bg-stone-50">Berikutnya</a>
                            @else
                                <span class="cursor-not-allowed rounded-xl border border-stone-200 px-4 py-2 text-stone-300">Berikutnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </section>
</x-layouts.admin>
