<x-layouts.customer title="Larashop | Register">
    <section class="mx-auto max-w-xl space-y-6">
        <x-customer-section-title
            eyebrow="Register Customer"
            title="Buat akun customer dengan proses singkat"
            description="Form awal dibuat sederhana agar cepat diisi di mobile dan langsung siap dipakai untuk checkout."
        />

        <div class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
            <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Nama lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Masukkan nama lengkap">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Masukkan username">
                    @error('username')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="opsional@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Password</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Buat password">
                    @error('password')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Konfirmasi password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Ulangi password">
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-stone-900 px-5 py-3.5 text-sm font-semibold text-white">
                    Buat akun
                </button>
            </form>
        </div>
    </section>
</x-layouts.customer>
