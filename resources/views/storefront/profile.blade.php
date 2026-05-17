<x-layouts.customer title="Larashop | Profil">
    <section class="space-y-6">
        <x-customer-section-title
            eyebrow="Profil Customer"
            title="Profil Saya"
            description="Kelola informasi profil Anda untuk mengontrol, melindungi dan mengamankan akun"
        />

        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)] lg:items-start">
            <x-customer.account-nav />

            <div class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
                @if (session('success'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="grid gap-4" data-customer-profile-form>
                    @csrf
                    @method('PUT')
                    <div class="hidden rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" data-profile-feedback></div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Nama lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $customer['name'] ?? '') }}" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Username <span class="text-rose-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username', $customer['username'] ?? '') }}" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $customer['email'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-stone-700">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $customer['phone'] ?? '') }}" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                    </div>
                    <div id="reset-password" class="rounded-[1.5rem] border border-stone-200 bg-stone-50 p-4">
                        <p class="text-sm font-semibold text-stone-900">Reset password</p>
                        <p class="mt-1 text-xs leading-5 text-stone-500">Kosongkan dua field ini kalau tidak ingin mengubah password.</p>
                        <div class="mt-4 grid gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700">Password baru</label>
                                <div class="relative" data-password-field>
                                    <input type="password" name="password" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 pr-12 text-sm outline-none focus:border-emerald-500 focus:bg-white" data-password-input>
                                    <button type="button" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-stone-400 transition hover:text-stone-700" data-password-toggle aria-label="Tampilkan password" aria-pressed="false">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" data-eye-open>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" data-eye-closed>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0013.42 13.42" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A10.94 10.94 0 0112 5c4.48 0 8.27 2.94 9.54 7a10.96 10.96 0 01-4.04 5.19M6.23 6.23A10.95 10.95 0 002.46 12a10.96 10.96 0 005.31 6.08" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700">Konfirmasi password baru</label>
                                <div class="relative" data-password-field>
                                    <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 pr-12 text-sm outline-none focus:border-emerald-500 focus:bg-white" data-password-input>
                                    <button type="button" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center text-stone-400 transition hover:text-stone-700" data-password-toggle aria-label="Tampilkan password" aria-pressed="false">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" data-eye-open>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" data-eye-closed>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0013.42 13.42" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A10.94 10.94 0 0112 5c4.48 0 8.27 2.94 9.54 7a10.96 10.96 0 01-4.04 5.19M6.23 6.23A10.95 10.95 0 002.46 12a10.96 10.96 0 005.31 6.08" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3.5 text-sm font-semibold text-white" data-profile-submit>
                        Simpan perubahan
                    </button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.customer>
