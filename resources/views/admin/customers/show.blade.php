<x-layouts.admin :title="'Admin Sobat Akar Tani Kimia | ' . $customer['name']">
    <section class="space-y-6">
        <x-admin.page-header
            eyebrow="Admin Customer Detail"
            :title="$customer['name']"
            description="Review data akun, status, dan histori ringkas customer."
        >
            <x-slot:actions>
                <a href="{{ route('admin.customers.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke daftar
                </a>
                <a href="{{ route('admin.customers.edit', $customer['code']) }}" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Edit customer
                </a>
                <form method="POST" action="{{ route('admin.customers.destroy', $customer['code']) }}" data-confirm="Hapus customer {{ $customer['name'] }}? Tindakan ini permanen." data-confirm-ok="Ya, hapus">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-2xl bg-rose-50 px-5 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-100">
                        Hapus customer
                    </button>
                </form>
            </x-slot:actions>
        </x-admin.page-header>

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-6">
                <x-admin.form-section>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-stone-500">{{ $customer['code'] }}</p>
                            <h2 class="mt-2 text-2xl font-semibold text-stone-950">{{ $customer['name'] }}</h2>
                        </div>
                        <span class="rounded-full {{ $customer['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : ($customer['status'] === 'Menunggu verifikasi' ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-700') }} px-3 py-1 text-xs font-semibold">
                            {{ $customer['status'] }}
                        </span>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Telepon</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $customer['phone'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Username</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ '@' . $customer['username'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Alamat utama</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $primaryAddress['label'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Penerima shipment</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $primaryAddress['recipient_name'] ?? '-' }}</p>
                            <p class="mt-1 text-sm text-stone-500">{{ $primaryAddress['recipient_phone'] ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Bergabung</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $customer['joined_at'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-stone-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Order terakhir</p>
                            <p class="mt-2 font-semibold text-stone-900">{{ $customer['last_order'] }}</p>
                        </div>
                    </div>
                </x-admin.form-section>

                <x-admin.address-book
                    :addresses="$addresses"
                    mode="readonly"
                    :description="'Customer ini punya ' . count($addresses) . ' alamat tersimpan untuk kebutuhan checkout dan shipment.'"
                    empty-text="Belum ada alamat pengiriman tersimpan."
                >
                    <x-slot:actions>
                        <a href="{{ route('admin.customers.edit', $customer['code']) }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                            Kelola alamat
                        </a>
                    </x-slot:actions>
                </x-admin.address-book>
            </div>

            <aside class="space-y-6">
                <x-admin.form-section title="Ringkasan customer">
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Total order</span>
                            <span class="font-semibold text-stone-900">{{ $customer['total_orders'] }}</span>
                        </div>
                        <div class="flex items-center justify-between rounded-2xl bg-stone-50 px-4 py-4">
                            <span class="text-stone-500">Total belanja</span>
                            <span class="font-semibold text-stone-900">{{ $customer['total_spent'] }}</span>
                        </div>
                    </div>
                </x-admin.form-section>

                <x-admin.form-section title="Aksi cepat">
                    <div class="mt-5 space-y-3">
                        <a href="{{ route('admin.customers.edit', $customer['code']) }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                            Edit customer
                        </a>
                        <a href="{{ route('admin.customers.create') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-stone-300 bg-white px-5 py-3 text-sm font-medium text-stone-700">
                            Buat customer baru
                        </a>
                    </div>
                </x-admin.form-section>
            </aside>
        </div>
    </section>
</x-layouts.admin>
