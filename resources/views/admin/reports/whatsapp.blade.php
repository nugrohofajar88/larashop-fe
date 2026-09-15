<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Laporan WhatsApp">
    <section class="space-y-6">
        @include('admin.reports._tabs')

        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Laporan</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Penggunaan WhatsApp</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Aktivitas nomor WA bisnis & korelasinya ke penjualan pada bulan terpilih.
                </p>
            </div>

            <form method="GET" action="{{ route('admin.reports.whatsapp') }}" class="flex items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-600">Bulan</label>
                    <input type="month" name="month" value="{{ $meta['month'] ?? '' }}" class="mt-1 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                </div>
                <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Tampilkan</button>
            </form>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            ⚠️ Nomor WA ini juga dipakai untuk keperluan pribadi. <strong>Pesan Masuk</strong> di bawah termasuk chat pribadi (tidak bisa dipisahkan otomatis). <strong>Estimasi Pesan Bisnis Masuk</strong> adalah pendekatan (pesan yang dapat balasan bot) — bukan angka pasti.
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Pesan Masuk (total, termasuk pribadi)</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $stats['messages_in'] ?? 0 }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Pesan Keluar (balasan bot)</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-stone-950">{{ $stats['messages_out'] ?? 0 }}</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Estimasi Pesan Bisnis Masuk</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-emerald-700">{{ $stats['business_messages_in_estimate'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-stone-400">Estimasi, bukan angka pasti</p>
            </article>
            <article class="rounded-[1.5rem] border border-stone-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-stone-500">Order dari WhatsApp</p>
                <p class="mt-3 text-2xl font-semibold tracking-tight text-emerald-700">{{ $stats['whatsapp_order_count'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-stone-500">dari {{ $meta['total_order_count'] ?? 0 }} order bulan ini</p>
            </article>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-stone-950">Omzet: WhatsApp vs Web — {{ $meta['month_label'] ?? '-' }}</h2>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-5">
                    <p class="text-sm text-stone-500">Order via WhatsApp</p>
                    <p class="mt-2 text-xl font-semibold text-stone-900">{{ $stats['whatsapp_order_count'] ?? 0 }} order</p>
                    <p class="mt-1 text-lg font-semibold text-emerald-700">{{ $stats['whatsapp_revenue'] ?? 'Rp0' }}</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-stone-50 p-5">
                    <p class="text-sm text-stone-500">Order via Web</p>
                    <p class="mt-2 text-xl font-semibold text-stone-900">{{ $stats['web_order_count'] ?? 0 }} order</p>
                    <p class="mt-1 text-lg font-semibold text-stone-900">{{ $stats['web_revenue'] ?? 'Rp0' }}</p>
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-stone-400">
                Order dari WhatsApp dideteksi dari kode order yang muncul di transkrip pesan keluar bot — order lama (sebelum pencatatan transkrip lengkap) bisa saja under-detect.
                Total omzet: {{ $stats['whatsapp_revenue'] ?? 'Rp0' }} + {{ $stats['web_revenue'] ?? 'Rp0' }} = {{ $meta['total_revenue'] ?? 'Rp0' }}.
            </p>
        </section>
    </section>
</x-layouts.admin>
