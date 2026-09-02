<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Tren Penjualan">
    <section class="space-y-6">
        @include('admin.reports._tabs')

        @php($granularity = $meta['granularity'] ?? 'day')
        @php($growth = $meta['growth_pct'] ?? null)

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Laporan</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Tren Penjualan</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Arah omzet dari waktu ke waktu - biar tahu lagi naik atau turun, bukan cuma snapshot sesaat.
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.reports.trend', ['granularity' => 'day']) }}"
                    class="rounded-2xl px-5 py-2.5 text-sm font-semibold transition {{ $granularity === 'day' ? 'bg-stone-900 text-white' : 'border border-stone-200 bg-white text-stone-600 hover:border-stone-300' }}">
                    Harian (30 hari)
                </a>
                <a href="{{ route('admin.reports.trend', ['granularity' => 'month']) }}"
                    class="rounded-2xl px-5 py-2.5 text-sm font-semibold transition {{ $granularity === 'month' ? 'bg-stone-900 text-white' : 'border border-stone-200 bg-white text-stone-600 hover:border-stone-300' }}">
                    Bulanan (12 bulan)
                </a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Total Omzet</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $meta['total_revenue'] ?? 'Rp0' }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Total Order</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $meta['total_orders'] ?? 0 }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Rata-rata per {{ $granularity === 'day' ? 'Hari' : 'Bulan' }}</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $meta['avg_revenue_per_period'] ?? 'Rp0' }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Dibanding Periode Sebelumnya</p>
                @if ($growth === null)
                    <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-400">-</p>
                    <p class="mt-1 text-xs text-stone-400">Periode sebelumnya belum ada data pembanding.</p>
                @else
                    <p class="mt-3 text-2xl font-semibold tracking-tight {{ $growth >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
                    </p>
                    <p class="mt-1 text-xs text-stone-500">{{ $growth >= 0 ? 'Naik' : 'Turun' }} dari periode sebelumnya</p>
                @endif
            </article>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-stone-950">Grafik Omzet</h2>
            <div class="mt-5" style="height: 320px;">
                <canvas data-sales-trend-chart data-points="{{ json_encode($points) }}"></canvas>
            </div>
        </section>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-semibold text-stone-950">Rincian per {{ $granularity === 'day' ? 'Hari' : 'Bulan' }}</h2>

            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ $granularity === 'day' ? 'Tanggal' : 'Bulan' }}</th>
                            <th class="px-4 py-3 font-medium text-right">Jumlah Order</th>
                            <th class="px-4 py-3 font-medium text-right">Omzet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @foreach (array_reverse($points) as $point)
                            <tr>
                                <td class="px-4 py-3 text-stone-800">{{ $point['period_label'] }}</td>
                                <td class="px-4 py-3 text-right text-stone-600">{{ $point['order_count'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-stone-900">{{ $point['revenue'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5 space-y-2 md:hidden">
                @foreach (array_reverse($points) as $point)
                    <div class="flex items-center justify-between rounded-2xl border border-stone-200 bg-white p-4">
                        <div>
                            <p class="font-medium text-stone-800">{{ $point['period_label'] }}</p>
                            <p class="text-xs text-stone-500">{{ $point['order_count'] }} order</p>
                        </div>
                        <p class="font-semibold text-stone-900">{{ $point['revenue'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </section>
</x-layouts.admin>
