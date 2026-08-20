<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Laporan Pelanggan">
    <section class="space-y-6">
        @include('admin.reports._tabs')

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Laporan</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Pelanggan</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Top 50 pelanggan berdasarkan total belanja pada bulan terpilih, dan pembeda pelanggan baru vs berulang.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.reports.customers') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-600">Bulan</label>
                    <input type="month" name="month" value="{{ $meta['month'] ?? '' }}" class="mt-1 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                </div>
                <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Tampilkan</button>
            </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Total Pelanggan Belanja</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $meta['customer_count'] ?? 0 }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Pelanggan Baru (1x order)</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $meta['new_count'] ?? 0 }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Pelanggan Berulang</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-emerald-700">{{ $meta['repeat_count'] ?? 0 }}</p>
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
                            <th class="px-4 py-3 font-medium">Pelanggan</th>
                            <th class="px-4 py-3 font-medium text-right">Jml Order</th>
                            <th class="px-4 py-3 font-medium text-right">Total Belanja</th>
                            <th class="px-4 py-3 font-medium text-center">Tipe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($customers as $index => $customer)
                            <tr>
                                <td class="px-4 py-3 text-stone-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-stone-900">{{ $customer['name'] }}</p>
                                    <p class="mt-0.5 text-xs text-stone-500">{{ $customer['phone'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-right text-stone-800">{{ $customer['order_count'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-stone-900">{{ $customer['total_spent'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $customer['is_repeat'] ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">{{ $customer['is_repeat'] ? 'Berulang' : 'Baru' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada pelanggan yang belanja pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: kartu --}}
            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($customers as $index => $customer)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs text-stone-500">#{{ $index + 1 }}</p>
                                <p class="font-semibold text-stone-900">{{ $customer['name'] }}</p>
                                <p class="mt-0.5 text-xs text-stone-500">{{ $customer['phone'] }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $customer['is_repeat'] ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">{{ $customer['is_repeat'] ? 'Berulang' : 'Baru' }}</span>
                        </div>
                        <dl class="mt-3 space-y-1.5 text-sm">
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Jumlah Order</dt><dd class="text-stone-800">{{ $customer['order_count'] }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-stone-500">Total Belanja</dt><dd class="font-semibold text-stone-900">{{ $customer['total_spent'] }}</dd></div>
                        </dl>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada pelanggan yang belanja pada bulan ini.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.admin>
