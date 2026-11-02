<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Detail Reseller">
    @php($inputClass = 'w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white')
    @php($labelClass = 'mb-1 block text-xs font-semibold uppercase tracking-wider text-stone-500')

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-col gap-3 rounded-3xl border border-stone-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('admin.resellers.index') }}" class="text-sm font-semibold text-emerald-700">&larr; Kembali ke daftar reseller</a>
                <h1 class="mt-2 text-2xl font-bold text-stone-900">{{ $reseller['name'] }}</h1>
                <p class="mt-1 text-sm text-stone-500">{{ $reseller['phone'] }} @if ($reseller['email']) · {{ $reseller['email'] }} @endif</p>
            </div>
            <span class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold {{ $reseller['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : ($reseller['status'] === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-700') }}">
                {{ $reseller['status_label'] }}
            </span>
        </div>

        @if (!empty($reseller['rejected_reason']))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Alasan ditolak: {{ $reseller['rejected_reason'] }}</div>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <p class="{{ $labelClass }}">Kode Referral</p>
                <p class="mt-1 font-mono text-lg font-bold text-stone-900">{{ $reseller['referral_code'] }}</p>
            </div>
            <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <p class="{{ $labelClass }}">Saldo Komisi</p>
                <p class="mt-1 text-lg font-bold text-stone-900">{{ $reseller['balance'] }}</p>
            </div>
            <div class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                <p class="{{ $labelClass }}">Pembeli Tertaut</p>
                <p class="mt-1 text-lg font-bold text-stone-900">{{ $reseller['referred_count'] }}</p>
            </div>
        </div>

        {{-- Aksi status --}}
        <div class="flex flex-wrap gap-3 rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
            @if ($reseller['status'] === 'pending')
                <form method="POST" action="{{ route('admin.resellers.approve', $reseller['id']) }}" data-confirm="Setujui pendaftaran reseller ini?" data-confirm-ok="Ya, setujui">
                    @csrf
                    <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white">Setujui</button>
                </form>
                <form method="POST" action="{{ route('admin.resellers.reject', $reseller['id']) }}" data-cancel-reason-form>
                    @csrf
                    <button type="submit" class="rounded-2xl border border-rose-200 px-5 py-2.5 text-sm font-semibold text-rose-600">Tolak</button>
                </form>
            @elseif ($reseller['status'] === 'active')
                <form method="POST" action="{{ route('admin.resellers.suspend', $reseller['id']) }}" data-confirm="Tangguhkan reseller ini? Kode referralnya tidak bisa dipakai lagi sampai diaktifkan ulang." data-confirm-ok="Ya, tangguhkan">
                    @csrf
                    <button type="submit" class="rounded-2xl border border-rose-200 px-5 py-2.5 text-sm font-semibold text-rose-600">Tangguhkan</button>
                </form>
            @elseif (in_array($reseller['status'], ['suspended', 'rejected'], true))
                <form method="POST" action="{{ route('admin.resellers.reactivate', $reseller['id']) }}" data-confirm="Aktifkan kembali reseller ini?" data-confirm-ok="Ya, aktifkan">
                    @csrf
                    <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white">Aktifkan Kembali</button>
                </form>
            @endif
        </div>

        {{-- Atur komisi --}}
        <form method="POST" action="{{ route('admin.resellers.commission.update', $reseller['id']) }}" class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-semibold text-stone-900">Aturan Komisi</h2>
            <p class="mt-1 text-sm text-stone-500">Berlaku untuk transaksi baru yang direferensikan reseller ini sejak disimpan.</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Jenis Komisi</label>
                    <select name="commission_type" class="{{ $inputClass }}">
                        <option value="percentage" {{ $reseller['commission_type'] === 'percentage' ? 'selected' : '' }}>Persen (%)</option>
                        <option value="fixed" {{ $reseller['commission_type'] === 'fixed' ? 'selected' : '' }}>Nominal tetap (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Nilai Komisi</label>
                    <input type="number" name="commission_value" min="0" value="{{ $reseller['commission_value'] }}" class="{{ $inputClass }}">
                </div>
            </div>
            <button type="submit" class="mt-4 rounded-2xl bg-stone-900 px-6 py-3 text-sm font-semibold text-white">Simpan Aturan Komisi</button>
        </form>

        {{-- Riwayat komisi --}}
        <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-stone-900">Riwayat Komisi</h2>
            <div class="mt-4 overflow-x-auto rounded-2xl border border-stone-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Order</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Komisi</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($reseller['commissions'] ?? [] as $commission)
                            <tr>
                                <td class="px-4 py-3">{{ $commission['order_code'] }}</td>
                                <td class="px-4 py-3 text-stone-500">{{ $commission['date'] }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $commission['amount'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $commission['status'] === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : ($commission['status'] === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-stone-100 text-stone-600') }}">
                                        {{ $commission['status_label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-stone-500">Belum ada komisi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Riwayat pencairan --}}
        <div class="rounded-3xl border border-stone-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-stone-900">Riwayat Pencairan</h2>
            <div class="mt-4 overflow-x-auto rounded-2xl border border-stone-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Jumlah</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($reseller['withdrawals'] ?? [] as $withdrawal)
                            <tr>
                                <td class="px-4 py-3 text-stone-500">{{ $withdrawal['requested_at'] }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $withdrawal['amount'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $withdrawal['status'] === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($withdrawal['status'] === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-stone-100 text-stone-600') }}">
                                        {{ $withdrawal['status_label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-stone-500">Belum ada pengajuan pencairan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
