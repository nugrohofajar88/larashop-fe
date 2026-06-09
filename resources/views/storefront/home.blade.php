<x-layouts.customer title="Sobat Akar Tani Kimia | Katalog Pertanian">
    <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr] lg:items-center">
        <div class="space-y-5">
            <div class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">
                Mobile-first untuk customer
            </div>
            <div class="space-y-4">
                <h1 class="max-w-xl text-4xl font-semibold leading-tight tracking-tight text-stone-950 sm:text-5xl">
                    Belanja kebutuhan pertanian dengan alur yang ringan, cepat, dan jelas.
                </h1>
                <p class="max-w-xl text-sm leading-7 text-stone-600 sm:text-base">
                    Customer bisa lihat katalog tanpa login, lalu masuk saat siap checkout, cek ongkir, dan memantau status pesanan.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-emerald-900/15 transition hover:bg-emerald-700">
                    Lihat katalog
                </a>
                <a href="{{ route('customer.orders') }}" class="inline-flex items-center justify-center rounded-2xl border border-stone-300 bg-white px-5 py-3.5 text-sm font-semibold text-stone-800 transition hover:border-stone-400">
                    Cek area pesanan
                </a>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                @foreach ($benefits as $benefit)
                    <div class="rounded-2xl border border-stone-200 bg-white/80 px-4 py-4 text-sm text-stone-700 shadow-sm">
                        {{ $benefit }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-xl shadow-stone-300/20">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-semibold text-stone-900">Ringkasan belanja</p>
                    <p class="mt-1 text-sm text-stone-500">Simulasi tampilan customer di mobile</p>
                </div>
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Dummy</span>
            </div>

            <div class="mt-5 space-y-3">
                @foreach ($products as $product)
                    <div class="rounded-2xl bg-stone-50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-stone-950">{{ $product['name'] }}</p>
                                <p class="mt-1 text-xs text-stone-500">{{ $product['unit'] }}</p>
                            </div>
                            @if (! empty($product['badge']))
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">{{ $product['badge'] }}</span>
                            @endif
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-emerald-700">{{ $product['price'] }}</p>
                                <p class="text-xs text-stone-500">{{ $product['stock'] }}</p>
                            </div>
                            <a href="{{ route('products.show', $product['slug']) }}" class="rounded-full bg-stone-900 px-4 py-2 text-xs font-semibold text-white">Detail</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mt-12 space-y-6">
        <x-customer-section-title
            eyebrow="Kategori Utama"
            title="Disusun untuk kebutuhan belanja yang cepat di layar kecil"
            description="Tiap kategori dibuat ringkas, mudah dipindai, dan tetap informatif agar customer nyaman saat membuka dari handphone."
        />

        <div class="grid gap-4 lg:grid-cols-3">
            @foreach ($categories as $category)
                <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-stone-900 text-sm font-bold text-white">
                        {{ $category['icon'] }}
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-stone-950">{{ $category['name'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-stone-600">{{ $category['description'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mt-12 rounded-[2rem] bg-stone-950 px-5 py-6 text-white sm:px-6 lg:px-8">
        <x-customer-section-title
            eyebrow="Flow Customer"
            title="Alur checkout dibuat sederhana"
            description="Customer cukup browsing dulu, lalu login saat ingin menyelesaikan pembelian dan memantau order."
        />

        <div class="mt-6 grid gap-4 lg:grid-cols-3">
            @foreach ($steps as $step)
                <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4 text-sm leading-6 text-stone-200">
                    <span class="mb-3 inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-sm font-semibold text-stone-950">
                        {{ $loop->iteration }}
                    </span>
                    <p>{{ $step }}</p>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.customer>
