<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Tambah Account">
    <section class="space-y-6">
        <x-admin.page-header
            eyebrow="Admin Account"
            title="Tambah account admin"
            description="Siapkan akun internal baru untuk operasional tim admin."
        >
            <x-slot:actions>
                <a href="{{ route('admin.accounts.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke daftar
                </a>
                <button form="account-create-form" type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Simpan account
                </button>
            </x-slot:actions>
        </x-admin.page-header>

        <form id="account-create-form" action="{{ route('admin.accounts.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('admin.accounts._form', ['account' => []])
        </form>
    </section>
</x-layouts.admin>
