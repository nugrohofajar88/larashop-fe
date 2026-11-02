<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Reseller">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Reseller</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Kelola pendaftar & reseller aktif</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                    Setujui pendaftar baru, atur komisi per-reseller, atau tangguhkan reseller yang bermasalah.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.reseller-withdrawals.index') }}" class="rounded-2xl border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">
                    Pengajuan Pencairan
                </a>
                <a href="{{ route('admin.payments.settings') }}#reseller" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Pengaturan Program
                </a>
            </div>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap gap-2">
                @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'active' => 'Aktif', 'rejected' => 'Ditolak', 'suspended' => 'Ditangguhkan'] as $key => $label)
                    <a href="{{ route('admin.resellers.index', $key === 'all' ? [] : ['status' => $key]) }}"
                        class="rounded-full px-4 py-2 text-sm font-semibold {{ $activeStatus === $key ? 'bg-stone-900 text-white' : 'border border-stone-200 text-stone-600 hover:bg-stone-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="mt-5 overflow-x-auto rounded-2xl border border-stone-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-50 text-stone-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Reseller</th>
                            <th class="px-4 py-3 font-medium">Kode Referral</th>
                            <th class="px-4 py-3 font-medium">Komisi</th>
                            <th class="px-4 py-3 font-medium">Saldo</th>
                            <th class="px-4 py-3 font-medium">Referral</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($resellers as $reseller)
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <p class="font-semibold text-stone-900">{{ $reseller['name'] }}</p>
                                    <p class="mt-1 text-xs text-stone-500">{{ $reseller['phone'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top font-mono text-stone-700">{{ $reseller['referral_code'] }}</td>
                                <td class="px-4 py-4 align-top text-stone-700">{{ $reseller['commission_label'] }}</td>
                                <td class="px-4 py-4 align-top font-semibold text-stone-900">{{ $reseller['balance'] }}</td>
                                <td class="px-4 py-4 align-top text-stone-600">{{ $reseller['referred_count'] }} pembeli</td>
                                <td class="px-4 py-4 align-top">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $reseller['status'] === 'active' ? 'bg-emerald-100 text-emerald-700' : ($reseller['status'] === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-700') }}">
                                        {{ $reseller['status_label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.resellers.show', $reseller['id']) }}" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-medium text-stone-700">Detail</a>
                                        @if ($reseller['status'] === 'pending')
                                            <form method="POST" action="{{ route('admin.resellers.approve', $reseller['id']) }}" data-confirm="Setujui pendaftaran reseller {{ $reseller['name'] }}?" data-confirm-ok="Ya, setujui">
                                                @csrf
                                                <button type="submit" class="rounded-full bg-emerald-600 px-3 py-2 text-xs font-medium text-white">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.resellers.reject', $reseller['id']) }}" data-cancel-reason-form>
                                                @csrf
                                                <button type="submit" class="rounded-full border border-rose-200 px-3 py-2 text-xs font-medium text-rose-600">Tolak</button>
                                            </form>
                                        @elseif ($reseller['status'] === 'active')
                                            <form method="POST" action="{{ route('admin.resellers.suspend', $reseller['id']) }}" data-confirm="Tangguhkan reseller {{ $reseller['name'] }}? Kode referralnya tidak bisa dipakai lagi sampai diaktifkan ulang." data-confirm-ok="Ya, tangguhkan">
                                                @csrf
                                                <button type="submit" class="rounded-full border border-rose-200 px-3 py-2 text-xs font-medium text-rose-600">Tangguhkan</button>
                                            </form>
                                        @elseif (in_array($reseller['status'], ['suspended', 'rejected'], true))
                                            <form method="POST" action="{{ route('admin.resellers.reactivate', $reseller['id']) }}" data-confirm="Aktifkan kembali reseller {{ $reseller['name'] }}?" data-confirm-ok="Ya, aktifkan">
                                                @csrf
                                                <button type="submit" class="rounded-full bg-emerald-600 px-3 py-2 text-xs font-medium text-white">Aktifkan</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada reseller yang cocok dengan filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</x-layouts.admin>
