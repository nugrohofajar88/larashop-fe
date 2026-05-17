<x-layouts.admin :title="'Admin Larashop | Edit ' . $account['name']">
    <section class="space-y-6">
        <x-admin.page-header
            eyebrow="Admin Account"
            title="Edit account admin"
            description="Perbarui role, status, atau data kontak untuk akun admin yang sudah ada."
        >
            <x-slot:actions>
                <a href="{{ route('admin.accounts.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke daftar
                </a>
                <button form="account-edit-form" type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Simpan perubahan
                </button>
            </x-slot:actions>
        </x-admin.page-header>

        <form id="account-edit-form" action="{{ route('admin.accounts.update', $account['id']) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.accounts._form', ['account' => $account])
        </form>
    </section>
</x-layouts.admin>
