<x-layouts.admin title="Admin Sobat Akar Tani Kimia | Kategori">
    @php
        $inputClass = 'w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-stone-800 outline-none focus:border-emerald-500 focus:bg-white';
        $labelClass = 'mb-1 block text-sm font-medium text-stone-700';
        $checkClass = 'h-5 w-5 rounded border-stone-300 text-emerald-600 focus:ring-emerald-500';
    @endphp

    <section class="space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Admin Kategori</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Kelola kategori produk</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">Tambah, ubah, aktif/nonaktifkan, dan atur urutan kategori. Dipakai di form produk &amp; filter katalog.</p>
        </div>

        @error('category')
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700">{{ $message }}</div>
        @enderror

        {{-- Tambah kategori --}}
        <details class="rounded-3xl border border-emerald-200 bg-emerald-50/40 p-5" {{ $errors->any() && old('name') ? 'open' : '' }}>
            <summary class="inline-flex cursor-pointer select-none items-center gap-2 text-sm font-semibold text-emerald-800">+ Tambah kategori</summary>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <div>
                    <label class="{{ $labelClass }}">Nama <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="{{ $inputClass }}" required>
                </div>
                <div>
                    <label class="{{ $labelClass }}">Slug (opsional)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="otomatis dari nama" class="{{ $inputClass }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="{{ $labelClass }}">Deskripsi (opsional)</label>
                    <input type="text" name="description" value="{{ old('description') }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}">Urutan</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="{{ $inputClass }}">
                </div>
                <label class="flex items-center gap-3 self-end rounded-2xl border border-stone-200 bg-white px-4 py-3">
                    <input type="checkbox" name="is_active" value="1" checked class="{{ $checkClass }}">
                    <span class="text-sm font-medium text-stone-800">Aktif</span>
                </label>
                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-2xl bg-stone-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-stone-800">Simpan kategori</button>
                </div>
            </form>
        </details>

        {{-- Daftar kategori --}}
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($categories as $cat)
                <article data-category-card class="rounded-3xl border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-stone-900">{{ $cat['name'] }}</p>
                            <p class="mt-0.5 text-xs uppercase tracking-[0.18em] text-stone-500">{{ $cat['slug'] }}</p>
                            @if (! empty($cat['description']))
                                <p class="mt-1 text-sm text-stone-600">{{ $cat['description'] }}</p>
                            @endif
                            <p class="mt-2 text-xs text-stone-500">{{ $cat['products_count'] }} produk · urutan {{ $cat['sort_order'] }}</p>
                        </div>
                        <span class="shrink-0 rounded-full {{ $cat['is_active'] ? 'bg-emerald-100 text-emerald-700' : 'bg-stone-100 text-stone-600' }} px-2.5 py-1 text-xs font-semibold">
                            {{ $cat['is_active'] ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button type="button" onclick="this.closest('[data-category-card]').querySelector('[data-edit-form]').classList.toggle('hidden')" class="flex-1 rounded-full bg-stone-900 px-3 py-2 text-center text-xs font-medium text-white">Edit</button>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat['id']) }}" onsubmit="return confirm('Hapus kategori {{ $cat['name'] }}?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-full border border-rose-200 px-3 py-2 text-xs font-medium text-rose-600">Hapus</button>
                        </form>
                    </div>

                    <form data-edit-form method="POST" action="{{ route('admin.categories.update', $cat['id']) }}" class="mt-4 hidden space-y-3 border-t border-stone-100 pt-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="{{ $labelClass }}">Nama</label>
                            <input type="text" name="name" value="{{ $cat['name'] }}" class="{{ $inputClass }}" required>
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Slug</label>
                            <input type="text" name="slug" value="{{ $cat['slug'] }}" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Deskripsi</label>
                            <input type="text" name="description" value="{{ $cat['description'] }}" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Urutan</label>
                            <input type="number" name="sort_order" value="{{ $cat['sort_order'] }}" min="0" class="{{ $inputClass }}">
                        </div>
                        <label class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
                            <input type="checkbox" name="is_active" value="1" {{ $cat['is_active'] ? 'checked' : '' }} class="{{ $checkClass }}">
                            <span class="text-sm font-medium text-stone-800">Aktif</span>
                        </label>
                        <button type="submit" class="rounded-2xl bg-stone-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-stone-800">Simpan perubahan</button>
                    </form>
                </article>
            @empty
                <p class="col-span-full rounded-2xl border border-stone-200 bg-white px-4 py-8 text-center text-sm text-stone-500">Belum ada kategori.</p>
            @endforelse
        </div>
    </section>
</x-layouts.admin>
