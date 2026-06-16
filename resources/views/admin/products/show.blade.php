<x-layouts.admin :title="'Admin Sobat Akar Tani Kimia | ' . $product['name']">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Product Detail</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">{{ $product['name'] }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Halaman review produk untuk membantu admin mengecek kesiapan data katalog, stok, dan pengiriman.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.products.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke daftar
                </a>
                <a href="{{ route('admin.products.edit', $product['sku']) }}" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Edit produk
                </a>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="space-y-6">
                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-stone-500">{{ $product['category'] }} / {{ $product['sku'] }}</p>
                            <h2 class="mt-2 text-2xl font-semibold text-stone-950">{{ $product['name'] }}</h2>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="w-fit rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">{{ $product['status'] }}</span>
                            <span class="w-fit rounded-full {{ $product['stock'] <= 12 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700' }} px-3 py-1 text-xs font-semibold">
                                {{ $product['highlight'] }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 max-w-none space-y-2 text-sm text-stone-700 [&_blockquote]:border-l-2 [&_blockquote]:border-stone-300 [&_blockquote]:pl-3 [&_h1]:text-base [&_h1]:font-bold [&_h2]:font-semibold [&_ol]:list-decimal [&_ol]:pl-5 [&_strong]:font-semibold [&_ul]:list-disc [&_ul]:pl-5">
                        {!! $product['description'] !!}
                    </div>
                </section>

                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Galeri produk</h2>
                    <div class="mt-5 -mx-1 overflow-x-auto pb-1">
                        <div class="flex min-w-full gap-4 px-1">
                        @foreach ($images as $image)
                            <article class="w-64 shrink-0 rounded-[1.5rem] border border-stone-200 p-4">
                                <img
                                    src="{{ $image['path'] }}"
                                    alt="{{ $image['label'] }} {{ $product['name'] }}"
                                    class="aspect-square w-full rounded-2xl object-cover"
                                >
                                <div class="mt-4">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-stone-900">{{ $image['label'] }}</p>
                                        @if ($image['is_primary'])
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">Utama</span>
                                        @endif
                                    </div>
                                    <p class="mt-2 text-sm text-stone-500">{{ $image['name'] }}</p>
                                </div>
                            </article>
                        @endforeach
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Varian produk</h2>

                    <div class="mt-5 space-y-3">
                        @foreach ($product['variants'] as $variant)
                            <article class="rounded-[1.5rem] border border-stone-200 px-4 py-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-base font-semibold text-stone-900">{{ $variant['label'] }}</p>
                                            @if ($variant['is_default'])
                                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">Default</span>
                                            @endif
                                            @if (! $variant['is_active'])
                                                <span class="rounded-full bg-stone-100 px-3 py-1 text-[11px] font-semibold text-stone-600">Nonaktif</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $variant['sku'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-emerald-700">{{ $variant['price'] }}</p>
                                        <p class="mt-1 text-xs text-stone-500">Stok {{ $variant['stock'] }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 md:grid-cols-3">
                                    <div class="rounded-2xl bg-stone-50 px-4 py-3">
                                        <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Berat</p>
                                        <p class="mt-2 font-semibold text-stone-900">{{ $variant['weight_grams'] ? number_format($variant['weight_grams'], 0, ',', '.') . ' gram' : '-' }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-stone-50 px-4 py-3">
                                        <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Dimensi</p>
                                        <p class="mt-2 font-semibold text-stone-900">{{ $variant['dimension'] }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-stone-50 px-4 py-3">
                                        <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Harga coret</p>
                                        <p class="mt-2 font-semibold text-stone-900">{{ $variant['compare_at_price'] ? 'Rp' . number_format($variant['compare_at_price'], 0, ',', '.') : '-' }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Informasi pengiriman</h2>

                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Berat</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $product['weight'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Panjang</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $product['length'] }} cm</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Lebar</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $product['width'] }} cm</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Tinggi</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $product['height'] }} cm</p>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Ringkasan produk</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Harga jual</span>
                            <span class="font-semibold text-stone-900">{{ $product['default_variant']['price'] ?? $product['price'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Total stok</span>
                            <span class="font-semibold text-stone-900">{{ $product['stock'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Varian default</span>
                            <span class="font-semibold text-stone-900">{{ $product['default_variant']['label'] ?? $product['unit'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Jumlah varian</span>
                            <span class="font-semibold text-stone-900">{{ $product['variant_count'] }}</span>
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Aksi cepat</h2>
                    <div class="mt-5 space-y-3">
                        <a href="{{ route('admin.products.edit', $product['sku']) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                            Edit data produk
                        </a>
                        <button class="inline-flex w-full items-center justify-center rounded-2xl border border-stone-300 bg-white px-5 py-3 text-sm font-medium text-stone-700">
                            Update stok
                        </button>
                    </div>
                </section>
            </aside>
        </div>
    </section>
</x-layouts.admin>
