<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Dashboard">
    @php
        $d = $dashboard ?? [];
        $chart = $d['omzet_chart'] ?? [];
        $maxOmzet = collect($chart)->max('value') ?: 1;
        $statuses = $d['status_distribusi'] ?? [];
        $statusTotal = collect($statuses)->sum('count') ?: 0;
        $produk = $d['produk_terlaris'] ?? [];
        $orders = $d['orders_terbaru'] ?? [];

        // Bangun stop conic-gradient untuk donut status.
        $acc = 0;
        $stops = [];
        foreach ($statuses as $s) {
            $from = $statusTotal > 0 ? round($acc / $statusTotal * 100, 2) : 0;
            $acc += $s['count'];
            $to = $statusTotal > 0 ? round($acc / $statusTotal * 100, 2) : 0;
            $stops[] = $s['color'].' '.$from.'% '.$to.'%';
        }
        $donut = $stops ? 'conic-gradient('.implode(', ', $stops).')' : 'conic-gradient(#e7e5e4 0% 100%)';
    @endphp

    <section class="space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Dashboard</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Ringkasan bisnis Akar Tani Kimia</h1>
            <p class="mt-2 text-sm text-stone-600">Omzet, pesanan, dan produk terlaris — data langsung dari transaksi.</p>
        </div>

        {{-- Kartu metrik --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">💰</span>
                    <p class="text-sm text-stone-500">Omzet Bulan Ini</p>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-950">{{ $d['omzet_bulan_ini_label'] ?? 'Rp0' }}</p>
                <p class="mt-2 text-sm text-emerald-700">{{ $d['pesanan_lunas'] ?? 0 }} pesanan lunas bulan ini</p>
            </article>

            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">🧾</span>
                    <p class="text-sm text-stone-500">Pesanan Bulan Ini</p>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-950">{{ $d['pesanan_bulan_ini'] ?? 0 }}</p>
                <p class="mt-2 text-sm text-stone-600">{{ $d['unit_terjual_bulan_ini'] ?? 0 }} unit produk terjual</p>
            </article>

            <a href="{{ route('admin.orders.index') }}" class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:border-amber-300 hover:shadow">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">⏳</span>
                    <p class="text-sm text-stone-500">Menunggu Pembayaran</p>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-950">{{ $d['menunggu_pembayaran'] ?? 0 }}</p>
                <p class="mt-2 text-sm text-amber-700">Perlu validasi pembayaran</p>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700">📦</span>
                    <p class="text-sm text-stone-500">Perlu Diproses</p>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-950">{{ $d['perlu_diproses'] ?? 0 }}</p>
                <p class="mt-2 text-sm text-stone-600">
                    Siap dijadwalkan pickup
                    @if (($d['perlu_pembatalan'] ?? 0) > 0) · {{ $d['perlu_pembatalan'] }} minta batal @endif
                </p>
            </a>
        </div>

        {{-- Grafik omzet + donut status --}}
        <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-950">Grafik Omzet</h2>
                        <p class="mt-1 text-sm text-stone-500">6 bulan terakhir</p>
                    </div>
                </div>

                <div class="mt-6 flex h-56 items-end gap-3">
                    @forelse ($chart as $bar)
                        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <span class="text-xs font-semibold text-stone-500 opacity-0 transition group-hover:opacity-100">{{ $bar['value_label'] }}</span>
                            <div class="flex w-full items-end" style="height: 100%;">
                                <div class="w-full rounded-t-lg bg-emerald-500/80 transition hover:bg-emerald-600"
                                     style="height: {{ max(2, round(($bar['value'] / $maxOmzet) * 100)) }}%;"
                                     title="{{ $bar['label'] }}: {{ $bar['value_label'] }}"></div>
                            </div>
                            <span class="text-xs text-stone-500">{{ $bar['label'] }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">Belum ada data omzet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-stone-950">Status Pesanan</h2>
                <p class="mt-1 text-sm text-stone-500">Distribusi seluruh pesanan</p>

                <div class="mt-5 flex items-center justify-center">
                    <div class="relative h-40 w-40 rounded-full" style="background: {{ $donut }};">
                        <div class="absolute inset-[22%] flex flex-col items-center justify-center rounded-full bg-white">
                            <span class="text-2xl font-semibold text-stone-900">{{ $statusTotal }}</span>
                            <span class="text-xs text-stone-500">pesanan</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5 space-y-2">
                    @forelse ($statuses as $s)
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-stone-600">
                                <span class="inline-block h-3 w-3 rounded-full" style="background: {{ $s['color'] }};"></span>
                                {{ $s['label'] }}
                            </span>
                            <span class="font-semibold text-stone-900">{{ $s['count'] }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">Belum ada pesanan.</p>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Produk terlaris + order terbaru --}}
        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-stone-950">Produk Terlaris</h2>
                <p class="mt-1 text-sm text-stone-500">Berdasarkan pesanan yang sudah dibayar</p>

                <div class="mt-5 space-y-3">
                    @forelse ($produk as $i => $p)
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs font-semibold text-white">{{ $i + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-stone-900">{{ $p['name'] }}</p>
                                <p class="text-xs text-stone-500">{{ $p['qty'] }} terjual · {{ $p['omzet_label'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-stone-500">Belum ada produk terjual.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-stone-950">Order Terbaru</h2>
                    <a href="{{ route('admin.orders.index') }}" class="rounded-full border border-stone-300 px-4 py-1.5 text-sm font-medium text-stone-700">Lihat semua</a>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse ($orders as $order)
                        <a href="{{ route('admin.orders.show', $order['code']) }}" class="flex items-center justify-between gap-3 rounded-2xl border border-stone-200 px-4 py-3 hover:bg-stone-50">
                            <div class="min-w-0">
                                <p class="font-semibold text-stone-900">{{ $order['code'] }}</p>
                                <p class="truncate text-xs text-stone-500">{{ $order['customer'] }} · {{ $order['amount'] }}</p>
                            </div>
                            <span class="shrink-0 rounded-full bg-stone-100 px-2.5 py-1 text-xs font-semibold text-stone-700">{{ $order['status_label'] }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-stone-500">Belum ada pesanan.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
</x-layouts.admin>
