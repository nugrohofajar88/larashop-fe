<x-layouts.customer title="Sobat Akar Tani Kimia | Login Admin">
    <section class="mx-auto max-w-xl space-y-6">
        <x-customer-section-title
            eyebrow="Login Admin"
            title="Masuk ke panel Admin"
            description="Kelola produk, customer, pesanan, dan shipment"
        />

        <div class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm sm:p-6">
            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Username atau email</label>
                    <input type="text" name="login" value="{{ old('login') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Masukkan username atau email admin">
                    @error('login')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Password</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Masukkan password admin">
                    @error('password')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-stone-900 px-5 py-3.5 text-sm font-semibold text-white">
                    Masuk ke admin
                </button>
            </form>
        </div>
    </section>
</x-layouts.customer>
