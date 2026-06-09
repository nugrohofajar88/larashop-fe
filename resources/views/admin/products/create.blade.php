<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Tambah Produk">
    <section class="space-y-6">
        <x-admin.page-header
            eyebrow="Admin Products"
            title="Tambah produk baru"
            description="Form ini disiapkan untuk input produk baru dengan data yang sudah siap dipakai di katalog, checkout, dan shipment."
        >
            <x-slot:actions>
                <a href="{{ route('admin.products.index') }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Kembali ke daftar
                </a>
                <button form="product-create-form" type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Simpan produk
                </button>
            </x-slot:actions>
        </x-admin.page-header>

        <form id="product-create-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('admin.products._form', ['product' => [], 'images' => $images])
        </form>
    </section>
</x-layouts.admin>
