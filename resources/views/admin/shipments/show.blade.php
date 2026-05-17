<x-layouts.admin :title="'Admin Larashop | ' . $shipment['code']">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Shipment Detail</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">{{ $shipment['code'] }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Ringkasan shipment untuk monitoring AWB, courier, dan status pengiriman.
                </p>
            </div>

            <a href="{{ route('admin.shipments.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                Kembali ke shipments
            </a>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.shipments.index') }}" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white">Monitoring Shipment</a>
            <a href="{{ route('admin.shipments.settings') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700">Shipment Settings</a>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_0.9fr]">
            <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-stone-950">Informasi shipment</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div class="rounded-2xl bg-stone-50 px-4 py-4">
                        <p class="text-sm text-stone-500">Order</p>
                        <p class="mt-1 font-semibold text-stone-900">{{ $shipment['order_code'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-50 px-4 py-4">
                        <p class="text-sm text-stone-500">Customer</p>
                        <p class="mt-1 font-semibold text-stone-900">{{ $shipment['customer'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-50 px-4 py-4">
                        <p class="text-sm text-stone-500">Courier</p>
                        <p class="mt-1 font-semibold text-stone-900">{{ $shipment['courier'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-50 px-4 py-4">
                        <p class="text-sm text-stone-500">Status</p>
                        <p class="mt-1 font-semibold text-stone-900">{{ $shipment['status'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-stone-50 px-4 py-4 md:col-span-2">
                        <p class="text-sm text-stone-500">AWB / Resi</p>
                        <p class="mt-1 font-semibold text-stone-900">{{ $shipment['awb'] ?? 'Belum tersedia' }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-semibold text-stone-950">Catatan operasional</h2>
                <div class="mt-5 rounded-2xl bg-stone-50 px-4 py-4 text-sm leading-6 text-stone-700">
                    {{ $shipment['note'] }}
                </div>
            </section>
        </div>
    </section>
</x-layouts.admin>
