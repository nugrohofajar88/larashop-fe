<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Customer">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Customer</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Kelola akun customer terdaftar</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Data customer di sini fokus ke username, nomor kontak, dan kesiapan alamat pengiriman untuk checkout.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.customers.create') }}" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Tambah customer
                </a>
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

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-stone-950">Daftar customer</h2>
                    <p class="mt-1 text-sm text-stone-500">Dummy list customer dengan ringkasan akun dan alamat aktif.</p>
                </div>

                <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-wrap gap-3">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama, username, kode, atau telepon..."
                        class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none placeholder:text-stone-400 focus:border-emerald-500 focus:bg-white lg:w-80"
                    >
                    <select name="status" class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white">
                        <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>Semua status</option>
                        @foreach ($statuses as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" {{ $activeStatus === $statusKey ? 'selected' : '' }}>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">Terapkan</button>
                </form>
            </div>

            <div class="mt-4 flex items-center justify-between gap-4 text-sm text-stone-500">
                <p>Menampilkan {{ count($customers) }} customer{{ $search !== '' || $activeStatus !== 'all' ? ' sesuai filter' : '' }}.</p>
                @if ($search !== '' || $activeStatus !== 'all')
                    <a href="{{ route('admin.customers.index') }}" class="font-semibold text-emerald-700">Reset filter</a>
                @endif
            </div>

            {{-- Desktop: tabel --}}
            <div class="mt-5 hidden overflow-x-auto rounded-2xl border border-stone-200 md:block">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Customer</th>
                            <th class="px-4 py-3 font-medium">Kontak</th>
                            <th class="px-4 py-3 font-medium">Alamat</th>
                            <th class="px-4 py-3 font-medium">Ringkasan</th>
                            <th class="px-4 py-3 font-medium">Order</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <p class="font-semibold text-stone-900">{{ $customer['name'] }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $customer['code'] }}</p>
                                    <p class="mt-2 text-sm text-stone-600">{{ '@' . $customer['username'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top text-stone-700">{{ $customer['phone'] }}</td>
                                <td class="px-4 py-4 align-top text-stone-600">
                                    @php($primaryAddress = collect($customer['addresses'])->firstWhere('is_primary', true) ?? $customer['addresses'][0] ?? null)
                                    @if ($primaryAddress)
                                        <p class="font-medium text-stone-800">{{ $primaryAddress['label'] }} · {{ $primaryAddress['city'] }}</p>
                                        <p class="mt-1 text-xs text-stone-500">{{ $customer['address_count'] }} alamat tersimpan</p>
                                    @else
                                        <p>Belum dimuat di list</p>
                                        <p class="mt-1 text-xs text-stone-500">{{ $customer['address_count'] }} alamat tersimpan</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top text-stone-600">{{ $customer['address_count'] }} alamat</td>
                                <td class="px-4 py-4 align-top text-stone-700">
                                    <p>{{ $customer['total_orders'] }} order</p>
                                    <p class="mt-1 text-xs text-stone-500">{{ $customer['total_spent'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <span class="rounded-full {{ $customer['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : ($customer['status'] === 'Menunggu verifikasi' ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-700') }} px-3 py-1 text-xs font-semibold">
                                        {{ $customer['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.customers.show', $customer['code']) }}" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-medium text-stone-700">Detail</a>
                                        <a href="{{ route('admin.customers.edit', $customer['code']) }}" class="rounded-full bg-stone-900 px-3 py-2 text-xs font-medium text-white">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada customer yang cocok dengan filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile: kartu --}}
            <div class="mt-5 space-y-3 md:hidden">
                @forelse ($customers as $customer)
                    @php($primaryAddress = collect($customer['addresses'])->firstWhere('is_primary', true) ?? $customer['addresses'][0] ?? null)
                    <article class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-stone-900">{{ $customer['name'] }}</p>
                                <p class="mt-0.5 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $customer['code'] }}</p>
                                <p class="mt-1 text-sm text-stone-600">{{ '@' . $customer['username'] }}</p>
                            </div>
                            <span class="shrink-0 rounded-full {{ $customer['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : ($customer['status'] === 'Menunggu verifikasi' ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-700') }} px-2.5 py-1 text-xs font-semibold">
                                {{ $customer['status'] }}
                            </span>
                        </div>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Kontak</dt>
                                <dd class="text-stone-800">{{ $customer['phone'] }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Alamat</dt>
                                <dd class="text-right text-stone-700">{{ $primaryAddress ? $primaryAddress['label'].' · '.$primaryAddress['city'] : 'Belum dimuat' }}<span class="block text-xs text-stone-500">{{ $customer['address_count'] }} alamat</span></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-stone-500">Order</dt>
                                <dd class="text-right text-stone-800">{{ $customer['total_orders'] }} order<span class="block text-xs text-stone-500">{{ $customer['total_spent'] }}</span></dd>
                            </div>
                        </dl>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.customers.show', $customer['code']) }}" class="flex-1 rounded-full border border-stone-300 px-3 py-2 text-center text-xs font-medium text-stone-700">Detail</a>
                            <a href="{{ route('admin.customers.edit', $customer['code']) }}" class="flex-1 rounded-full bg-stone-900 px-3 py-2 text-center text-xs font-medium text-white">Edit</a>
                        </div>
                    </article>
                @empty
                    <p class="rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada customer yang cocok dengan filter saat ini.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts.admin>
