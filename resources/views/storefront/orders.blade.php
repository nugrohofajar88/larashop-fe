<x-layouts.customer title="Sobat Akar Tani Kimia | Pesanan">
    <div class="flex flex-col gap-10 md:flex-row">
        <x-customer.account-nav />

        <section class="flex-1 space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <h1 class="font-headline-lg text-headline-lg text-on-surface">Riwayat Pesanan</h1>
                <a href="{{ route('checkout') }}" class="self-start rounded-full bg-primary px-5 py-3 font-body-md font-bold text-on-primary transition hover:bg-secondary">Lanjut checkout</a>
            </div>

            {{-- Status tabs --}}
            <div class="overflow-x-auto">
                <div class="inline-flex min-w-full gap-2 rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-2 soft-warm-shadow">
                    @foreach ($orderTabs as $tab)
                        <a href="{{ route('customer.orders', $tab['key'] === 'all' ? [] : ['status' => $tab['key']]) }}"
                            class="whitespace-nowrap rounded-2xl px-4 py-2.5 font-body-sm text-sm font-semibold transition {{ $activeOrderStatus === $tab['key'] ? 'bg-on-background text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                            {{ $tab['label'] }} ({{ $tab['count'] }})
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($orders === [])
                <div class="rounded-3xl border border-dashed border-surface-container-highest bg-surface-container-lowest px-5 py-14 text-center soft-warm-shadow">
                    <span class="material-symbols-outlined mb-3 text-5xl text-outline-variant">receipt_long</span>
                    <p class="font-body-md text-base font-semibold text-on-surface">Belum ada pesanan di tab ini.</p>
                    <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">Coba pindah tab lain atau lanjut belanja untuk membuat pesanan baru.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        @php
                            $status = $order['status'] ?? '';
                            $badgeClasses = match ($status) {
                                'pending_payment' => 'bg-amber-100 text-amber-800',
                                'paid', 'processing' => 'bg-secondary-container text-on-secondary-container',
                                'shipped', 'completed' => 'bg-sky-100 text-sky-800',
                                'cancelled' => 'bg-error-container text-on-error-container',
                                default => 'bg-surface-container text-on-surface-variant',
                            };
                        @endphp
                        <article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-body-md font-semibold text-on-surface">{{ $order['code'] }}</p>
                                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">{{ $order['date'] }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">{{ $order['status_label'] ?? $order['status'] }}</span>
                            </div>
                            <div class="mt-4 flex flex-wrap items-end justify-between gap-4 border-t border-surface-container-highest pt-4">
                                <div>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Total pembayaran</p>
                                    <p class="mt-1 font-headline-md text-lg font-bold {{ $status === 'cancelled' ? 'text-error' : 'text-primary' }}">{{ $order['total'] }}</p>
                                </div>
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    @if (! empty($order['cancel_requested']))
                                        <span class="inline-flex items-center rounded-full bg-surface-container-low px-4 py-2 font-body-sm text-sm font-medium text-on-surface-variant">⏳ Menunggu konfirmasi batal</span>
                                    @elseif (! empty($order['can_cancel']))
                                        @php $isPending = ($order['status'] ?? '') === 'pending_payment'; @endphp
                                        <form action="{{ route('customer.orders.cancel', $order['code']) }}" method="POST"
                                              data-confirm="{{ $isPending ? 'Yakin batalkan pesanan ini?' : 'Ajukan pembatalan pesanan ini? Pesanan sudah dibayar sehingga perlu ditinjau admin dulu.' }}"
                                              data-confirm-title="{{ $isPending ? 'Batalkan pesanan' : 'Ajukan pembatalan' }}"
                                              data-confirm-ok="{{ $isPending ? 'Ya, batalkan' : 'Ya, ajukan' }}">
                                            @csrf
                                            <button type="submit" class="rounded-full border border-error-container px-4 py-2 font-body-sm text-sm font-semibold text-error">{{ $isPending ? 'Batalkan' : 'Ajukan batal' }}</button>
                                        </form>
                                    @endif
                                    @if (($order['status'] ?? null) === 'shipped')
                                        <form action="{{ route('customer.orders.complete', $order['code']) }}" method="POST" data-confirm="Tandai pesanan ini sudah diterima?" data-confirm-title="Konfirmasi penerimaan" data-confirm-ok="Ya, sudah diterima">
                                            @csrf
                                            <button type="submit" class="rounded-full border border-secondary-container px-4 py-2 font-body-sm text-sm font-semibold text-primary">Pesanan diterima</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('customer.orders.show', $order['code']) }}" class="rounded-full bg-on-background px-4 py-2 font-body-sm text-sm font-semibold text-on-primary transition hover:bg-primary">Detail</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts.customer>
