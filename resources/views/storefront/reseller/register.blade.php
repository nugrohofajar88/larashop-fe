<x-layouts.customer title="Sobat Akar Tani Kimia | Reseller">
    <div class="flex flex-col gap-10 md:flex-row">
        <x-customer.account-nav />

        <section class="flex-1">
            <h1 class="mb-8 font-headline-lg text-headline-lg text-on-surface">Program Reseller</h1>

            @if (session('error'))
                <div class="mb-6 flex items-center gap-3 rounded-xl border border-error-container bg-error-container/40 p-4 text-on-error-container">
                    <span class="material-symbols-outlined">error</span>
                    <p class="font-body-md text-body-md">{{ session('error') }}</p>
                </div>
            @endif

            <div class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow md:p-10">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-4xl text-primary">handshake</span>
                    <div>
                        <h2 class="font-headline-md text-headline-md text-on-surface">Dapat komisi tiap ada pembeli yang belanja lewat kamu</h2>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Daftar gratis, tunggu persetujuan admin, lalu bagikan kode referral kamu.</p>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-surface-container-low p-5">
                        <span class="material-symbols-outlined text-2xl text-primary">person_add</span>
                        <p class="mt-3 font-body-md font-semibold text-on-surface">1. Daftar</p>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Kirim pendaftaran, tunggu disetujui admin.</p>
                    </div>
                    <div class="rounded-2xl bg-surface-container-low p-5">
                        <span class="material-symbols-outlined text-2xl text-primary">share</span>
                        <p class="mt-3 font-body-md font-semibold text-on-surface">2. Bagikan kode</p>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Kamu dapat kode referral unik untuk dibagikan ke calon pembeli.</p>
                    </div>
                    <div class="rounded-2xl bg-surface-container-low p-5">
                        <span class="material-symbols-outlined text-2xl text-primary">payments</span>
                        <p class="mt-3 font-body-md font-semibold text-on-surface">3. Dapat komisi</p>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Setiap pembeli yang kamu referensikan belanja, komisi otomatis masuk saldo kamu.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('reseller.register') }}" class="mt-8">
                    @csrf
                    <button type="submit" class="w-full rounded-full bg-primary px-10 py-4 font-body-md font-bold text-on-primary shadow-lg transition-all duration-200 hover:bg-secondary active:scale-95 md:w-auto">
                        Daftar Jadi Reseller
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-layouts.customer>
