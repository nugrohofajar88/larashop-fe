<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Pencairan Reseller">
    <section class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Reseller</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Pengajuan Pencairan Komisi</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">Setujui lalu transfer manual ke reseller, atau tolak dengan keterangan.</p>
            </div>
            <a href="{{ route('admin.resellers.index') }}" class="rounded-2xl border border-stone-300 px-5 py-3 text-sm font-semibold text-stone-700">
                Daftar Reseller
            </a>
        </div>

        <section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap gap-2">
                @foreach (['all' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $label)
                    <a href="{{ route('admin.reseller-withdrawals.index', $key === 'all' ? [] : ['status' => $key]) }}"
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
                            <th class="px-4 py-3 font-medium">Jumlah</th>
                            <th class="px-4 py-3 font-medium">Diajukan</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @forelse ($withdrawals as $withdrawal)
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <p class="font-semibold text-stone-900">{{ $withdrawal['reseller_name'] }}</p>
                                    <p class="mt-1 font-mono text-xs text-stone-500">{{ $withdrawal['referral_code'] }}</p>
                                </td>
                                <td class="px-4 py-4 align-top font-semibold text-stone-900">{{ $withdrawal['amount'] }}</td>
                                <td class="px-4 py-4 align-top text-stone-500">{{ $withdrawal['requested_at'] }}</td>
                                <td class="px-4 py-4 align-top">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $withdrawal['status'] === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($withdrawal['status'] === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $withdrawal['status_label'] }}
                                    </span>
                                    @if (!empty($withdrawal['admin_note']))
                                        <p class="mt-1 text-xs text-stone-400">{{ $withdrawal['admin_note'] }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4 align-top">
                                    @if ($withdrawal['status'] === 'pending')
                                        <div class="flex flex-wrap gap-2">
                                            <form method="POST" action="{{ route('admin.reseller-withdrawals.approve', $withdrawal['id']) }}" data-confirm="Setujui pencairan {{ $withdrawal['amount'] }} untuk {{ $withdrawal['reseller_name'] }}? Pastikan sudah/akan ditransfer manual." data-confirm-ok="Ya, setujui">
                                                @csrf
                                                <button type="submit" class="rounded-full bg-emerald-600 px-3 py-2 text-xs font-medium text-white">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.reseller-withdrawals.reject', $withdrawal['id']) }}" data-cancel-reason-form>
                                                @csrf
                                                <button type="submit" class="rounded-full border border-rose-200 px-3 py-2 text-xs font-medium text-rose-600">Tolak</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-stone-400">Sudah diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-stone-500">Belum ada pengajuan pencairan sesuai filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</x-layouts.admin>
