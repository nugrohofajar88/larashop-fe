<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Laporan Performa Ekspedisi">
    <section class="space-y-6">
        @include('admin.reports._tabs')

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Laporan</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Performa Ekspedisi</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Rekap per kurir: jumlah order, rata-rata ongkir & cashback, dan order yang saat ini masih macet gagal booking.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.reports.shipping') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-600">Bulan</label>
                    <input type="month" name="month" value="{{ $meta['month'] ?? '' }}" class="mt-1 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                </div>
                <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Tampilkan</button>
            </form>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
            Catatan: kolom "Gagal Saat Ini" itu snapshot kondisi sekarang (order dalam periode ini yang masih belum ada AWB & catatannya mengandung kata "gagal") - bukan total historis semua percobaan booking, karena sistem cuma menyimpan pesan gagal yang TERAKHIR.
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-stone-950">{{ $meta['month_label'] ?? '-' }}</h2>
                <p class="mt-1 text-sm text-stone-500">{{ $meta['total_orders'] ?? 0 }} order</p>
            </div>

            {{-- Desktop: tabel --}}
            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Kurir</th>
                            <th class="px-4 py-3 font-medium text-right">Jml Order</th>
                            <th class="px-4 py-3 font-medium text-right">Gagal Saat Ini</th>
                            <th class="px-4 py-3 font-medium text-right">Rata-rata Ongkir</th>
                            <th class="px-4 py-3 font-medium text-right">Total Cashback</th>
                            <th class="px-4 py-3 font-medium text-right">Rata-rata Jam s/d Dikirim</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($couriers as $courier)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-stone-900">{{ $courier['courier'] }}</p>
                                    <p class="mt-0.5 text-xs text-stone-500">{{ implode(', ', $courier['service_names']) ?: '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-right text-stone-800">{{ $courier['order_count'] }}</td>
                                <td class="px-4 py-3 text-right {{ $courier['failed_now_count'] > 0 ? 'font-semibold text-rose-600' : 'text-stone-600' }}">{{ $courier['failed_now_count'] }}</td>
                                <td class="px-4 py-3 text-right text-stone-600">{{ $courier['avg_shipping_cost'] }}</td>
                                <td class="px-4 py-3 text-right text-emerald-700">{{ $courier['total_cashback'] }}</td>
                                <td class="px-4 py-3 text-right text-stone-600">{{ $courier['avg_hours_to_ship'] !== null ? $courier['avg_hours_to_ship'].' jam' : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada order pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: kartu --}}
            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($couriers as $courier)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <p class="font-semibold text-stone-900">{{ $courier['courier'] }}</p>
                        <p class="mt-0.5 text-xs text-stone-500">{{ implode(', ', $courier['service_names']) ?: '-' }}</p>
                        <dl class="mt-3 space-y-1.5 text-sm">
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Jumlah Order</dt><dd class="text-stone-800">{{ $courier['order_count'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Gagal Saat Ini</dt><dd class="{{ $courier['failed_now_count'] > 0 ? 'font-semibold text-rose-600' : 'text-stone-600' }}">{{ $courier['failed_now_count'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Rata-rata Ongkir</dt><dd class="text-stone-600">{{ $courier['avg_shipping_cost'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Total Cashback</dt><dd class="text-emerald-700">{{ $courier['total_cashback'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Rata-rata Jam s/d Dikirim</dt><dd class="text-stone-600">{{ $courier['avg_hours_to_ship'] !== null ? $courier['avg_hours_to_ship'].' jam' : '-' }}</dd></div>
                        </dl>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada order pada bulan ini.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.admin>
