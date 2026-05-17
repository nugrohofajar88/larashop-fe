<div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
    <div class="space-y-6">
        <x-admin.form-section title="Informasi utama" description="Data dasar yang akan tampil di katalog customer.">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-stone-700">Nama produk</label>
                    <input type="text" name="name" value="{{ old('name', $product['name'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="Masukkan nama produk">
                    @error('name')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product['sku'] ?? '') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="PRD-001">
                    @error('sku')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Kategori</label>
                    <select name="category" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                        @foreach ($categories as $categoryKey => $categoryLabel)
                            <option value="{{ $categoryKey }}" {{ old('category', $product['category_key'] ?? '') === $categoryKey ? 'selected' : '' }}>{{ $categoryLabel }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Status publikasi</label>
                    <select name="status" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white">
                        @foreach ($statuses as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" {{ old('status', $product['status_key'] ?? 'draft') === $statusKey ? 'selected' : '' }}>{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-stone-700">Deskripsi singkat</label>
                    <input id="product-description" name="description" type="hidden" value="{{ old('description', $product['description'] ?? '') }}">
                    <div class="trix-shell">
                        <trix-editor input="product-description" placeholder="Tulis deskripsi produk untuk katalog customer"></trix-editor>
                    </div>
                    <p class="mt-2 text-xs text-stone-500">Gunakan heading ringan, bold, bullet list, atau numbered list sesuai kebutuhan deskripsi produk.</p>
                    @error('description')
                        <p class="mt-2 text-sm text-rose-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-admin.form-section>

        <x-admin.form-section title="Varian produk" description="Setiap varian punya SKU, harga, stok, berat, dan dimensi sendiri. Varian default akan dipakai sebagai tampilan utama katalog.">
            @php
                $variantsState = old('variants_json')
                    ? json_decode(old('variants_json'), true)
                    : ($product['variants'] ?? []);
            @endphp

            <div class="space-y-4" data-product-variants>
                <script type="application/json" data-variants-state>@json($variantsState)</script>
                <input type="hidden" name="variants_json" value="{{ old('variants_json', json_encode($variantsState)) }}" data-variants-payload>

                <div class="flex flex-wrap items-center justify-between gap-3 rounded-[1.5rem] border border-dashed border-stone-300 bg-stone-50 px-4 py-4">
                    <div>
                        <p class="text-sm font-semibold text-stone-900">Siapkan beberapa kemasan atau ukuran</p>
                        <p class="mt-1 text-sm text-stone-500">Contoh: 500gr, 1kg, atau paket khusus dengan harga dan dimensi berbeda.</p>
                    </div>
                    <button type="button" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white" data-variant-add>
                        Tambah varian
                    </button>
                </div>

                <div class="space-y-4" data-variant-list></div>

                @error('variants_json')
                    <p class="text-sm text-rose-700">{{ $message }}</p>
                @enderror
            </div>
        </x-admin.form-section>
    </div>

    <div class="space-y-6">
        <x-admin.form-section title="Galeri produk" description="Template multi foto untuk foto utama dan beberapa foto pendukung produk." data-product-gallery>
            <script type="application/json" data-gallery-state>@json($images)</script>
            <input type="hidden" name="primary_image" data-gallery-primary value="">
            <input type="hidden" name="removed_images" data-gallery-removed value="">
            <input type="hidden" name="existing_image_count" value="{{ count($images) }}">
            <input type="file" name="product_images[]" data-gallery-input class="hidden" accept="image/*" multiple>

            <div class="mt-5 rounded-[1.75rem] border border-dashed border-stone-300 bg-stone-50 px-5 py-10 text-center">
                <p class="text-sm font-semibold text-stone-800">Upload beberapa foto produk</p>
                <p class="mt-2 text-sm text-stone-500">Siapkan foto utama, detail kemasan, dan foto pendukung lain jika diperlukan.</p>
                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                    <button type="button" onclick="this.closest('[data-product-gallery]').querySelector('[data-gallery-input]').click()" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-medium text-white">Pilih banyak foto</button>
                    <button type="button" class="rounded-full border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700">Format: JPG, PNG, WEBP</button>
                </div>
            </div>

            <div class="mt-5 space-y-3" data-gallery-list></div>

            <div data-gallery-empty class="rounded-[1.5rem] border border-stone-200 bg-stone-50 px-4 py-5 text-sm text-stone-600">
                Belum ada foto. Area ini akan menampilkan preview beberapa gambar setelah admin memilih file upload.
            </div>

            @error('product_images')
                <p class="mt-3 text-sm text-rose-700">{{ $message }}</p>
            @enderror

            @error('product_images.*')
                <p class="mt-3 text-sm text-rose-700">{{ $message }}</p>
            @enderror

            @error('primary_image')
                <p class="mt-3 text-sm text-rose-700">{{ $message }}</p>
            @enderror
        </x-admin.form-section>

        <x-admin.form-section title="Checklist sebelum simpan">
            <x-admin.checklist-list :items="[
                'Nama produk jelas dan mudah dicari customer.',
                'Minimal satu varian aktif sudah punya SKU, harga, stok, dan berat.',
                'Varian default sudah ditentukan untuk tampilan katalog.',
            ]" />
        </x-admin.form-section>
    </div>
</div>
