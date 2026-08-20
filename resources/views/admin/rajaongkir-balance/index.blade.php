<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Saldo RajaOngkir">
    <section class="space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Saldo RajaOngkir</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                Estimasi saldo deposit RajaOngkir/Komerce, direkonstruksi dari top up yang kamu catat manual, ongkir yang sudah di-booking, biaya generate QRIS, dan remitansi COD yang sudah selesai. Bandingkan angka "Estimasi Saldo" di bawah dengan saldo asli di dashboard RajaOngkir - kalau selisihnya jauh, ada yang perlu ditelusuri.
            </p>
        </div>

        {{-- Kartu estimasi saldo --}}
        @php($estVal = $meta['estimated_balance_value'] ?? 0)
        <section class="overflow-hidden rounded-[1.5rem] border border-stone-200 bg-white shadow-sm">
            <div class="px-6 py-5">
                <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-400">Rekonstruksi Saldo</h3>

                <dl class="mt-5 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-stone-500">Total Top Up</dt>
                        <dd class="font-medium text-emerald-700">{{ $meta['total_topup'] ?? 'Rp0' }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-stone-500">Total Ongkir Non-COD (net cashback, sudah di-booking)</dt>
                        <dd class="font-medium text-rose-600">(-{{ $meta['total_ongkir'] ?? 'Rp0' }})</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-stone-500">Total Biaya QRIS ({{ $meta['qris_count'] ?? 0 }}x generate)</dt>
                        <dd class="font-medium text-rose-600">(-{{ $meta['total_qris_fee'] ?? 'Rp0' }})</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-stone-500">Total COD Diremit (order selesai)</dt>
                        <dd class="font-medium text-emerald-700">{{ $meta['total_cod_remitted'] ?? 'Rp0' }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <dt class="text-stone-500">Biaya Retry-Booking ({{ $meta['retry_fee_count'] ?? 0 }}x)</dt>
                        <dd class="font-medium text-rose-600">(-{{ $meta['total_retry_fee'] ?? 'Rp0' }})</dd>
                    </div>
                </dl>

                <div class="my-5 border-t border-dashed border-stone-200"></div>

                <div class="flex items-end justify-between">
                    <p class="text-sm font-medium text-stone-600">Estimasi Saldo</p>
                    <p class="text-3xl font-bold tracking-tight {{ $estVal < 0 ? 'text-rose-600' : 'text-stone-950' }}">{{ $meta['estimated_balance'] ?? 'Rp0' }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-stone-100 bg-sky-50 px-6 py-4">
                <div>
                    <p class="text-sm font-semibold text-sky-800">Dana COD dalam Perjalanan ({{ $meta['cod_in_transit_count'] ?? 0 }} order)</p>
                    <p class="mt-0.5 text-xs text-sky-700">Belum diremit, masih dipegang kurir - terpisah dari Estimasi Saldo di atas (persis seperti "Balance" vs "Potential Income" di dashboard RajaOngkir).</p>
                </div>
                <p class="text-lg font-bold text-sky-800">{{ $meta['cod_in_transit'] ?? 'Rp0' }}</p>
            </div>
        </section>

        {{-- Order dengan selisih ongkir ke ekspedisi - potensi tagihan susulan --}}
        @if (! empty($flaggedDiscrepancies))
            <section class="rounded-[1.5rem] border border-amber-200 bg-amber-50 p-5">
                <h3 class="text-sm font-semibold text-amber-800">⚠️ Order dengan Potensi Tagihan Susulan ({{ count($flaggedDiscrepancies) }})</h3>
                <p class="mt-1 text-xs text-amber-700">Ongkir yang tercatat di order ini beda dari yang beneran di-charge ekspedisi (biasanya karena data berat produk salah saat booking). Ongkir ke customer tetap sesuai (kolom "Tercatat"), tapi kalau kurir nimbang ulang & nagih susulan di akhir bulan, cocokkan ke daftar ini dulu.</p>

                <div class="mt-4 space-y-3">
                    @foreach ($flaggedDiscrepancies as $item)
                        <article class="rounded-2xl border border-amber-200 bg-white p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="font-semibold text-stone-900">{{ $item['code'] }}</p>
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-stone-500">Tercatat: <span class="font-medium text-stone-800">{{ $item['shipping_total'] }}</span></span>
                                    <span class="text-stone-500">Real ekspedisi: <span class="font-medium text-rose-600">{{ $item['shipping_actual_value'] }}</span></span>
                                </div>
                            </div>
                            <p class="mt-2 text-xs leading-5 text-stone-600">{{ $item['note'] }}</p>
                            <p class="mt-1 text-xs text-stone-400">Ditandai {{ $item['reconciled_at'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Order dengan biaya retry-booking (percobaan booking gagal, tetap kepotong) --}}
        @if (! empty($retryFees))
            <section class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-stone-900">Biaya Retry-Booking ({{ count($retryFees) }} order)</h3>
                <p class="mt-1 text-xs text-stone-500">Order ini sempat gagal booking pengiriman (kemungkinan karena timeout/koneksi saat memanggil API kurir), lalu dicoba ulang sampai berhasil. Kurir tetap mengenakan biaya di setiap percobaan, bukan cuma yang berhasil - jadi ada biaya ekstra yang beneran kepotong dari saldo deposit, di luar ongkir yang tercatat normal.</p>

                <div class="mt-4 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-stone-50 text-stone-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Pelanggan</th>
                                <th class="px-4 py-3 font-medium">Order</th>
                                <th class="px-4 py-3 font-medium">Komerce Order No</th>
                                <th class="px-4 py-3 font-medium">AWB</th>
                                <th class="px-4 py-3 font-medium">Tanggal</th>
                                <th class="px-4 py-3 font-medium text-right">Biaya Retry</th>
                                <th class="px-4 py-3 font-medium text-right">Banyak Retry</th>
                                <th class="px-4 py-3 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @foreach ($retryFees as $item)
                                <tr>
                                    <td class="px-4 py-3 text-stone-600">{{ $item['recipient_name'] ?: '-' }}</td>
                                    <td class="px-4 py-3 font-medium text-stone-900">{{ $item['code'] }}</td>
                                    <td class="px-4 py-3 text-stone-600">{{ $item['komerce_order_no'] ?: '-' }}</td>
                                    <td class="px-4 py-3 text-stone-600">{{ $item['awb'] ?: '-' }}</td>
                                    <td class="px-4 py-3 text-stone-500">{{ $item['date'] }}</td>
                                    <td class="px-4 py-3 text-right text-stone-700">{{ $item['fee_per_retry'] }}</td>
                                    <td class="px-4 py-3 text-right text-stone-700">{{ $item['retry_count'] }}x</td>
                                    <td class="px-4 py-3 text-right font-semibold text-rose-600">{{ $item['fee'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 space-y-3 md:hidden">
                    @foreach ($retryFees as $item)
                        <article class="rounded-2xl border border-stone-200 bg-white p-4">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-stone-900">{{ $item['code'] }}</p>
                                <p class="font-semibold text-rose-600">{{ $item['fee'] }}</p>
                            </div>
                            <p class="mt-1 text-xs text-stone-500">{{ $item['recipient_name'] ?: '-' }} &middot; AWB {{ $item['awb'] ?: '-' }} &middot; {{ $item['date'] }}</p>
                            <p class="mt-1 text-xs text-stone-400">Komerce {{ $item['komerce_order_no'] ?: '-' }} &middot; {{ $item['fee_per_retry'] }} x {{ $item['retry_count'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Sinkronisasi biaya generate QRIS dari file mutasi --}}
        <section class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-stone-900">Sinkronisasi Biaya QRIS</h3>
            <p class="mt-1 text-xs text-stone-500">Upload file mutasi RajaOngkir/Komerce (format CSV, kolom "Tanggal", "Jenis Transaksi", "Mutasi") untuk update total biaya generate QRIS di atas. Baris yang sudah pernah tercatat otomatis dilewati, jadi aman diulang.</p>
            <form method="POST" action="{{ route('admin.rajaongkir-balance.sync-qris') }}" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-center gap-3">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv" required class="block w-full max-w-xs text-sm text-stone-600 file:mr-3 file:rounded-xl file:border-0 file:bg-stone-900 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white">
                <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-2.5 text-sm font-semibold text-white">Sinkronkan</button>
            </form>
        </section>

        {{-- Form tambah top up --}}
        <section class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-stone-900">Catat Top Up Baru</h3>
            <form method="POST" action="{{ route('admin.rajaongkir-balance.store-topup') }}" class="mt-4 grid gap-3 sm:grid-cols-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-stone-600">Tanggal</label>
                    <input type="date" name="topup_date" value="{{ old('topup_date', now()->format('Y-m-d')) }}" required class="mt-1 w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600">Nominal (Rp)</label>
                    <input type="number" name="amount" min="1" value="{{ old('amount') }}" required placeholder="500000" class="mt-1 w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-stone-600">Catatan (opsional)</label>
                    <input type="text" name="note" value="{{ old('note') }}" placeholder="Mis. top up via QRIS pribadi" class="mt-1 w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                </div>
                <div class="sm:col-span-4">
                    <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-2.5 text-sm font-semibold text-white">Simpan Top Up</button>
                </div>
            </form>
        </section>

        {{-- Riwayat top up --}}
        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-stone-950">Riwayat Top Up</h2>

            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium text-right">Nominal</th>
                            <th class="px-4 py-3 font-medium">Catatan</th>
                            <th class="px-4 py-3 font-medium">Dicatat oleh</th>
                            <th class="px-4 py-3 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($topups as $topup)
                            <tr>
                                <td class="px-4 py-3 text-stone-700">{{ $topup['topup_date'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-emerald-700">{{ $topup['amount'] }}</td>
                                <td class="px-4 py-3 text-stone-600">{{ $topup['note'] ?: '-' }}</td>
                                <td class="px-4 py-3 text-stone-500">{{ $topup['created_by'] ?: '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.rajaongkir-balance.destroy-topup', $topup['id']) }}" onsubmit="return confirm('Hapus catatan top up ini?')">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada catatan top up.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($topups as $topup)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $topup['topup_date'] }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $topup['note'] ?: '-' }}</p>
                                <p class="mt-0.5 text-xs text-stone-400">oleh {{ $topup['created_by'] ?: '-' }}</p>
                            </div>
                            <p class="font-semibold text-emerald-700">{{ $topup['amount'] }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.rajaongkir-balance.destroy-topup', $topup['id']) }}" onsubmit="return confirm('Hapus catatan top up ini?')" class="mt-3">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Hapus</button>
                        </form>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada catatan top up.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.admin>
