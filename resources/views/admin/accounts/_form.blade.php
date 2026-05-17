<div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
    <div class="space-y-6">
        <x-admin.form-section title="Informasi account" description="Data dasar untuk akun yang bisa login ke admin panel.">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-stone-700">Nama lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $account['name'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Masukkan nama admin">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $account['email'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="nama@larashop.test">
                    @error('email')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Username</label>
                    <input type="text" name="username" value="{{ old('username', $account['username'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="usernameadmin">
                    @error('username')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Nomor telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $account['phone'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Role</label>
                    <select name="role" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $account['role'] ?? '') === $role ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Status</label>
                    <select name="status" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', $account['status'] ?? 'Aktif') === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Password sementara</label>
                    <input type="password" name="password" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Konfirmasi password</label>
                    <input type="password" name="password_confirmation" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Ulangi password">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-stone-700">Catatan akses</label>
                    <textarea name="note" rows="5" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Catatan internal terkait role atau scope akses">{{ old('note', $account['note'] ?? '') }}</textarea>
                    @error('note')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-admin.form-section>
    </div>
</div>
