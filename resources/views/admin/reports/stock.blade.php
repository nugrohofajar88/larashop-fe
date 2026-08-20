<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Laporan Stok">
    <section class="space-y-6">
        @include('admin.reports._tabs')

        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Laporan</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Stok</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                Produk yang perlu direstock (stok menipis) & produk yang lama tidak terjual (slow-moving, kandidat promo/clearance). Snapshot saat ini, tidak scoped ke bulan.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Stok Menipis (&le; {{ $meta['low_stock_threshold'] ?? 12 }})</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-rose-600">{{ $meta['low_stock_count'] ?? 0 }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Slow-moving (&ge; {{ $meta['slow_moving_days'] ?? 60 }} hari tidak laku)</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-amber-600">{{ $meta['slow_moving_count'] ?? 0 }}</p>
            </article>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-stone-950">Stok Menipis - Perlu Restock</h2>

            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Produk</th>
                            <th class="px-4 py-3 font-medium">SKU</th>
                            <th class="px-4 py-3 font-medium text-right">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($lowStock as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-stone-900">{{ $item['product_name'] }}</p>
                                    <p class="mt-0.5 text-xs text-stone-500">{{ $item['variant_label'] ?: '-' }}</p>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-stone-600">{{ $item['sku'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-rose-600">{{ $item['stock'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-stone-500">Tidak ada produk dengan stok menipis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($lowStock as $item)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $item['product_name'] }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $item['variant_label'] ?: '-' }} &middot; {{ $item['sku'] }}</p>
                            </div>
                            <p class="font-semibold text-rose-600">{{ $item['stock'] }}</p>
                        </div>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Tidak ada produk dengan stok menipis.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-stone-950">Slow-moving - Kandidat Promo/Clearance</h2>

            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Produk</th>
                            <th class="px-4 py-3 font-medium">SKU</th>
                            <th class="px-4 py-3 font-medium text-right">Stok</th>
                            <th class="px-4 py-3 font-medium">Terakhir Laku</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($slowMoving as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-stone-900">{{ $item['product_name'] }}</p>
                                    <p class="mt-0.5 text-xs text-stone-500">{{ $item['variant_label'] ?: '-' }}</p>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-stone-600">{{ $item['sku'] }}</td>
                                <td class="px-4 py-3 text-right text-stone-800">{{ $item['stock'] }}</td>
                                <td class="px-4 py-3 text-amber-700">{{ $item['last_sold'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-stone-500">Tidak ada produk slow-moving.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($slowMoving as $item)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $item['product_name'] }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $item['variant_label'] ?: '-' }} &middot; {{ $item['sku'] }}</p>
                            </div>
                            <p class="text-stone-800">Stok {{ $item['stock'] }}</p>
                        </div>
                        <p class="mt-2 text-xs text-amber-700">Terakhir laku: {{ $item['last_sold'] }}</p>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Tidak ada produk slow-moving.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.admin>
