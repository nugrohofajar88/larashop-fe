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

        <section id="daftar-order" class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm scroll-mt-4">
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

            {{-- Export CSV data penjualan (marketplace/order_no/resi/sku/nama_produk/qty) - semua
                 order kecuali dibatalkan (order yang belum punya AWB tetap ikut, resi dikosongkan).
                 Rentang tanggal opsional (berdasarkan tanggal order dibuat), terpisah dari filter
                 status/search di atas supaya bisa export lintas status/halaman sekaligus. --}}
            <form method="GET" action="{{ route('admin.orders.export') }}" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-stone-50/60 p-4">
                <div>
                    <label class="block text-xs font-medium text-stone-600">Dari tanggal</label>
                    <input type="date" name="date_from" class="mt-1 rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600">Sampai tanggal</label>
                    <input type="date" name="date_to" class="mt-1 rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm">
                </div>
                <button type="submit" class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                    Export CSV Penjualan
                </button>
                <p class="basis-full text-xs text-stone-500">Kosongkan tanggal untuk export semua order (kecuali yang dibatalkan). Order yang belum punya resi/AWB tetap ikut, kolom resi dikosongkan.</p>
            </form>

            <div class="mt-5 overflow-x-auto">
                <div class="inline-flex min-w-full gap-2 rounded-[1.5rem] border border-stone-200 bg-stone-50/80 p-2">
                    @foreach ($statusTabs as $tab)
                        <a
                            href="{{ route('admin.orders.index', array_filter(['status' => $tab['key'] === 'all' ? null : $tab['key'], 'search' => $search !== '' ? $search : null])) }}#daftar-order"
                            class="whitespace-nowrap rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ $activeStatus === $tab['key'] ? 'bg-stone-900 text-white shadow-sm' : 'text-stone-600 hover:bg-white hover:text-stone-900' }}"
                        >
                            {{ $tab['label'] }} ({{ $tab['count'] }})
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Bar pickup massal = FORM MANDIRI. Checkbox di tabel pakai atribut
                 form="bulkPickupForm" supaya tidak bersarang dengan form aksi per-baris
                 (_row-actions berisi <form> sendiri). Muncul saat ada yang dicentang. --}}
            <form id="bulkPickupForm" method="POST" action="{{ route('admin.orders.schedule-pickup-bulk') }}"
                  data-bulk-pickup class="mt-5 hidden rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                @csrf
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-medium text-stone-600">Tanggal pickup</label>
                        <input type="date" name="pickup_date" data-pickup-date value="{{ now()->setTimezone('Asia/Jakarta')->addDay()->format('Y-m-d') }}" min="{{ now()->setTimezone('Asia/Jakarta')->format('Y-m-d') }}" class="mt-1 rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-stone-600">Jam</label>
                        <input type="time" name="pickup_time" data-pickup-time value="10:00" class="mt-1 rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-stone-600">Kendaraan</label>
                        <select name="pickup_vehicle" class="mt-1 rounded-xl border border-stone-200 bg-white px-3 py-2 text-sm">
                            <option value="Motor">Motor</option>
                            <option value="Mobil">Mobil</option>
                            <option value="Truk">Truk</option>
                        </select>
                    </div>
                    <button type="submit" data-bulk-pickup-btn class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                        Jadwalkan Pickup (<span data-count-paid>0</span>)
                    </button>
                    {{-- Disembunyikan sementara --}}
                    {{-- <button type="submit" formaction="{{ route('admin.orders.mark-shipped-bulk') }}" data-bulk-ship-btn class="rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-40">
                        Tandai Dikirim (<span data-count-processing>0</span>)
                    </button> --}}
                    <button type="submit" formaction="{{ route('admin.orders.print-labels-bulk') }}" formtarget="_blank" data-bulk-label-btn class="rounded-xl bg-stone-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-stone-800 disabled:cursor-not-allowed disabled:opacity-40">
                        Cetak Label (<span data-count-awb>0</span>)
                    </button>
                </div>
                <p class="mt-2 text-xs text-stone-500">Centang order <b>paid</b> (sudah ter-booking) → <b>Jadwalkan Pickup</b>; order yang sudah punya AWB → <b>Cetak Label</b> (gabung jadi 1 PDF, <b>maks 20</b> per cetak).</p>
                <p data-label-warn class="mt-1 hidden text-xs font-semibold text-amber-700">Cetak Label dibatasi maksimal 20 order sekali jalan. Kurangi pilihan order ber-AWB jadi 20 atau kurang.</p>
            </form>

            {{-- Desktop: tabel. Wrapper overflow-visible (bukan overflow-x-auto) supaya dropdown
                 aksi ⋮ tidak terpotong, terutama di baris paling atas. --}}
            <div class="mt-5 hidden rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="rounded-tl-2xl px-3 py-3"><input type="checkbox" data-bulk-all class="h-4 w-4 rounded border-stone-300 text-emerald-600"></th>
                            <th class="px-4 py-3 font-medium">Order</th>
                            <th class="px-4 py-3 font-medium">Customer</th>
                            <th class="px-4 py-3 font-medium">Nominal</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Shipment</th>
                            <th class="px-4 py-3 font-medium">Cetak</th>
                            <th class="rounded-tr-2xl px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-3 py-4 align-top">
                                    @if (in_array($order['status'] ?? '', ['paid', 'processing'], true) || ! empty($order['awb']))
                                        <input type="checkbox" name="order_codes[]" value="{{ $order['code'] }}" form="bulkPickupForm" data-bulk-item data-status="{{ $order['status'] ?? '' }}" data-awb="{{ ! empty($order['awb']) ? '1' : '0' }}" class="h-4 w-4 rounded border-stone-300 text-emerald-600">
                                    @endif
                                </td>
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
                                    <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">{{ $statusLabels[$order['status'] ?? ''] ?? ($order['status_label'] ?? $order['status']) }}</span>
                                    @if (! empty($order['cancel_requested']))
                                        <span class="ml-1 inline-block rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Minta batal</span>
                                    @endif
                                    <p class="mt-2 text-xs text-stone-500">{{ $order['payment_status'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top text-stone-600">
                                    <p>{{ $order['shipping_service'] }}</p>
                                    <p class="mt-1 text-xs text-stone-500">{{ $order['awb'] ?? 'Belum ada AWB' }}</p>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <span title="{{ $order['printed_label'] ?? '' }}" class="rounded-full px-3 py-1 text-xs font-semibold {{ ! empty($order['is_printed']) ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500' }}">{{ ! empty($order['is_printed']) ? 'Ya' : 'Tidak' }}</span>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    @include('admin.orders._row-actions')
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada order yang cocok dengan filter saat ini.</td>
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
                            <div class="flex min-w-0 items-start gap-2">
                                @if (in_array($order['status'] ?? '', ['paid', 'processing'], true) || ! empty($order['awb']))
                                    <input type="checkbox" name="order_codes[]" value="{{ $order['code'] }}" form="bulkPickupForm" data-bulk-item data-status="{{ $order['status'] ?? '' }}" data-awb="{{ ! empty($order['awb']) ? '1' : '0' }}" class="mt-1 h-4 w-4 rounded border-stone-300 text-emerald-600">
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('admin.orders.show', $order['code']) }}" class="font-semibold text-stone-900">{{ $order['code'] }}</a>
                                    <p class="mt-0.5 text-xs text-stone-500">{{ $order['date'] }}</p>
                                </div>
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
                                <dd class="text-right"><span class="rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700">{{ $statusLabels[$order['status'] ?? ''] ?? ($order['status_label'] ?? $order['status']) }}</span>@if (! empty($order['cancel_requested']))<span class="ml-1 inline-block rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Minta batal</span>@endif<span class="mt-1 block text-xs text-stone-500">{{ $order['payment_status'] }}</span></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Shipment</dt>
                                <dd class="text-right text-stone-700">{{ $order['shipping_service'] }}<span class="block text-xs text-stone-500">{{ $order['awb'] ?? 'Belum ada AWB' }}</span></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Cetak Label</dt>
                                <dd class="text-right"><span title="{{ $order['printed_label'] ?? '' }}" class="rounded-full px-2.5 py-1 text-xs font-semibold {{ ! empty($order['is_printed']) ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-500' }}">{{ ! empty($order['is_printed']) ? 'Ya' : 'Tidak' }}</span></dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada order yang cocok dengan filter saat ini.</p>
                @endforelse
            </div>

            {{-- Paging --}}
            @if (($pagination['last_page'] ?? 1) > 1)
                @php
                    $baseParams = array_filter(['status' => $activeStatus !== 'all' ? $activeStatus : null, 'search' => $search !== '' ? $search : null]);
                    $curPage = $pagination['current_page'];
                    $lastPage = $pagination['last_page'];
                @endphp
                <div class="mt-5 flex flex-col items-center justify-between gap-3 border-t border-stone-100 pt-5 sm:flex-row">
                    <p class="text-sm text-stone-500">
                        Halaman {{ $curPage }} dari {{ $lastPage }} &middot; total {{ $pagination['total'] }} order
                    </p>
                    <div class="flex items-center gap-2">
                        @if ($curPage > 1)
                            <a href="{{ route('admin.orders.index', [...$baseParams, 'page' => $curPage - 1]) }}#daftar-order"
                                class="rounded-xl border border-stone-200 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">&larr; Sebelumnya</a>
                        @else
                            <span class="rounded-xl border border-stone-100 px-4 py-2 text-sm font-medium text-stone-300">&larr; Sebelumnya</span>
                        @endif
                        @if ($curPage < $lastPage)
                            <a href="{{ route('admin.orders.index', [...$baseParams, 'page' => $curPage + 1]) }}#daftar-order"
                                class="rounded-xl border border-stone-200 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Selanjutnya &rarr;</a>
                        @else
                            <span class="rounded-xl border border-stone-100 px-4 py-2 text-sm font-medium text-stone-300">Selanjutnya &rarr;</span>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </section>

    {{-- Bulk pickup: hitung pilihan, tampilkan/sembunyikan bar, select-all.
         Checkbox ada di luar form (pakai atribut form=), jadi query ke document. --}}
    <script>
        (function () {
            const bar = document.querySelector('[data-bulk-pickup]');
            if (!bar) return;
            const cPaid = bar.querySelector('[data-count-paid]');
            const cProc = bar.querySelector('[data-count-processing]');
            const cAwb = bar.querySelector('[data-count-awb]');
            const btnPickup = bar.querySelector('[data-bulk-pickup-btn]');
            const btnShip = bar.querySelector('[data-bulk-ship-btn]');
            const btnLabel = bar.querySelector('[data-bulk-label-btn]');
            const warn = bar.querySelector('[data-label-warn]');
            const all = document.querySelector('[data-bulk-all]');
            // Tiap order punya 2 checkbox (layout desktop + mobile); salah satu disembunyikan
            // via CSS. Hanya ambil yang TERLIHAT (offsetParent != null) supaya tak dobel.
            const items = () => Array.from(document.querySelectorAll('[data-bulk-item]')).filter(c => c.offsetParent !== null);
            function refresh() {
                const checked = items().filter(c => c.checked);
                const nPaid = checked.filter(c => c.dataset.status === 'paid').length;
                const nProc = checked.filter(c => c.dataset.status === 'processing').length;
                const nAwb = checked.filter(c => c.dataset.awb === '1').length;
                if (cPaid) cPaid.textContent = nPaid;
                if (cProc) cProc.textContent = nProc;
                if (cAwb) cAwb.textContent = nAwb;
                if (btnPickup) btnPickup.disabled = nPaid === 0;
                if (btnShip) btnShip.disabled = nProc === 0;
                // Satu call gabungan ke Komerce (order_no dipisah koma) → batas wajar 20 per cetak.
                const LABEL_MAX = 20;
                btnLabel.disabled = nAwb === 0 || nAwb > LABEL_MAX;
                btnLabel.title = nAwb > LABEL_MAX ? ('Maksimal ' + LABEL_MAX + ' label per cetak (kamu pilih ' + nAwb + ')') : '';
                if (warn) warn.classList.toggle('hidden', nAwb <= LABEL_MAX);
                bar.classList.toggle('hidden', checked.length === 0);
                if (all) all.checked = items().length > 0 && checked.length === items().length;
            }
            document.addEventListener('change', function (e) {
                if (e.target.matches('[data-bulk-all]')) {
                    items().forEach(c => { c.checked = e.target.checked; });
                }
                if (e.target.matches('[data-bulk-item]') || e.target.matches('[data-bulk-all]')) refresh();
            });
            refresh();

            // Cegah pilih jam yang sudah lewat saat tanggal = hari ini.
            const dateEl = bar.querySelector('[data-pickup-date]');
            const timeEl = bar.querySelector('[data-pickup-time]');
            if (dateEl && timeEl) {
                const enforceTime = () => {
                    const n = new Date();
                    const today = n.getFullYear() + '-' + String(n.getMonth() + 1).padStart(2, '0') + '-' + String(n.getDate()).padStart(2, '0');
                    if (dateEl.value === today) {
                        const m = String(n.getHours()).padStart(2, '0') + ':' + String(n.getMinutes()).padStart(2, '0');
                        timeEl.min = m;
                        if (timeEl.value && timeEl.value < m) timeEl.value = m;
                    } else {
                        timeEl.removeAttribute('min');
                    }
                };
                dateEl.addEventListener('change', enforceTime);
                enforceTime();
            }
        })();
    </script>

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
