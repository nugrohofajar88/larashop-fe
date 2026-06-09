<x-layouts.admin :title="'Admin Sobat Akar Tani Kimia | Edit ' . $customer['name']">
    <section class="space-y-6">
        <x-admin.page-header
            eyebrow="Admin Customer"
            title="Edit akun customer"
            description="Perbarui status, data kontak, atau alamat customer yang sudah terdaftar."
        >
            <x-slot:actions>
                <a href="{{ route('admin.customers.show', $customer['code']) }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke detail
                </a>
                <button form="customer-edit-form" type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Simpan perubahan
                </button>
            </x-slot:actions>
        </x-admin.page-header>

        <form id="customer-edit-form" action="{{ route('admin.customers.update', $customer['code']) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.customers._form', ['customer' => $customer, 'addresses' => $addresses])
        </form>
    </section>
</x-layouts.admin>
