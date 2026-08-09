<x-layouts.customer title="Sobat Akar Tani Kimia | Lupa Password">
    <div class="flex min-h-[60vh] items-center justify-center py-4">
        <div class="relative w-full max-w-[440px] overflow-hidden rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-8 soft-warm-shadow md:p-12">
            <header class="mb-10 flex flex-col items-center text-center">
                <img src="{{ asset('images/logo-circle.png') }}" alt="Sobat Akar Tani Kimia" class="mb-6 h-20 w-20 rounded-full object-cover shadow-lg">
                <h1 class="mb-3 font-headline-lg text-headline-lg text-on-surface">Lupa Password</h1>
                <p class="max-w-[300px] font-body-md text-body-md text-on-surface-variant">Masukkan username, nomor HP, atau email akunmu — kode OTP akan dikirim ke WhatsApp yang terdaftar.</p>
            </header>

            @if ($errors->any())
                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-error-container bg-error-container/40 p-4">
                    <span class="material-symbols-outlined text-[20px] text-on-error-container">error</span>
                    <p class="font-body-sm text-body-sm text-on-error-container">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('password.forgot.send') }}" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label class="ml-1 font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Username / Email / No. HP</label>
                    <input type="text" name="login" value="{{ old('login') }}" placeholder="Contoh: 08123456789"
                        class="w-full rounded-2xl border border-transparent bg-surface-container-low px-5 py-4 font-body-md text-body-md outline-none transition-all duration-200 placeholder:text-outline focus:border-primary focus:ring-4 focus:ring-primary/10">
                </div>

                <button type="submit" class="mt-2 w-full rounded-2xl bg-primary py-4 font-body-md font-bold text-on-primary shadow-lg shadow-primary/20 transition-all duration-200 hover:bg-secondary active:scale-95">
                    Kirim Kode OTP
                </button>
            </form>

            <footer class="mt-10 flex flex-col items-center gap-2 text-center">
                <a href="{{ route('password.reset') }}" class="font-body-sm text-body-sm font-semibold text-primary hover:underline">Sudah punya kode OTP? Masukkan di sini</a>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Kembali ke Masuk</a>
                </p>
            </footer>
        </div>
    </div>
</x-layouts.customer>
