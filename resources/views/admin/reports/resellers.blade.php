<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Laporan Reseller">
    <section class="space-y-6">
        @include('admin.reports._tabs')

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Laporan</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Performa Reseller</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Top reseller berdasarkan komisi yang sudah dikonfirmasi pada bulan terpilih.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.reports.resellers') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-600">Bulan</label>
                    <input type="month" name="month" value="{{ $meta['month'] ?? '' }}" class="mt-1 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                </div>
                <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Tampilkan</button>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Reseller Aktif</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $meta['total_resellers_active'] ?? 0 }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Reseller Hasilkan Komisi Bulan Ini</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ count($resellers) }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Total Komisi Dikonfirmasi</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-emerald-700">{{ $meta['total_commission_confirmed'] ?? 'Rp0' }}</p>
            </article>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-stone-950">{{ $meta['month_label'] ?? '-' }}</h2>
            </div>

            {{-- Desktop: tabel --}}
            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">#</th>
                            <th class="px-4 py-3 font-medium">Reseller</th>
                            <th class="px-4 py-3 font-medium text-right">Order Terkonfirmasi</th>
                            <th class="px-4 py-3 font-medium text-right">Total Pembeli Tertaut</th>
                            <th class="px-4 py-3 font-medium text-right">Komisi Dikonfirmasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($resellers as $index => $reseller)
                            <tr>
                                <td class="px-4 py-3 text-stone-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-stone-900">{{ $reseller['name'] }}</p>
                                    <p class="mt-0.5 font-mono text-xs text-stone-500">{{ $reseller['referral_code'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-right text-stone-800">{{ $reseller['order_count'] }}</td>
                                <td class="px-4 py-3 text-right text-stone-800">{{ $reseller['referred_count'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-stone-900">{{ $reseller['total_commission'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada komisi yang dikonfirmasi pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: kartu --}}
            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($resellers as $index => $reseller)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs text-stone-500">#{{ $index + 1 }}</p>
                                <p class="font-semibold text-stone-900">{{ $reseller['name'] }}</p>
                                <p class="mt-0.5 font-mono text-xs text-stone-500">{{ $reseller['referral_code'] }}</p>
                            </div>
                        </div>
                        <dl class="mt-3 space-y-1.5 text-sm">
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Order Terkonfirmasi</dt><dd class="text-stone-800">{{ $reseller['order_count'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Pembeli Tertaut</dt><dd class="text-stone-800">{{ $reseller['referred_count'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Komisi Dikonfirmasi</dt><dd class="font-semibold text-stone-900">{{ $reseller['total_commission'] }}</dd></div>
                        </dl>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada komisi yang dikonfirmasi pada bulan ini.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.admin>
