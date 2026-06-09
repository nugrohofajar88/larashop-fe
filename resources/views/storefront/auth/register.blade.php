<x-layouts.customer title="Sobat Akar Tani Kimia | Daftar">
    <div class="flex min-h-[60vh] items-center justify-center py-4">
        <div class="relative w-full max-w-[480px] overflow-hidden rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-8 soft-warm-shadow md:p-12">
            <header class="mb-8 flex flex-col items-center text-center">
                <img src="{{ asset('images/logo-circle.png') }}" alt="Sobat Akar Tani Kimia" class="mb-6 h-20 w-20 rounded-full object-cover shadow-lg">
                <h1 class="mb-3 font-headline-lg text-headline-lg text-on-surface">Buat akun Sobat Akar Tani Kimia</h1>
                <p class="max-w-[300px] font-body-md text-body-md text-on-surface-variant">Proses singkat, langsung siap dipakai untuk checkout.</p>
            </header>

            @if ($errors->any())
                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-error-container bg-error-container/40 p-4">
                    <span class="material-symbols-outlined text-[20px] text-on-error-container">error</span>
                    <p class="font-body-sm text-body-sm text-on-error-container">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Nama lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap"
                        class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-3.5 font-body-md text-body-md outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                </div>
                <div class="space-y-2">
                    <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username"
                        class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-3.5 font-body-md text-body-md outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                </div>
                <div class="space-y-2">
                    <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                        class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-3.5 font-body-md text-body-md outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                </div>
                <div class="space-y-2">
                    <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Email <span class="lowercase text-outline">(opsional)</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="opsional@email.com"
                        class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-3.5 font-body-md text-body-md outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                </div>
                <div class="space-y-2">
                    <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Password</label>
                    <div class="relative" data-password-field>
                        <input type="password" name="password" placeholder="Buat password" data-password-input
                            class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-3.5 pr-14 font-body-md text-body-md outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                        <button type="button" data-password-toggle aria-label="Tampilkan password" aria-pressed="false"
                            class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-xl text-outline transition-colors hover:text-primary">
                            <span class="material-symbols-outlined" data-eye-open>visibility</span>
                            <span class="material-symbols-outlined hidden" data-eye-closed>visibility_off</span>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Konfirmasi password</label>
                    <div class="relative" data-password-field>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password" data-password-input
                            class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-3.5 pr-14 font-body-md text-body-md outline-none transition-all placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                        <button type="button" data-password-toggle aria-label="Tampilkan password" aria-pressed="false"
                            class="absolute right-3 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-xl text-outline transition-colors hover:text-primary">
                            <span class="material-symbols-outlined" data-eye-open>visibility</span>
                            <span class="material-symbols-outlined hidden" data-eye-closed>visibility_off</span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="mt-2 w-full rounded-2xl bg-on-background py-4 font-body-md font-bold text-on-primary shadow-lg transition-all duration-200 hover:bg-primary active:scale-95">
                    Buat akun
                </button>
            </form>

            <footer class="mt-8 text-center">
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="ml-1 font-bold text-primary hover:underline">Masuk</a>
                </p>
            </footer>
        </div>
    </div>
</x-layouts.customer>
