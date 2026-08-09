<x-layouts.customer title="Sobat Akar Tani Kimia | Masuk">
    <div class="flex min-h-[60vh] items-center justify-center py-4">
        <div class="relative w-full max-w-[440px] overflow-hidden rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-8 soft-warm-shadow md:p-12">
            <header class="mb-10 flex flex-col items-center text-center">
                <img src="{{ asset('images/logo-circle.png') }}" alt="Sobat Akar Tani Kimia" class="mb-6 h-20 w-20 rounded-full object-cover shadow-lg">
                <h1 class="mb-3 font-headline-lg text-headline-lg text-on-surface">Masuk ke Sobat Akar Tani Kimia</h1>
                <p class="max-w-[280px] font-body-md text-body-md text-on-surface-variant">Silakan masuk untuk melanjutkan transaksi pertanianmu.</p>
            </header>

            @if ($errors->any())
                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-error-container bg-error-container/40 p-4">
                    <span class="material-symbols-outlined text-[20px] text-on-error-container">error</span>
                    <p class="font-body-sm text-body-sm text-on-error-container">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Username / Email / No. HP</label>
                    <input type="text" name="login" value="{{ old('login') }}" placeholder="Contoh: buditani@email.com"
                        class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-4 font-body-md text-body-md outline-none transition-all duration-200 placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Password</label>
                        <a href="{{ route('password.forgot') }}" class="mr-1 font-body-sm text-body-sm font-semibold text-primary hover:underline">Lupa password?</a>
                    </div>
                    <div class="relative" data-password-field>
                        <input type="password" name="password" placeholder="Masukkan kata sandi" data-password-input
                            class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-4 pr-14 font-body-md text-body-md outline-none transition-all duration-200 placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                        <button type="button" data-password-toggle aria-label="Tampilkan password" aria-pressed="false"
                            class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-xl text-outline transition-colors hover:text-primary">
                            <span class="material-symbols-outlined" data-eye-open>visibility</span>
                            <span class="material-symbols-outlined hidden" data-eye-closed>visibility_off</span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="mt-2 w-full rounded-2xl bg-primary py-4 font-body-md font-bold text-on-primary shadow-lg shadow-primary/20 transition-all duration-200 hover:bg-secondary active:scale-95">
                    Masuk
                </button>
            </form>

            <footer class="mt-10 text-center">
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="ml-1 font-bold text-primary hover:underline">Daftar</a>
                </p>
            </footer>
        </div>
    </div>
</x-layouts.customer>
