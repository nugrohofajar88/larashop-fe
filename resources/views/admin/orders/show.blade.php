<x-layouts.admin :title="'Admin Larashop | ' . $order['code']">
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
        @endphp
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Order Detail</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">{{ $order['code'] }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Review pembayaran, detail item, alamat kirim, dan proses pengiriman dari satu halaman kerja.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
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
                @elseif (in_array($order['status'], ['paid', 'processing'], true))
                    <form method="POST" action="{{ route('admin.orders.process-shipment', $order['code']) }}">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                            Process Shipment
                        </button>
                    </form>
                @endif
                @if (in_array($order['status'], ['pending_payment', 'paid', 'processing'], true))
                    <form method="POST" action="{{ route('admin.orders.cancel', $order['code']) }}">
                        @csrf
                        <button type="submit" class="rounded-2xl bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700">
                            Batalkan order
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-stone-500">Customer</p>
                            <p class="mt-1 text-xl font-semibold text-stone-950">{{ $order['customer'] }}</p>
                            <p class="mt-1 text-sm text-stone-500">{{ $order['phone'] }}</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses }}">{{ $order['status_label'] ?? $order['status'] }}</span>
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
                            <p class="text-stone-500">Alamat kirim</p>
                            <p class="mt-1 leading-6 text-stone-700">{{ $order['address'] }}</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </section>
</x-layouts.admin>
