<x-layouts.admin :title="'Admin Larashop | Edit ' . $product['name']">
    <section class="space-y-6">
        <x-admin.page-header
            eyebrow="Admin Products"
            title="Edit produk"
            description="Perbarui data produk agar informasi katalog, harga, stok, dan data pengiriman tetap akurat."
        >
            <x-slot:actions>
                <a href="{{ route('admin.products.show', $product['sku']) }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm font-medium text-stone-700">
                    Lihat detail
                </a>
                <button form="product-edit-form" type="submit" class="rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white">
                    Simpan perubahan
                </button>
            </x-slot:actions>
        </x-admin.page-header>

        <form id="product-edit-form" action="{{ route('admin.products.update', $product['sku']) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.products._form', ['product' => $product, 'images' => $images])
        </form>
    </section>
</x-layouts.admin>
