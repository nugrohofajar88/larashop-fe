<x-layouts.customer title="Sobat Akar Tani Kimia | Reseller">
    <div class="flex flex-col gap-10 md:flex-row">
        <x-customer.account-nav />

        <section class="flex-1">
            <h1 class="mb-8 font-headline-lg text-headline-lg text-on-surface">Reseller</h1>

            @if (session('success'))
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-on-secondary-container/10 bg-secondary-container p-4 text-on-secondary-container">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    <p class="font-body-md text-body-md font-medium">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error') || $errors->any())
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-error-container bg-error-container/40 p-4 text-on-error-container">
                    <span class="material-symbols-outlined">error</span>
                    <p class="font-body-md text-body-md">{{ session('error') ?? $errors->first() }}</p>
                </div>
            @endif

            @if ($reseller['status'] === 'pending')
                <div class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow md:p-10">
                    <span class="material-symbols-outlined text-4xl text-tertiary">hourglass_top</span>
                    <h2 class="mt-3 font-headline-md text-headline-md text-on-surface">Menunggu persetujuan admin</h2>
                    <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Pendaftaran reseller kamu sedang ditinjau. Kamu akan bisa bagikan kode referral begitu disetujui.</p>
                </div>
            @elseif ($reseller['status'] === 'rejected')
                <div class="rounded-3xl border border-error-container bg-surface-container-lowest p-6 soft-warm-shadow md:p-10">
                    <span class="material-symbols-outlined text-4xl text-error">cancel</span>
                    <h2 class="mt-3 font-headline-md text-headline-md text-on-surface">Pendaftaran ditolak</h2>
                    @if (!empty($reseller['rejected_reason']))
                        <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Alasan: {{ $reseller['rejected_reason'] }}</p>
                    @endif
                    <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">Hubungi admin lewat WhatsApp toko kalau kamu merasa ini keliru.</p>
                </div>
            @elseif ($reseller['status'] === 'suspended')
                <div class="rounded-3xl border border-error-container bg-surface-container-lowest p-6 soft-warm-shadow md:p-10">
                    <span class="material-symbols-outlined text-4xl text-error">block</span>
                    <h2 class="mt-3 font-headline-md text-headline-md text-on-surface">Akun reseller ditangguhkan</h2>
                    <p class="mt-2 font-body-md text-body-md text-on-surface-variant">Kode referral kamu untuk sementara tidak aktif. Hubungi admin lewat WhatsApp toko untuk info lebih lanjut.</p>
                </div>
            @else
                {{-- Aktif: kode referral, saldo, komisi, pencairan --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                        <p class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Kode referral kamu</p>
                        <p class="mt-2 font-headline-lg text-headline-lg font-bold text-primary">{{ $reseller['referral_code'] }}</p>
                        <div class="mt-4 flex items-center gap-2 rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3">
                            <input type="text" readonly value="{{ $referralUrl }}" data-referral-link
                                class="min-w-0 flex-1 bg-transparent font-body-sm text-body-sm text-on-surface-variant outline-none">
                            <button type="button" data-copy-referral class="shrink-0 rounded-full bg-primary px-4 py-2 text-xs font-bold text-on-primary">Salin</button>
                        </div>
                        <p class="mt-3 font-body-sm text-body-sm text-on-surface-variant">Komisi: {{ $reseller['commission_label'] }} dari total belanja produk pembeli yang kamu referensikan.</p>
                    </div>

                    <div class="rounded-3xl border border-surface-container-highest bg-secondary-container/30 p-6 soft-warm-shadow">
                        <p class="font-label-eyebrow text-label-eyebrow uppercase text-on-secondary-container">Saldo komisi</p>
                        <p class="mt-2 font-headline-lg text-headline-lg font-bold text-on-secondary-container">{{ $reseller['balance'] }}</p>
                        <p class="mt-1 font-body-sm text-body-sm text-on-secondary-container/80">{{ $reseller['referred_count'] }} pembeli tertaut ke kamu.</p>

                        <form method="POST" action="{{ route('reseller.withdrawals.store') }}" class="mt-4 flex items-center gap-2" data-confirm="Ajukan pencairan saldo komisi ini?" data-confirm-ok="Ya, ajukan">
                            @csrf
                            <input type="number" name="amount" min="1" placeholder="Jumlah (Rp)" required
                                class="min-w-0 flex-1 rounded-xl border border-surface-container-highest bg-surface-container-lowest px-4 py-2.5 font-body-sm text-on-surface outline-none focus:border-primary">
                            <button type="submit" class="shrink-0 rounded-full bg-primary px-5 py-2.5 text-sm font-bold text-on-primary">Cairkan</button>
                        </form>
                    </div>
                </div>

                {{-- Riwayat komisi --}}
                <div class="mt-6 rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                    <h2 class="font-headline-md text-headline-md text-on-surface">Riwayat Komisi</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-left font-body-sm text-body-sm">
                            <thead class="text-on-surface-variant">
                                <tr>
                                    <th class="py-2 pr-4 font-medium">Order</th>
                                    <th class="py-2 pr-4 font-medium">Tanggal</th>
                                    <th class="py-2 pr-4 font-medium">Komisi</th>
                                    <th class="py-2 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container-highest">
                                @forelse ($commissions as $commission)
                                    <tr>
                                        <td class="py-3 pr-4 text-on-surface">{{ $commission['order_code'] }}</td>
                                        <td class="py-3 pr-4 text-on-surface-variant">{{ $commission['date'] }}</td>
                                        <td class="py-3 pr-4 font-semibold text-on-surface">{{ $commission['amount'] }}</td>
                                        <td class="py-3">
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $commission['status'] === 'confirmed' ? 'bg-secondary-container text-on-secondary-container' : ($commission['status'] === 'cancelled' ? 'bg-error-container text-on-error-container' : 'bg-surface-container text-on-surface-variant') }}">
                                                {{ $commission['status_label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-on-surface-variant">Belum ada komisi masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Riwayat pencairan --}}
                <div class="mt-6 rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                    <h2 class="font-headline-md text-headline-md text-on-surface">Riwayat Pencairan</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-left font-body-sm text-body-sm">
                            <thead class="text-on-surface-variant">
                                <tr>
                                    <th class="py-2 pr-4 font-medium">Tanggal</th>
                                    <th class="py-2 pr-4 font-medium">Jumlah</th>
                                    <th class="py-2 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container-highest">
                                @forelse ($withdrawals as $withdrawal)
                                    <tr>
                                        <td class="py-3 pr-4 text-on-surface-variant">{{ $withdrawal['requested_at'] }}</td>
                                        <td class="py-3 pr-4 font-semibold text-on-surface">{{ $withdrawal['amount'] }}</td>
                                        <td class="py-3">
                                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $withdrawal['status'] === 'approved' ? 'bg-secondary-container text-on-secondary-container' : ($withdrawal['status'] === 'rejected' ? 'bg-error-container text-on-error-container' : 'bg-surface-container text-on-surface-variant') }}">
                                                {{ $withdrawal['status_label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-6 text-center text-on-surface-variant">Belum ada pengajuan pencairan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    (function () {
                        const btn = document.querySelector('[data-copy-referral]');
                        const input = document.querySelector('[data-referral-link]');
                        if (!btn || !input) return;
                        btn.addEventListener('click', async () => {
                            try {
                                await navigator.clipboard.writeText(input.value);
                                const original = btn.textContent;
                                btn.textContent = 'Tersalin!';
                                setTimeout(() => { btn.textContent = original; }, 1500);
                            } catch (err) {
                                input.select();
                            }
                        });
                    })();
                </script>
            @endif
        </section>
    </div>
</x-layouts.customer>
