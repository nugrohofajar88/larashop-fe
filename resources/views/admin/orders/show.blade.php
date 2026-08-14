<x-layouts.admin :title="'Admin Sobat Akar Tani Kimia | ' . $order['code']">
    <section class="space-y-6">
        @php
            $status = $order['status'] ?? '';
            $statusBadgeClasses = match ($status) {
                'pending_payment' => 'bg-amber-100 text-amber-800',
                'paid', 'processing' => 'bg-emerald-100 text-emerald-800',
                'shipped', 'completed' => 'bg-sky-100 text-sky-800',
                'cancelled' => 'bg-rose-100 text-rose-700',
                default => 'bg-stone-100 text-stone-700',
            };
            // Label berorientasi tindakan, sama seperti di halaman daftar pesanan
            // admin - biar admin tahu apa yang perlu dilakukan, bukan cuma nama
            // status teknis. Khusus admin - JANGAN dipakai di halaman customer.
            $statusLabel = match ($status) {
                'pending_payment' => 'Menunggu Pembayaran',
                'paid' => 'Perlu Dikirim',
                'processing' => 'Menunggu Penjemputan',
                'shipped' => 'Dalam Pengiriman',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
                default => $order['status_label'] ?? $status,
            };
        @endphp
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Order Detail</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">{{ $order['code'] }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Review pembayaran, detail item, alamat kirim, dan proses pengiriman dari satu halaman kerja.
                </p>
            </div>

            <div class="flex flex-wrap items-start gap-3">
                <a href="{{ route('admin.orders.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke orders
                </a>
                @if ($order['status'] === 'pending_payment')
                    <form method="POST" action="{{ route('admin.orders.validate-payment', $order['code']) }}">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">
                            Validasi pembayaran
                        </button>
                    </form>
                @elseif ($order['status'] === 'processing')
                    <form method="POST" action="{{ route('admin.orders.process-shipment', $order['code']) }}">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                            Tandai Dikirim
                        </button>
                    </form>
                @endif
                @if (! empty($order['awb']))
                    <a href="{{ route('admin.orders.label', $order['code']) }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white hover:bg-sky-700">
                        🖨️ Cetak Label
                    </a>
                    {{-- Packing list internal (isi paket per baris, bukan pengganti label resmi -
                         label DIY ini TIDAK punya kode sortir kurir, jangan ditempel ke paket). --}}
                    <a href="{{ route('admin.orders.label-diy', $order['code']) }}" target="_blank" rel="noopener"
                       title="Packing list internal (bukan pengganti label resmi - tidak ada kode sortir kurir)"
                       class="inline-flex items-center gap-2 rounded-2xl border border-sky-600 px-5 py-3 text-sm font-semibold text-sky-700 hover:bg-sky-50">
                        📋 Packing List
                    </a>
                @endif
                {{-- Saat ada pengajuan pembatalan, tombol Setujui/Tolak pindah ke banner di bawah
                     supaya deretan aksi atas tidak berantakan. --}}
                @if (in_array($order['status'], ['pending_payment', 'paid', 'processing'], true) && empty($order['cancel_requested']))
                    <form method="POST" action="{{ route('admin.orders.cancel', $order['code']) }}" data-cancel-reason-form>
                        @csrf
                        <button type="submit" class="rounded-2xl bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">
                            Batalkan order
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (! empty($order['cancel_requested']))
            <div class="rounded-2xl border border-amber-300 bg-amber-50 px-5 py-4">
                <p class="text-sm text-amber-900">
                    ⚠️ <b>Customer mengajukan pembatalan</b>@if (! empty($order['cancel_requested_at'])) pada {{ $order['cancel_requested_at'] }}@endif.
                    Setujui untuk membatalkan order (stok dikembalikan), atau tolak agar order tetap berjalan.
                </p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('admin.orders.cancel', $order['code']) }}" data-cancel-reason-form>
                        @csrf
                        <button type="submit" class="rounded-2xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">
                            Setujui pembatalan
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.reject-cancellation', $order['code']) }}" data-confirm="Tolak permintaan pembatalan? Order tetap berjalan." data-confirm-title="Tolak pembatalan" data-confirm-ok="Ya, tolak">
                        @csrf
                        <button type="submit" class="rounded-2xl border border-amber-300 bg-white px-5 py-2.5 text-sm font-semibold text-amber-900 hover:bg-amber-100">
                            Tolak pembatalan
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-stone-500">Customer</p>
                            <p class="mt-1 text-xl font-semibold text-stone-950">{{ $order['customer'] }}</p>
                            @php
                                $pemesanPhone = ($order['customer_phone'] ?? '') ?: ($order['phone'] ?? '');
                                $waDigits = preg_replace('/\D/', '', $pemesanPhone);
                                $waDigits = \Illuminate\Support\Str::startsWith($waDigits, '0') ? '62'.substr($waDigits, 1) : $waDigits;
                            @endphp
                            <p class="mt-1 text-sm text-stone-500">
                                No. HP:
                                @if ($pemesanPhone !== '')
                                    <a href="https://wa.me/{{ $waDigits }}" target="_blank" rel="noopener" class="font-medium text-emerald-700 hover:underline">{{ $pemesanPhone }}</a>
                                @else
                                    <span>-</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses }}">{{ $statusLabel }}</span>
                            <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">{{ $order['payment_status'] }}</span>
                        </div>
                    </div>

                    @if (($order['status'] ?? null) === 'cancelled')
                        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700">
                            Order ini sudah dibatalkan. Ledger kode unik dan proses turunannya seharusnya sudah dikembalikan atau dihentikan.
                        </div>
                    @endif

                    @if (($order['status'] ?? null) === 'completed')
                        <div class="mt-4 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-4 text-sm text-sky-700">
                            Order ini sudah selesai karena customer menandai pesanan sudah diterima.
                        </div>
                    @endif

                    <div class="mt-6 space-y-3">
                        @foreach ($timeline as $step)
                            @php
                                $isCancelledStep = ($step['tone'] ?? null) === 'cancelled';
                                $stepWrapperClasses = $step['active']
                                    ? ($isCancelledStep ? 'border border-rose-200 bg-rose-50/70' : 'border border-emerald-200 bg-emerald-50/60')
                                    : 'bg-stone-50';
                                $stepTitleClasses = $step['active']
                                    ? ($isCancelledStep ? 'text-rose-800' : 'text-emerald-800')
                                    : 'text-stone-900';
                                $stepNoteClasses = $step['active']
                                    ? ($isCancelledStep ? 'text-rose-700' : 'text-emerald-700')
                                    : 'text-stone-500';
                            @endphp
                            <div class="rounded-2xl {{ $stepWrapperClasses }} px-4 py-4">
                                <p class="text-sm font-semibold {{ $stepTitleClasses }}">{{ $step['label'] }}</p>
                                <p class="mt-1 text-sm {{ $stepNoteClasses }}">{{ $step['note'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Item pesanan</h2>
                    <div class="mt-5 space-y-3">
                        @foreach ($order['items'] as $item)
                            <div class="flex items-center justify-between gap-4 rounded-2xl bg-stone-50 px-4 py-4 text-sm">
                                <div>
                                    <p class="font-semibold text-stone-900">{{ $item['name'] }}</p>
                                    <p class="text-stone-500">Qty {{ $item['qty'] }}</p>
                                </div>
                                <p class="font-semibold text-stone-900">{{ $item['subtotal'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Pembayaran</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-stone-500">Metode</p>
                            <p class="mt-1 font-semibold text-stone-900">{{ $order['payment']['method'] }}</p>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Total produk</span>
                            <span class="font-semibold text-stone-900">{{ $order['payment']['items_total'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Ongkir</span>
                            <span class="font-semibold text-stone-900">{{ $order['payment']['shipping_total'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Kode unik</span>
                            <span class="font-semibold text-stone-900">{{ $order['payment']['unique_code'] }}</span>
                        </div>
                        @if (($order['payment']['used_unique_code'] ?? 'Rp0') !== 'Rp0')
                            <div class="flex items-center justify-between rounded-2xl bg-emerald-50 px-4 py-4">
                                <span class="text-emerald-700">Potongan saldo kode unik</span>
                                <span class="font-semibold text-emerald-700">-{{ $order['payment']['used_unique_code'] }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between rounded-2xl bg-stone-900 px-4 py-4 text-white">
                            <span>Total transfer</span>
                            <span class="text-lg font-semibold">{{ $order['payment']['grand_total'] }}</span>
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Pengiriman</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-stone-500">Layanan</p>
                            <p class="mt-1 font-semibold text-stone-900">{{ $order['shipping_service'] }}</p>
                            <p class="mt-1 text-stone-500">Estimasi {{ $order['shipping_estimate'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-stone-500">Nomor resi / AWB</p>
                            <p class="mt-1 font-semibold text-stone-900">{{ $order['awb'] ?? 'Belum tersedia' }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-stone-500">Order ID Komerce</p>
                            <p class="mt-1 font-mono text-sm font-semibold text-stone-900">{{ $order['shipping']['komerce_order_no'] ?? 'Belum di-booking' }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-stone-500">Status Cetak Label</p>
                            <p class="mt-1 font-semibold {{ ! empty($order['is_printed']) ? 'text-emerald-700' : 'text-stone-900' }}">{{ $order['printed_label'] ?? 'Belum dicetak' }}</p>
                        </div>
                        @if (! empty($order['shipping']['note']))
                            <div class="rounded-2xl bg-amber-50 px-4 py-4">
                                <p class="text-amber-700">Catatan pengiriman</p>
                                <p class="mt-1 leading-6 text-amber-900">{{ $order['shipping']['note'] }}</p>
                            </div>
                        @endif
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-stone-500">Alamat kirim</p>
                            <p class="mt-1 leading-6 text-stone-700">{{ $order['address'] }}</p>
                        </div>
                    </div>

                    @if (in_array($order['status'], ['paid', 'processing'], true) && empty($order['shipping']['komerce_order_no']))
                        <div class="mt-5 border-t border-stone-200 pt-5">
                            <p class="text-sm font-semibold text-stone-900">Order belum di-booking ke ekspedisi</p>
                            <p class="mt-1 text-sm text-stone-500">Jadwalkan pickup baru bisa dipakai setelah booking berhasil. Coba booking ulang dulu.</p>
                            <form method="POST" action="{{ route('admin.orders.retry-booking', $order['code']) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white">Coba Booking Lagi</button>
                            </form>
                        </div>
                    @elseif ($order['status'] === 'paid')
                        <form method="POST" action="{{ route('admin.orders.schedule-pickup', $order['code']) }}" class="mt-5 border-t border-stone-200 pt-5">
                            @csrf
                            <p class="text-sm font-semibold text-stone-900">Jadwalkan pickup</p>
                            <div class="mt-3 flex flex-wrap items-end gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-stone-500">Tanggal pickup</label>
                                    <input type="date" name="pickup_date" data-pickup-date value="{{ now()->setTimezone('Asia/Jakarta')->addDay()->format('Y-m-d') }}" min="{{ now()->setTimezone('Asia/Jakarta')->format('Y-m-d') }}" required class="mt-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-800">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-stone-500">Jam</label>
                                    <input type="time" name="pickup_time" data-pickup-time value="10:00" required class="mt-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-800">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-stone-500">Kendaraan</label>
                                    <select name="pickup_vehicle" class="mt-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-800">
                                        <option value="Motor">Motor</option>
                                        <option value="Mobil">Mobil</option>
                                        <option value="Truk">Truk</option>
                                    </select>
                                </div>
                                <button type="submit" class="rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white">Jadwalkan Pickup</button>
                            </div>
                        </form>
                    @endif
                </section>

                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <h2 class="text-xl font-semibold text-stone-950">Riwayat status</h2>
                    @if (! empty($order['trackings']))
                        <ol class="mt-5 space-y-4">
                            @foreach (array_reverse($order['trackings']) as $t)
                                <li class="flex gap-3">
                                    <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $loop->first ? 'bg-emerald-600' : 'bg-stone-300' }}"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-stone-900">{{ $t['label'] }}</p>
                                        <p class="text-xs text-stone-500">{{ $t['time'] }} · {{ ucfirst($t['source']) }}@if (! empty($t['raw_status'])) · {{ $t['raw_status'] }}@endif</p>
                                        @if (! empty($t['awb']))
                                            <p class="text-xs text-stone-500">AWB: {{ $t['awb'] }}</p>
                                        @endif
                                        @if (! empty($t['note']))
                                            <p class="text-xs text-stone-500">{{ $t['note'] }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="mt-3 text-sm text-stone-500">Belum ada riwayat status.</p>
                    @endif
                </section>
            </aside>
        </div>
    </section>

    {{-- Cegah pilih jam pickup yang sudah lewat kalau tanggal = hari ini. --}}
    <script>
        (function () {
            const dateEl = document.querySelector('[data-pickup-date]');
            const timeEl = document.querySelector('[data-pickup-time]');
            if (!dateEl || !timeEl) return;
            const enforce = () => {
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
            dateEl.addEventListener('change', enforce);
            enforce();
        })();
    </script>
</x-layouts.admin>
