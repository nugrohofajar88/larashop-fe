@php
    $initialAddresses = old('shipping_addresses')
        ? json_decode(old('shipping_addresses'), true)
        : ($addresses ?? ($customer['addresses'] ?? []));
@endphp

<div class="grid gap-6 xl:grid-cols-[0.92fr_1.08fr]">
    <div class="space-y-6">
        <x-admin.form-section title="Informasi customer" description="Fokus ke identitas dasar customer dan status akun.">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-stone-700">Nama customer</label>
                    <input type="text" name="name" value="{{ old('name', $customer['name'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Masukkan nama customer">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Username</label>
                    <input type="text" name="username" value="{{ old('username', $customer['username'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="usernamecustomer">
                    @error('username')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Nomor telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer['phone'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Status akun</label>
                    <select name="status" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                        @foreach ($statuses as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" {{ old('status', $customer['status_key'] ?? 'active') === $statusKey ? 'selected' : '' }}>{{ $statusLabel }}</option>
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
            </div>
        </x-admin.form-section>

        <x-admin.form-section title="Checklist">
            <x-admin.checklist-list :items="[
                'Customer bisa punya beberapa alamat pengiriman sekaligus.',
                'Pastikan satu alamat ditandai sebagai utama untuk checkout dan shipment.',
                'Nomor penerima dan kode pos sebaiknya lengkap agar tim shipping lebih cepat prosesnya.',
            ]" />
        </x-admin.form-section>
    </div>

    <x-admin.address-book
        :addresses="$initialAddresses"
        mode="editable"
        input-name="shipping_addresses"
        :input-value="old('shipping_addresses', json_encode($initialAddresses))"
        description="Daftar alamat dibuat compact agar nyaman dipakai saat review customer."
        empty-text="Belum ada alamat pengiriman. Tambahkan minimal satu alamat agar customer siap dipakai untuk checkout dan shipment."
    />
</div>
