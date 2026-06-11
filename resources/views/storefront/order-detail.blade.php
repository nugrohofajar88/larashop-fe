<x-layouts.customer :title="'Sobat Akar Tani Kimia | ' . $order['code']">
    @php
        $status = $order['status'] ?? '';
        $statusBadgeClasses = match ($status) {
            'pending_payment' => 'bg-amber-100 text-amber-800',
            'paid', 'processing' => 'bg-secondary-container text-on-secondary-container',
            'shipped', 'completed' => 'bg-sky-100 text-sky-800',
            'cancelled' => 'bg-error-container text-on-error-container',
            default => 'bg-surface-container text-on-surface-variant',
        };
    @endphp

    <div class="flex flex-col gap-10 md:flex-row">
        <x-customer.account-nav />

        <section class="flex-1 space-y-6">
            <div>
                <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-1 font-body-sm text-body-sm font-medium text-primary hover:underline">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar pesanan
                </a>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <h1 class="font-headline-lg text-headline-lg text-on-surface">{{ $order['code'] }}</h1>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses }}">{{ $order['status_label'] ?? $order['status'] }}</span>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
                <div class="space-y-6">
                    {{-- Timeline --}}
                    <article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Tanggal order</p>
                                <p class="mt-1 font-body-md font-semibold text-on-surface">{{ $order['date'] }}</p>
                            </div>
                        </div>

                        @if (($order['status'] ?? null) === 'pending_payment')
                            @php($pmQris = $order['payment_methods']['qris'] ?? true)
                            @php($pmTransfer = $order['payment_methods']['transfer'] ?? false)

                            @if ($pmQris)
                            <div class="mt-4 rounded-2xl border border-primary/30 bg-secondary-container/20 p-5"
                                 data-qris-card
                                 data-qris-generate="{{ route('customer.orders.qris', $order['code']) }}"
                                 data-qris-status="{{ route('customer.orders.qris-status', $order['code']) }}"
                                 data-csrf="{{ csrf_token() }}">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">qr_code_2</span>
                                    <h2 class="font-headline-md text-lg font-bold text-on-surface">Bayar dengan QRIS</h2>
                                </div>
                                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Scan pakai e-wallet / m-banking apa pun (GoPay, OVO, DANA, dll). Pembayaran terkonfirmasi otomatis.</p>

                                <div class="mt-4 flex flex-col items-center gap-2 text-center">
                                    <div data-qris-loading class="py-8 text-on-surface-variant">
                                        <span class="material-symbols-outlined animate-spin">progress_activity</span>
                                        <p class="mt-1 font-body-sm text-sm">Menyiapkan QR…</p>
                                    </div>
                                    <img data-qris-img alt="QRIS" class="hidden h-60 w-60 rounded-2xl border border-surface-container-highest bg-white p-2">
                                    <p data-qris-amount class="hidden font-headline-sm text-xl font-bold text-on-surface"></p>
                                    <p data-qris-expiry class="hidden font-body-sm text-sm text-on-surface-variant"></p>
                                    <div data-qris-paid class="hidden w-full rounded-2xl bg-primary px-5 py-3 font-semibold text-on-primary">✅ Pembayaran diterima! Memuat ulang…</div>
                                    <p data-qris-error class="hidden font-body-sm text-sm text-error"></p>
                                    <button type="button" data-qris-refresh class="hidden rounded-2xl border border-primary px-4 py-2 text-sm font-semibold text-primary">Buat QR baru</button>
                                </div>
                            </div>
                            @endif

                            @if ($pmTransfer && ! empty($order['payment_accounts']))
                            <div class="mt-4 rounded-2xl border border-surface-container-highest bg-surface-container-lowest p-5">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-on-surface-variant">account_balance</span>
                                    <h2 class="font-headline-md text-lg font-bold text-on-surface">Transfer Bank</h2>
                                </div>
                                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Transfer tepat sejumlah <span class="font-bold text-on-surface">{{ $order['total'] ?? $order['payment']['grand_total'] ?? '' }}</span> ke salah satu rekening:</p>
                                <div class="mt-3 space-y-2">
                                    @foreach ($order['payment_accounts'] as $acc)
                                        <div class="rounded-2xl border border-surface-container-highest bg-surface-container-low px-4 py-3">
                                            <p class="font-body-sm text-sm font-semibold text-on-surface">{{ $acc['bank_name'] }}</p>
                                            <p class="font-headline-sm text-base font-bold tracking-wide text-on-surface">{{ $acc['account_number'] }}</p>
                                            <p class="font-body-sm text-xs text-on-surface-variant">a.n. {{ $acc['account_holder'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-3 font-body-sm text-xs text-on-surface-variant">Setelah transfer, konfirmasi via WhatsApp@if (! empty($order['store_whatsapp'])) ke <a href="https://wa.me/{{ $order['store_whatsapp'] }}" class="font-semibold text-primary hover:underline">admin</a>@endif dengan menyebut kode pesanan & kirim bukti transfer.</p>
                            </div>
                            @endif

                            <form action="{{ route('customer.orders.cancel', $order['code']) }}" method="POST" class="mt-4" onsubmit="return confirm('Batalkan pesanan ini?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-error-container px-4 py-2.5 font-body-sm text-sm font-semibold text-error">Batalkan pesanan</button>
                            </form>

                            <script>
                            (function () {
                                const card = document.querySelector('[data-qris-card]');
                                if (!card) return;
                                const genUrl = card.dataset.qrisGenerate, statusUrl = card.dataset.qrisStatus, csrf = card.dataset.csrf;
                                const q = s => card.querySelector(s);
                                const loading = q('[data-qris-loading]'), img = q('[data-qris-img]'), amount = q('[data-qris-amount]'),
                                      expiry = q('[data-qris-expiry]'), paid = q('[data-qris-paid]'), err = q('[data-qris-error]'), refresh = q('[data-qris-refresh]');
                                let pollTimer = null, expiryTimer = null, expiresAt = null;
                                const rupiah = n => 'Rp ' + (n || 0).toLocaleString('id-ID');

                                function showError(msg) {
                                    loading.classList.add('hidden');
                                    err.textContent = msg; err.classList.remove('hidden');
                                    refresh.classList.remove('hidden');
                                }
                                async function generate() {
                                    loading.classList.remove('hidden');
                                    [err, refresh, img, amount, expiry].forEach(el => el.classList.add('hidden'));
                                    try {
                                        const r = await fetch(genUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
                                        const j = await r.json();
                                        if (!r.ok) throw new Error(j.message || 'Gagal membuat QRIS');
                                        const d = j.data || {};
                                        if (!d.qris_image) throw new Error('QR tidak tersedia');
                                        img.src = d.qris_image; img.classList.remove('hidden');
                                        amount.textContent = rupiah(d.amount); amount.classList.remove('hidden');
                                        loading.classList.add('hidden');
                                        if (d.expired_at) { expiresAt = new Date(d.expired_at); startCountdown(); }
                                        startPolling();
                                    } catch (e) { showError(e.message); }
                                }
                                function startCountdown() {
                                    expiry.classList.remove('hidden');
                                    clearInterval(expiryTimer);
                                    expiryTimer = setInterval(() => {
                                        const diff = Math.floor((expiresAt - new Date()) / 1000);
                                        if (diff <= 0) { clearInterval(expiryTimer); clearInterval(pollTimer); expiry.textContent = 'QR kedaluwarsa'; refresh.classList.remove('hidden'); return; }
                                        const m = String(Math.floor(diff / 60)).padStart(2, '0'), s = String(diff % 60).padStart(2, '0');
                                        expiry.textContent = 'Berlaku ' + m + ':' + s;
                                    }, 1000);
                                }
                                function startPolling() {
                                    clearInterval(pollTimer);
                                    pollTimer = setInterval(async () => {
                                        try {
                                            const r = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                                            const j = await r.json();
                                            if ((j.data || {}).payment_status === 'paid') { clearInterval(pollTimer); clearInterval(expiryTimer); onPaid(); }
                                        } catch (e) {}
                                    }, 6000);
                                }
                                function onPaid() {
                                    [img, amount, expiry, refresh, loading].forEach(el => el.classList.add('hidden'));
                                    paid.classList.remove('hidden');
                                    setTimeout(() => location.reload(), 2500);
                                }
                                refresh.addEventListener('click', generate);
                                generate();
                            })();
                            </script>
                        @endif
                        @if (($order['status'] ?? null) === 'shipped')
                            <form action="{{ route('customer.orders.complete', $order['code']) }}" method="POST" class="mt-4" onsubmit="return confirm('Tandai pesanan ini sudah diterima?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-secondary-container px-4 py-2.5 font-body-sm text-sm font-semibold text-primary">Pesanan diterima</button>
                            </form>
                        @endif

                        <div class="mt-6 space-y-0">
                            @foreach ($timeline as $step)
                                @php
                                    $isCancelledStep = ($step['tone'] ?? null) === 'cancelled';
                                    $dot = $step['active']
                                        ? ($isCancelledStep ? 'bg-error text-on-error' : 'bg-primary text-on-primary')
                                        : 'bg-surface-container-high text-outline';
                                    $titleClr = $step['active']
                                        ? ($isCancelledStep ? 'text-error' : 'text-on-surface')
                                        : 'text-on-surface-variant';
                                @endphp
                                <div class="flex gap-4 pb-6 last:pb-0">
                                    <div class="flex flex-col items-center">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $dot }}">
                                            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">{{ $step['active'] ? ($isCancelledStep ? 'close' : 'check') : 'radio_button_unchecked' }}</span>
                                        </span>
                                        @if (! $loop->last)
                                            <span class="mt-1 w-px flex-1 {{ $step['active'] ? 'bg-primary/30' : 'bg-surface-container-high' }}"></span>
                                        @endif
                                    </div>
                                    <div class="pb-1">
                                        <p class="font-body-md font-semibold {{ $titleClr }}">{{ $step['label'] }}</p>
                                        <p class="mt-0.5 font-body-sm text-body-sm text-on-surface-variant">{{ $step['note'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    {{-- Items --}}
                    <article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Item pesanan</h2>
                        <div class="mt-4 space-y-3">
                            @foreach ($order['items'] as $item)
                                <div class="flex items-center justify-between gap-4 rounded-2xl bg-surface-container-low px-4 py-4 font-body-md text-body-md">
                                    <div>
                                        <p class="font-semibold text-on-surface">{{ $item['name'] }}</p>
                                        @if (! empty($item['variant']))
                                            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Varian {{ $item['variant'] }}</p>
                                        @endif
                                        <p class="font-body-sm text-body-sm text-on-surface-variant">Qty {{ $item['qty'] }}</p>
                                    </div>
                                    <p class="font-semibold text-on-surface">{{ $item['subtotal'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>

                <aside class="space-y-6">
                    <article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Pengiriman</h2>
                        <div class="mt-4 space-y-3">
                            <div class="rounded-2xl bg-surface-container-low px-4 py-4">
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Layanan</p>
                                <p class="mt-1 font-body-md font-semibold text-on-surface">{{ $order['shipping']['service'] }}</p>
                                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Estimasi {{ $order['shipping']['estimate'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-surface-container-low px-4 py-4">
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Alamat kirim</p>
                                <p class="mt-1 font-body-md leading-6 text-on-surface-variant">{{ $order['shipping']['address'] }}</p>
                                @if (! empty($order['shipping']['awb']))
                                    <p class="mt-2 font-body-sm text-xs text-on-surface-variant">AWB: {{ $order['shipping']['awb'] }}</p>
                                @endif
                            </div>
                        </div>
                    </article>

                    <article class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                        <h2 class="font-headline-md text-headline-md text-on-surface">Pembayaran</h2>
                        <div class="mt-4 space-y-3 font-body-md text-body-md">
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Total produk</span>
                                <span class="font-semibold text-on-surface">{{ $order['payment']['items_total'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Ongkir</span>
                                <span class="font-semibold text-on-surface">{{ $order['payment']['shipping_total'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-on-surface-variant">Kode unik</span>
                                <span class="font-semibold text-on-surface">{{ $order['payment']['unique_code'] }}</span>
                            </div>
                            @if (($order['payment']['used_unique_code'] ?? 'Rp0') !== 'Rp0')
                                <div class="flex items-center justify-between">
                                    <span class="text-on-surface-variant">Potongan saldo kode unik</span>
                                    <span class="font-semibold text-primary">-{{ $order['payment']['used_unique_code'] }}</span>
                                </div>
                            @endif
                            <div class="border-t border-dashed border-surface-container-highest pt-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-on-surface">Total transfer</span>
                                    <span class="font-headline-md text-lg font-bold text-primary">{{ $order['payment']['grand_total'] }}</span>
                                </div>
                            </div>
                        </div>

                        @if (!empty($order['payment_accounts']))
                            <div class="mt-5 rounded-2xl border border-primary/20 bg-primary/5 p-4">
                                <p class="font-body-sm text-body-sm font-semibold text-on-surface">💳 Transfer ke salah satu rekening:</p>
                                <div class="mt-3 space-y-3">
                                    @foreach ($order['payment_accounts'] as $acc)
                                        <div class="rounded-xl bg-surface-container-lowest p-3">
                                            <p class="font-body-md font-semibold text-on-surface">{{ $acc['bank_name'] }}</p>
                                            <p class="font-headline-md text-lg font-bold tracking-wide text-primary">{{ $acc['account_number'] }}</p>
                                            <p class="font-body-sm text-body-sm text-on-surface-variant">a.n. {{ $acc['account_holder'] }}</p>
                                            @if (!empty($acc['note']))
                                                <p class="mt-1 font-body-sm text-xs text-on-surface-variant">{{ $acc['note'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-3 font-body-sm text-xs text-on-surface-variant">Transfer tepat sesuai <span class="font-semibold">Total transfer</span> di atas (termasuk kode unik) lalu konfirmasi via WhatsApp.</p>
                            </div>
                        @endif

                        @php($waNumber = $order['store_whatsapp'] ?? '')
                        @if ($waNumber !== '')
                            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo, saya mau konfirmasi pembayaran pesanan '.($order['code'] ?? '')) }}" target="_blank" rel="noopener" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-on-background px-5 py-3.5 font-body-md font-semibold text-on-primary transition hover:bg-primary">
                                <span class="material-symbols-outlined text-[20px]">chat</span> Konfirmasi via WhatsApp
                            </a>
                        @endif
                    </article>
                </aside>
            </div>
        </section>
    </div>
</x-layouts.customer>
