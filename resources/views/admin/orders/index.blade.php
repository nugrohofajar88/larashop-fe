<x-layouts.admin title="Admin Larashop | Orders">
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

            <div class="mt-5 overflow-hidden rounded-2xl border border-stone-200">
                <div>
                <table class="min-w-full text-left text-sm overflow-visible">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Order</th>
                            <th class="px-4 py-3 font-medium">Customer</th>
                            <th class="px-4 py-3 font-medium">Nominal</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Shipment</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($orders as $order)
                            <tr class="overflow-visible">
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
                                <td class="overflow-visible px-4 py-4 align-top">
                                    <details class="relative ml-auto w-fit">
                                        <summary class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 transition hover:border-stone-300 hover:text-stone-900">
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
                                                <path d="M10 4.75a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5zm0 6.5a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5zm0 6.5a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5z" />
                                            </svg>
                                        </summary>

                                        <div class="absolute bottom-full right-0 z-50 mb-2 w-52 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xl shadow-stone-900/10">
                                            <a href="{{ route('admin.orders.show', $order['code']) }}" class="block px-4 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                                                Lihat invoice
                                            </a>

                                            @if ($order['status'] === 'pending_payment')
                                                <form method="POST" action="{{ route('admin.orders.validate-payment', $order['code']) }}">
                                                    @csrf
                                                    <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                                                        Validasi pembayaran
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.orders.cancel', $order['code']) }}">
                                                    @csrf
                                                    <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-rose-700 transition hover:bg-rose-50">
                                                        Batalkan pesanan
                                                    </button>
                                                </form>
                                            @elseif ($order['status'] === 'paid')
                                                <form method="POST" action="{{ route('admin.orders.process-shipment', $order['code']) }}">
                                                    @csrf
                                                    <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                                                        Proses pesanan
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.orders.cancel', $order['code']) }}">
                                                    @csrf
                                                    <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-rose-700 transition hover:bg-rose-50">
                                                        Batalkan pesanan
                                                    </button>
                                                </form>
                                            @elseif ($order['status'] === 'processing')
                                                <form method="POST" action="{{ route('admin.orders.process-shipment', $order['code']) }}">
                                                    @csrf
                                                    <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                                                        Kirim pesanan
                                                    </button>
                                                </form>
                                            @elseif ($order['status'] === 'shipped')
                                                <form method="POST" action="{{ route('admin.orders.complete', $order['code']) }}">
                                                    @csrf
                                                    <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                                                        Tandai selesai
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </details>
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
            </div>
        </section>
    </section>
</x-layouts.admin>
