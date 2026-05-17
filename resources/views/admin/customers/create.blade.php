<x-layouts.admin title="Admin Larashop | Tambah Customer">
    <section class="space-y-6">
        <x-admin.page-header
            eyebrow="Admin Customer"
            title="Tambah akun customer"
            description="Admin bisa membuat akun customer secara manual untuk kebutuhan onboarding atau penjualan langsung."
        >
            <x-slot:actions>
                <a href="{{ route('admin.customers.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke daftar
                </a>
                <button form="customer-create-form" type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Simpan customer
                </button>
            </x-slot:actions>
        </x-admin.page-header>

        <form id="customer-create-form" action="{{ route('admin.customers.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.customers._form', ['customer' => [], 'addresses' => []])
        </form>
    </section>
</x-layouts.admin>
