<x-layouts.customer title="Sobat Akar Tani Kimia | Profil">
    <div class="flex flex-col gap-10 md:flex-row">
        <x-customer.account-nav />

        <section class="flex-1">
            <h1 class="mb-8 font-headline-lg text-headline-lg text-on-surface">Profil</h1>

            <div class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow md:p-10">
                @if (session('success'))
                    <div class="mb-8 flex items-center gap-3 rounded-xl border border-on-secondary-container/10 bg-secondary-container p-4 text-on-secondary-container">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <p class="font-body-md text-body-md font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-8 flex items-center gap-3 rounded-xl border border-error-container bg-error-container/40 p-4 text-on-error-container">
                        <span class="material-symbols-outlined">error</span>
                        <p class="font-body-md text-body-md">{{ $errors->first() }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-8" data-customer-profile-form>
                    @csrf
                    @method('PUT')
                    <div class="hidden rounded-xl border border-tertiary-fixed bg-tertiary-fixed/40 px-4 py-3 text-body-sm text-on-tertiary-fixed" data-profile-feedback></div>

                    {{-- Identity --}}
                    <div class="grid grid-cols-1 gap-x-gutter gap-y-6 md:grid-cols-2">
                        <div class="space-y-2">
                            <label class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Nama lengkap <span class="text-error">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $customer['name'] ?? '') }}" required
                                class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="space-y-2">
                            <label class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Username <span class="text-error">*</span></label>
                            <input type="text" name="username" value="{{ old('username', $customer['username'] ?? '') }}" required
                                class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="space-y-2">
                            <label class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Email</label>
                            <input type="email" name="email" value="{{ old('email', $customer['email'] ?? '') }}"
                                class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="space-y-2">
                            <label class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Nomor WhatsApp <span class="text-error">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $customer['phone'] ?? '') }}" required
                                class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 font-body-md text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary">
                        </div>
                    </div>

                    <hr class="border-surface-container-highest">

                    {{-- Reset password --}}
                    <div id="reset-password">
                        <p class="font-body-md font-semibold text-on-surface">Reset password</p>
                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Kosongkan dua field ini kalau tidak ingin mengubah password.</p>
                        <div class="mt-4 grid grid-cols-1 gap-x-gutter gap-y-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Password baru</label>
                                <div class="relative" data-password-field>
                                    <input type="password" name="password" placeholder="••••••••" data-password-input
                                        class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 pr-12 font-body-md text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary">
                                    <button type="button" data-password-toggle aria-label="Tampilkan password" aria-pressed="false"
                                        class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-outline transition-colors hover:text-primary">
                                        <span class="material-symbols-outlined" data-eye-open>visibility</span>
                                        <span class="material-symbols-outlined hidden" data-eye-closed>visibility_off</span>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="font-label-eyebrow text-label-eyebrow uppercase text-on-surface-variant">Konfirmasi password baru</label>
                                <div class="relative" data-password-field>
                                    <input type="password" name="password_confirmation" placeholder="••••••••" data-password-input
                                        class="w-full rounded-xl border border-surface-container-highest bg-surface-container-low px-4 py-3 pr-12 font-body-md text-on-surface outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary">
                                    <button type="button" data-password-toggle aria-label="Tampilkan password" aria-pressed="false"
                                        class="absolute right-3 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-outline transition-colors hover:text-primary">
                                        <span class="material-symbols-outlined" data-eye-open>visibility</span>
                                        <span class="material-symbols-outlined hidden" data-eye-closed>visibility_off</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" data-profile-submit
                            class="w-full rounded-full bg-primary px-10 py-4 font-body-md font-bold text-on-primary shadow-lg transition-all duration-200 hover:bg-secondary active:scale-95 md:w-auto">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</x-layouts.customer>
