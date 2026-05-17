<x-layouts.customer :title="'Larashop | ' . $order['code']">
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
        <div class="space-y-3">
            <a href="{{ route('customer.orders') }}" class="inline-flex items-center text-sm font-medium text-emerald-700">
                Kembali ke daftar pesanan
            </a>
            <x-customer-section-title
                eyebrow="Detail Order"
                :title="$order['code']"
                description="Halaman ini membantu customer melihat status, ringkasan item, pengiriman, dan total pembayaran dalam satu layar."
            />
        </div>

        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)] lg:items-start">
            <x-customer.account-nav />

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
                <div class="space-y-4">
                    <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm text-stone-500">Tanggal order</p>
                                <p class="mt-1 font-semibold text-stone-950">{{ $order['date'] }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses }}">{{ $order['status_label'] ?? $order['status'] }}</span>
                        </div>

                        @if (($order['status'] ?? null) === 'pending_payment')
                            <form action="{{ route('customer.orders.cancel', $order['code']) }}" method="POST" class="mt-4" onsubmit="return confirm('Batalkan pesanan ini?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600">
                                    Batalkan pesanan
                                </button>
                            </form>
                        @endif

                        @if (($order['status'] ?? null) === 'shipped')
                            <form action="{{ route('customer.orders.complete', $order['code']) }}" method="POST" class="mt-4" onsubmit="return confirm('Tandai pesanan ini sudah diterima?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-emerald-200 px-4 py-2.5 text-sm font-semibold text-emerald-700">
                                    Pesanan diterima
                                </button>
                            </form>
                        @endif

                        @if (($order['status'] ?? null) === 'cancelled')
                            <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700">
                                Pesanan ini sudah dibatalkan. Jika perlu order ulang, customer bisa kembali ke katalog atau checkout ulang dari keranjang.
                            </div>
                        @endif

                        @if (($order['status'] ?? null) === 'completed')
                            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-700">
                                Pesanan ini sudah selesai karena customer menandainya sudah diterima.
                            </div>
                        @endif

                        <div class="mt-5 space-y-3">
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
                    </article>

                    <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-stone-950">Item pesanan</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($order['items'] as $item)
                                <div class="flex items-center justify-between gap-4 rounded-2xl bg-stone-50 px-4 py-4 text-sm">
                                    <div>
                                        <p class="font-semibold text-stone-900">{{ $item['name'] }}</p>
                                        @if (! empty($item['variant']))
                                            <p class="mt-1 text-sm text-stone-500">Varian {{ $item['variant'] }}</p>
                                        @endif
                                        <p class="text-stone-500">Qty {{ $item['qty'] }}</p>
                                    </div>
                                    <p class="font-semibold text-stone-900">{{ $item['subtotal'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>

                <aside class="space-y-4">
                    <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-stone-950">Pengiriman</h2>
                        <div class="mt-4 space-y-3 text-sm text-stone-600">
                            <div class="rounded-2xl bg-stone-50 px-4 py-4">
                                <p class="text-stone-500">Layanan</p>
                                <p class="mt-1 font-semibold text-stone-900">{{ $order['shipping']['service'] }}</p>
                                <p class="mt-1 text-stone-500">Estimasi {{ $order['shipping']['estimate'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-stone-50 px-4 py-4">
                                <p class="text-stone-500">Alamat kirim</p>
                                <p class="mt-1 leading-6 text-stone-700">{{ $order['shipping']['address'] }}</p>
                                @if (! empty($order['shipping']['awb']))
                                    <p class="mt-2 text-xs text-stone-500">AWB: {{ $order['shipping']['awb'] }}</p>
                                @endif
                            </div>
                        </div>
                    </article>

                    <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                        <h2 class="text-lg font-semibold text-stone-950">Pembayaran</h2>
                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Total produk</span>
                                <span class="font-semibold text-stone-900">{{ $order['payment']['items_total'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Ongkir</span>
                                <span class="font-semibold text-stone-900">{{ $order['payment']['shipping_total'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-stone-500">Kode unik</span>
                                <span class="font-semibold text-stone-900">{{ $order['payment']['unique_code'] }}</span>
                            </div>
                            @if (($order['payment']['used_unique_code'] ?? 'Rp0') !== 'Rp0')
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-500">Potongan saldo kode unik</span>
                                    <span class="font-semibold text-emerald-700">-{{ $order['payment']['used_unique_code'] }}</span>
                                </div>
                            @endif
                            <div class="border-t border-dashed border-stone-200 pt-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-stone-600">Total transfer</span>
                                    <span class="text-lg font-semibold text-emerald-700">{{ $order['payment']['grand_total'] }}</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="mt-5 inline-flex w-full items-center justify-center rounded-2xl bg-stone-900 px-5 py-3.5 text-sm font-semibold text-white">
                            Konfirmasi via WhatsApp
                        </button>
                    </article>
                </aside>
            </div>
        </div>
    </section>
</x-layouts.customer>
