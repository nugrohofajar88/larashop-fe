<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Akuntansi">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Akuntansi</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Rincian gross &amp; net per order</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Cakupan: order yang sudah dibayar (paid/processing/shipped/completed) pada bulan terpilih, berdasarkan tanggal pembayaran.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.accounting') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-600">Bulan</label>
                    <input type="month" name="month" value="{{ $meta['month'] ?? '' }}" class="mt-1 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                </div>
                <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Tampilkan</button>
            </form>
        </div>

        {{-- Kartu total --}}
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Total Ongkir (Shipping Fee)</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $meta['total_shipping_fee'] ?? 'Rp0' }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Total Cashback Ongkir</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-emerald-700">{{ $meta['total_cashback'] ?? 'Rp0' }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Total Biaya Jasa COD</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-rose-700">{{ $meta['total_cod_service_fee'] ?? 'Rp0' }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Total Produk (Items)</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $meta['total_items'] ?? 'Rp0' }}</p>
            </article>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">{{ $meta['month_label'] ?? '-' }}</h2>
                    <p class="mt-1 text-sm text-stone-500">{{ $meta['count'] ?? 0 }} order</p>
                </div>
            </div>

            {{-- Desktop: tabel --}}
            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Order</th>
                            <th class="px-4 py-3 font-medium">Metode</th>
                            <th class="px-4 py-3 font-medium text-right">Gross</th>
                            <th class="px-4 py-3 font-medium text-right">Fee COD</th>
                            <th class="px-4 py-3 font-medium text-right">Cashback</th>
                            <th class="px-4 py-3 font-medium text-right">Net</th>
                            <th class="px-4 py-3 font-medium text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($orders as $order)
                            @php($netClass = $order['net_value'] > 0 ? 'text-emerald-700' : ($order['net_value'] < 0 ? 'text-rose-600' : 'text-stone-900'))
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-stone-900">{{ $order['code'] }}</p>
                                    <p class="mt-0.5 text-xs text-stone-500">{{ $order['date'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-stone-700">{{ $order['payment_method'] }}</td>
                                <td class="px-4 py-3 text-right text-stone-800">{{ $order['gross'] }}</td>
                                <td class="px-4 py-3 text-right text-stone-600">{{ $order['cod_service_fee'] }}</td>
                                <td class="px-4 py-3 text-right text-stone-600">{{ $order['shipping_cashback'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold {{ $netClass }}">{{ $order['net'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $order['status'] === 'CUAN' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $order['status'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada order pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: kartu --}}
            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($orders as $order)
                    @php($netClass = $order['net_value'] > 0 ? 'text-emerald-700' : ($order['net_value'] < 0 ? 'text-rose-600' : 'text-stone-900'))
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-stone-900">{{ $order['code'] }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $order['date'] }} · {{ $order['payment_method'] }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $order['status'] === 'CUAN' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $order['status'] }}</span>
                        </div>
                        <dl class="mt-3 space-y-1.5 text-sm">
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Gross</dt><dd class="text-stone-800">{{ $order['gross'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Fee COD</dt><dd class="text-stone-600">{{ $order['cod_service_fee'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Cashback</dt><dd class="text-stone-600">{{ $order['shipping_cashback'] }}</dd></div>
                            <div class="flex items-center justify-between border-t border-stone-100 pt-1.5"><dt class="font-medium text-stone-700">Net</dt><dd class="font-semibold {{ $netClass }}">{{ $order['net'] }}</dd></div>
                        </dl>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada order pada bulan ini.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.admin>
