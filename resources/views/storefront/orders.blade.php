<x-layouts.customer title="Larashop | Pesanan">
    <section class="space-y-6">
        <x-customer-section-title
            eyebrow="Area Login Customer"
            title="Riwayat dan status pesanan hanya untuk user yang sudah masuk"
            description="Sesuai flow aplikasi, customer tetap bisa lihat katalog tanpa login, tetapi checkout, status order, dan riwayat pesanan membutuhkan autentikasi."
        />

        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)] lg:items-start">
            <x-customer.account-nav />

            <div class="space-y-6">
                <div class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-lg font-semibold text-stone-950">Riwayat pesanan customer</p>
                            <p class="mt-2 text-sm leading-6 text-stone-600">
                                Semua data pesanan di halaman ini sekarang mengikuti order aktif dari backend API.
                            </p>
                        </div>
                        <a href="{{ route('checkout') }}" class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Lanjut checkout</a>
                    </div>
                </div>

                <div class="sticky top-[5.25rem] z-20 -mx-4 bg-[#f6f1e7]/95 px-4 py-1 backdrop-blur sm:-mx-6 sm:px-6 lg:static lg:z-auto lg:mx-0 lg:bg-transparent lg:px-0 lg:py-0 lg:backdrop-blur-0">
                    <div class="overflow-x-auto">
                        <div class="inline-flex min-w-full gap-2 rounded-[1.75rem] border border-stone-200 bg-white p-2 shadow-sm">
                            @foreach ($orderTabs as $tab)
                                <a
                                href="{{ route('customer.orders', $tab['key'] === 'all' ? [] : ['status' => $tab['key']]) }}"
                                class="whitespace-nowrap rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ $activeOrderStatus === $tab['key'] ? 'bg-stone-900 text-white' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-900' }}"
                            >
                                {{ $tab['label'] }} ({{ $tab['count'] }})
                            </a>
                        @endforeach
                    </div>
                </div>
                </div>

                @if ($orders === [])
                    <div class="rounded-[1.75rem] border border-dashed border-stone-300 bg-white px-5 py-10 text-center shadow-sm">
                        <p class="text-base font-semibold text-stone-900">Belum ada pesanan di tab ini.</p>
                        <p class="mt-2 text-sm text-stone-500">Coba pindah tab lain atau lanjut belanja untuk membuat pesanan baru.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($orders as $order)
                            @php
                                $status = $order['status'] ?? '';
                                $badgeClasses = match ($status) {
                                    'pending_payment' => 'bg-amber-100 text-amber-800',
                                    'paid', 'processing' => 'bg-emerald-100 text-emerald-800',
                                    'shipped', 'completed' => 'bg-sky-100 text-sky-800',
                                    'cancelled' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-stone-100 text-stone-700',
                                };
                            @endphp
                            <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-semibold text-stone-950">{{ $order['code'] }}</p>
                                        <p class="mt-1 text-sm text-stone-500">{{ $order['date'] }}</p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">{{ $order['status_label'] ?? $order['status'] }}</span>
                                </div>
                                <div class="mt-4 flex items-end justify-between gap-4">
                                    <div>
                                        <p class="text-sm text-stone-500">Total pembayaran</p>
                                        <p class="mt-1 text-lg font-semibold {{ $status === 'cancelled' ? 'text-rose-700' : 'text-emerald-700' }}">{{ $order['total'] }}</p>
                                    </div>
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        @if (($order['status'] ?? null) === 'pending_payment')
                                            <form action="{{ route('customer.orders.cancel', $order['code']) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?')">
                                                @csrf
                                                <button type="submit" class="rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @endif
                                        @if (($order['status'] ?? null) === 'shipped')
                                            <form action="{{ route('customer.orders.complete', $order['code']) }}" method="POST" onsubmit="return confirm('Tandai pesanan ini sudah diterima?')">
                                                @csrf
                                                <button type="submit" class="rounded-full border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700">
                                                    Pesanan diterima
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('customer.orders.show', $order['code']) }}" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                <div class="grid gap-4 xl:grid-cols-3">
                    @foreach ($statuses as $status)
                        <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                            <p class="inline-flex rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">{{ $status['label'] }}</p>
                            <p class="mt-4 text-sm leading-6 text-stone-600">{{ $status['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layouts.customer>
