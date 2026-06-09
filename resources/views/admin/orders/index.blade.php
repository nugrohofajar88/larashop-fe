<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Orders">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Orders</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Kelola pesanan, validasi pembayaran, dan proses shipment</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Halaman ini jadi pusat kerja admin untuk memantau order baru, mengecek pembayaran, dan meneruskan pesanan ke proses pengiriman.
                </p>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-stone-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-950">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm text-stone-600">{{ $stat['note'] }}</p>
                </article>
            @endforeach
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <form class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between" method="GET" action="{{ route('admin.orders.index') }}">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Daftar order</h2>
                    <p class="mt-1 text-sm text-stone-500">Filter order berdasarkan status dan cari berdasarkan kode, customer, atau nomor WhatsApp.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari order atau customer..."
                        class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none placeholder:text-stone-400 focus:border-emerald-500 focus:bg-white lg:w-72"
                    >
                    @if ($activeStatus !== 'all')
                        <input type="hidden" name="status" value="{{ $activeStatus }}">
                    @endif
                    <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Terapkan</button>
                </div>
            </form>

            <div class="mt-5 overflow-x-auto">
                <div class="inline-flex min-w-full gap-2 rounded-[1.5rem] border border-stone-200 bg-stone-50/80 p-2">
                    @foreach ($statusTabs as $tab)
                        <a
                            href="{{ route('admin.orders.index', array_filter(['status' => $tab['key'] === 'all' ? null : $tab['key'], 'search' => $search !== '' ? $search : null])) }}"
                            class="whitespace-nowrap rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ $activeStatus === $tab['key'] ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:bg-white hover:text-stone-900' }}"
                        >
                            {{ $tab['label'] }} ({{ $tab['count'] }})
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Desktop: tabel. Wrapper overflow-visible (bukan overflow-x-auto) supaya dropdown
                 aksi ⋮ tidak terpotong, terutama di baris paling atas. --}}
            <div class="mt-5 hidden rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="rounded-tl-2xl px-4 py-3 font-medium">Order</th>
                            <th class="px-4 py-3 font-medium">Customer</th>
                            <th class="px-4 py-3 font-medium">Nominal</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Shipment</th>
                            <th class="rounded-tr-2xl px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <a href="{{ route('admin.orders.show', $order['code']) }}" class="font-semibold text-stone-900">{{ $order['code'] }}</a>
                                    <p class="mt-1 text-xs text-stone-500">{{ $order['date'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <p class="text-stone-900">{{ $order['customer'] }}</p>
                                    <p class="mt-1 text-xs text-stone-500">{{ $order['phone'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top text-stone-700">{{ $order['total'] }}</td>
                                <td class="px-4 py-4 align-top">
                                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">{{ $order['status_label'] ?? $order['status'] }}</span>
                                    <p class="mt-2 text-xs text-stone-500">{{ $order['payment_status'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top text-stone-600">
                                    <p>{{ $order['shipping_service'] }}</p>
                                    <p class="mt-1 text-xs text-stone-500">{{ $order['awb'] ?? 'Belum ada AWB' }}</p>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    @include('admin.orders._row-actions')
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada order yang cocok dengan filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: kartu --}}
            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($orders as $order)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <a href="{{ route('admin.orders.show', $order['code']) }}" class="font-semibold text-stone-900">{{ $order['code'] }}</a>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $order['date'] }}</p>
                            </div>
                            @include('admin.orders._row-actions')
                        </div>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Customer</dt>
                                <dd class="text-right text-stone-800">{{ $order['customer'] }}<span class="block text-xs text-stone-500">{{ $order['phone'] }}</span></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Nominal</dt>
                                <dd class="text-right font-medium text-stone-900">{{ $order['total'] }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Status</dt>
                                <dd class="text-right"><span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700">{{ $order['status_label'] ?? $order['status'] }}</span><span class="mt-1 block text-xs text-stone-500">{{ $order['payment_status'] }}</span></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Shipment</dt>
                                <dd class="text-right text-stone-700">{{ $order['shipping_service'] }}<span class="block text-xs text-stone-500">{{ $order['awb'] ?? 'Belum ada AWB' }}</span></dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada order yang cocok dengan filter saat ini.</p>
                @endforelse
            </div>
        </section>
    </section>

    {{-- Dropdown aksi ⋮: buka satu menutup yang lain, klik di luar / Esc menutup. --}}
    <script>
        (function () {
            const openMenus = () => document.querySelectorAll('details[data-row-menu][open]');

            // Saat satu menu dibuka, tutup yang lain (toggle tidak bubble -> pakai capture).
            document.addEventListener('toggle', function (e) {
                const el = e.target;
                if (!(el instanceof HTMLDetailsElement) || !el.hasAttribute('data-row-menu') || !el.open) return;
                openMenus().forEach((d) => { if (d !== el) d.removeAttribute('open'); });
            }, true);

            // Klik di luar menu yang terbuka -> tutup.
            document.addEventListener('click', function (e) {
                openMenus().forEach((d) => { if (!d.contains(e.target)) d.removeAttribute('open'); });
            });

            // Esc -> tutup semua.
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') openMenus().forEach((d) => d.removeAttribute('open'));
            });
        })();
    </script>
</x-layouts.admin>
