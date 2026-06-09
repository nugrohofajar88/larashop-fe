<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Shipments">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Shipments</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Monitoring shipment dan AWB dari satu panel</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Gunakan halaman ini untuk melihat shipment yang siap dibuat, pickup yang sudah dijadwalkan, dan paket yang sedang berjalan.
                </p>
            </div>
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            @foreach ($stats as $stat)
                <article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-stone-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-stone-950">{{ $stat['value'] }}</p>
                    <p class="mt-2 text-sm text-stone-600">{{ $stat['note'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.shipments.index') }}" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white">Monitoring Shipment</a>
            <a href="{{ route('admin.shipments.settings') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700">Shipment Settings</a>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Origin shipment aktif</h2>
                    <p class="mt-1 text-sm text-stone-500">Origin ini akan dipakai sebagai titik asal default untuk estimasi ongkir customer.</p>
                </div>
                <a href="{{ route('admin.shipments.settings') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Ubah setting
                </a>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-4">
                <div class="rounded-2xl bg-stone-50 px-4 py-4">
                    <p class="text-stone-500">Gudang</p>
                    <p class="mt-1 font-semibold text-stone-900">{{ $shipmentSettings['label'] ?? '-' }}</p>
                </div>
                <div class="rounded-2xl bg-stone-50 px-4 py-4">
                    <p class="text-stone-500">PIC</p>
                    <p class="mt-1 font-semibold text-stone-900">{{ $shipmentSettings['contact_name'] ?? '-' }}</p>
                </div>
                <div class="rounded-2xl bg-stone-50 px-4 py-4">
                    <p class="text-stone-500">Kota</p>
                    <p class="mt-1 font-semibold text-stone-900">{{ $shipmentSettings['city'] ?? '-' }}</p>
                </div>
                <div class="rounded-2xl bg-stone-50 px-4 py-4">
                    <p class="text-stone-500">Origin ID</p>
                    <p class="mt-1 font-semibold text-stone-900">{{ $shipmentSettings['origin_id'] ?? '-' }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <form class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between" method="GET" action="{{ route('admin.shipments.index') }}">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Daftar shipment</h2>
                    <p class="mt-1 text-sm text-stone-500">Filter shipment berdasarkan status proses pengiriman.</p>
                </div>

                <div class="flex gap-3">
                    <select name="status" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                        <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>Semua status</option>
                        <option value="ready_to_create" {{ $activeStatus === 'ready_to_create' ? 'selected' : '' }}>ready_to_create</option>
                        <option value="pickup_scheduled" {{ $activeStatus === 'pickup_scheduled' ? 'selected' : '' }}>pickup_scheduled</option>
                        <option value="in_transit" {{ $activeStatus === 'in_transit' ? 'selected' : '' }}>in_transit</option>
                    </select>
                    <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Terapkan</button>
                </div>
            </form>

            <div class="mt-5 grid gap-4 xl:grid-cols-3">
                @foreach ($shipments as $shipment)
                    <article class="rounded-[1.75rem] border border-stone-200 bg-stone-50 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-stone-950">{{ $shipment['code'] }}</p>
                                <p class="mt-1 text-sm text-stone-500">{{ $shipment['order_code'] }} • {{ $shipment['customer'] }}</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-stone-700">{{ $shipment['status'] }}</span>
                        </div>

                        <div class="mt-5 space-y-3 text-sm">
                            <div class="rounded-2xl bg-white px-4 py-4">
                                <p class="text-stone-500">Courier</p>
                                <p class="mt-1 font-semibold text-stone-900">{{ $shipment['courier'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-white px-4 py-4">
                                <p class="text-stone-500">AWB</p>
                                <p class="mt-1 font-semibold text-stone-900">{{ $shipment['awb'] ?? 'Belum tersedia' }}</p>
                            </div>
                            <div class="rounded-2xl bg-white px-4 py-4">
                                <p class="text-stone-500">Catatan</p>
                                <p class="mt-1 text-stone-700">{{ $shipment['note'] }}</p>
                            </div>
                        </div>

                        <a href="{{ route('admin.shipments.show', $shipment['code']) }}" class="mt-5 inline-flex rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white">
                            Detail shipment
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    </section>
</x-layouts.admin>
