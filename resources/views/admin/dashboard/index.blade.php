<x-layouts.admin title="Admin Larashop | Dashboard">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Dashboard</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Panel operasional untuk katalog, order, dan shipment</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Tampilan admin dibuat desktop-first agar nyaman dipakai untuk validasi pembayaran, pengelolaan produk, dan proses integrasi pengiriman.
                </p>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                Dummy data aktif untuk tahap pengembangan UI
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-stone-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-950">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm text-stone-600">{{ $stat['note'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-stone-950">Daftar order terbaru</h2>
                        <p class="mt-1 text-sm text-stone-500">Simulasi tampilan meja kerja admin</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700">Lihat semua</a>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-stone-200">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-stone-50 text-stone-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Order</th>
                                <th class="px-4 py-3 font-medium">Customer</th>
                                <th class="px-4 py-3 font-medium">Nominal</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200 bg-white">
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="px-4 py-4 font-semibold text-stone-900">
                                        <a href="{{ route('admin.orders.show', $order['code']) }}">{{ $order['code'] }}</a>
                                    </td>
                                    <td class="px-4 py-4 text-stone-600">{{ $order['customer'] }}</td>
                                    <td class="px-4 py-4 text-stone-600">{{ $order['amount'] }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">
                                            {{ $order['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-stone-950">Prioritas admin hari ini</h2>
                <p class="mt-1 text-sm text-stone-500">Checklist operasional yang nantinya bisa berkembang jadi workflow dashboard.</p>

                <div class="mt-5 space-y-3">
                    @foreach ($tasks as $task)
                        <div class="flex items-start gap-3 rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-xs font-semibold text-white">
                                {{ $loop->iteration }}
                            </span>
                            <p class="text-sm leading-6 text-stone-700">{{ $task }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </section>
</x-layouts.admin>
