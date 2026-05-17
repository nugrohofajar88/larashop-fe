function safeParseVariants(value) {
    if (! value) {
        return [];
    }

    try {
        const parsed = JSON.parse(value);

        return Array.isArray(parsed) ? parsed : [];
    } catch {
        return [];
    }
}

function numberValue(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    return String(value);
}

function buildVariantCard(variant, index) {
    const isDefault = variant.is_default ? 'checked' : '';
    const isActive = variant.is_active !== false ? 'checked' : '';
    const activeLabel = variant.is_active !== false ? 'Aktif' : 'Nonaktif';

    return `
        <article class="rounded-[1.5rem] border border-stone-200 bg-white p-4 shadow-sm" data-variant-item data-index="${index}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-stone-900">Varian ${index + 1}</p>
                    <p class="mt-1 text-xs text-stone-500">SKU, harga, stok, berat, dan dimensi dikunci per varian.</p>
                </div>
                <button type="button" class="rounded-full border border-stone-200 px-3 py-1 text-xs font-semibold text-rose-700" data-variant-remove>
                    Hapus
                </button>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Label varian</label>
                    <input type="text" value="${variant.label ?? ''}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="500gr atau 1kg" data-variant-field="label">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">SKU varian</label>
                    <input type="text" value="${variant.sku ?? ''}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm uppercase outline-none focus:border-emerald-500 focus:bg-white" placeholder="PPK-500" data-variant-field="sku">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Harga jual</label>
                    <input type="number" min="0" value="${numberValue(variant.price ?? variant.price_value)}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="25000" data-variant-field="price">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Harga coret</label>
                    <input type="number" min="0" value="${numberValue(variant.compare_at_price)}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="30000" data-variant-field="compare_at_price">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Stok</label>
                    <input type="number" min="0" value="${numberValue(variant.stock)}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="0" data-variant-field="stock">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Berat (gram)</label>
                    <input type="number" min="0" value="${numberValue(variant.weight_grams)}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="500" data-variant-field="weight_grams">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Panjang (cm)</label>
                    <input type="number" min="0" step="0.01" value="${numberValue(variant.length_cm)}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="20" data-variant-field="length_cm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Lebar (cm)</label>
                    <input type="number" min="0" step="0.01" value="${numberValue(variant.width_cm)}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="15" data-variant-field="width_cm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-stone-700">Tinggi (cm)</label>
                    <input type="number" min="0" step="0.01" value="${numberValue(variant.height_cm)}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:bg-white" placeholder="8" data-variant-field="height_cm">
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <label class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-800">
                    <input type="radio" name="variant_default_choice" ${isDefault} data-variant-default>
                    Jadikan varian default
                </label>
                <label class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-stone-50 px-4 py-2 text-sm font-medium text-stone-700">
                    <input type="checkbox" ${isActive} data-variant-active>
                    ${activeLabel}
                </label>
            </div>
        </article>
    `;
}

function emptyVariant(baseSku = '') {
    return {
        label: '',
        sku: baseSku !== '' ? `${baseSku}-${Date.now().toString().slice(-4)}` : '',
        price: '',
        compare_at_price: '',
        stock: 0,
        weight_grams: '',
        length_cm: '',
        width_cm: '',
        height_cm: '',
        is_default: false,
        is_active: true,
    };
}

function collectVariants(list) {
    return Array.from(list.querySelectorAll('[data-variant-item]')).map((item) => ({
        label: item.querySelector('[data-variant-field="label"]')?.value.trim() ?? '',
        sku: item.querySelector('[data-variant-field="sku"]')?.value.trim().toUpperCase() ?? '',
        price: Number(item.querySelector('[data-variant-field="price"]')?.value || 0),
        compare_at_price: item.querySelector('[data-variant-field="compare_at_price"]')?.value === ''
            ? null
            : Number(item.querySelector('[data-variant-field="compare_at_price"]')?.value || 0),
        stock: Number(item.querySelector('[data-variant-field="stock"]')?.value || 0),
        weight_grams: item.querySelector('[data-variant-field="weight_grams"]')?.value === ''
            ? null
            : Number(item.querySelector('[data-variant-field="weight_grams"]')?.value || 0),
        length_cm: item.querySelector('[data-variant-field="length_cm"]')?.value === ''
            ? null
            : Number(item.querySelector('[data-variant-field="length_cm"]')?.value || 0),
        width_cm: item.querySelector('[data-variant-field="width_cm"]')?.value === ''
            ? null
            : Number(item.querySelector('[data-variant-field="width_cm"]')?.value || 0),
        height_cm: item.querySelector('[data-variant-field="height_cm"]')?.value === ''
            ? null
            : Number(item.querySelector('[data-variant-field="height_cm"]')?.value || 0),
        is_default: item.querySelector('[data-variant-default]')?.checked ?? false,
        is_active: item.querySelector('[data-variant-active]')?.checked ?? false,
    })).filter((variant) => variant.label !== '' || variant.sku !== '');
}

function ensureDefaultVariant(list) {
    const radios = Array.from(list.querySelectorAll('[data-variant-default]'));

    if (radios.length === 0) {
        return;
    }

    if (! radios.some((radio) => radio.checked)) {
        radios[0].checked = true;
    }
}

export function initAdminProductVariants() {
    const root = document.querySelector('[data-product-variants]');

    if (! root) {
        return;
    }

    const list = root.querySelector('[data-variant-list]');
    const payload = root.querySelector('[data-variants-payload]');
    const stateNode = root.querySelector('[data-variants-state]');
    const addButton = root.querySelector('[data-variant-add]');
    const form = root.closest('form');
    const baseSkuInput = form?.querySelector('input[name="sku"]');

    let variants = safeParseVariants(stateNode?.textContent?.trim());

    if (variants.length === 0) {
        variants = [emptyVariant(baseSkuInput?.value.trim().toUpperCase() ?? '')];
        variants[0].is_default = true;
    }

    const sync = () => {
        ensureDefaultVariant(list);
        const normalized = collectVariants(list);

        if (normalized.length === 0) {
            const fallback = emptyVariant(baseSkuInput?.value.trim().toUpperCase() ?? '');
            fallback.is_default = true;
            normalized.push(fallback);
        }

        if (! normalized.some((variant) => variant.is_default)) {
            normalized[0].is_default = true;
        }

        payload.value = JSON.stringify(normalized);
    };

    const render = () => {
        list.innerHTML = variants.map((variant, index) => buildVariantCard(variant, index)).join('');
        sync();
    };

    addButton?.addEventListener('click', () => {
        variants = collectVariants(list);
        variants.push(emptyVariant(baseSkuInput?.value.trim().toUpperCase() ?? ''));
        render();
    });

    list.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-variant-remove]');

        if (removeButton) {
            const item = removeButton.closest('[data-variant-item]');
            const index = Number(item?.dataset.index ?? -1);

            variants = collectVariants(list).filter((_, currentIndex) => currentIndex !== index);

            if (variants.length === 0) {
                variants = [emptyVariant(baseSkuInput?.value.trim().toUpperCase() ?? '')];
                variants[0].is_default = true;
            }

            render();
        }
    });

    list.addEventListener('change', (event) => {
        if (event.target.matches('[data-variant-default]')) {
            const current = event.target.closest('[data-variant-item]');

            list.querySelectorAll('[data-variant-default]').forEach((radio) => {
                radio.checked = radio === event.target;
            });

            current?.querySelector('[data-variant-default]')?.setAttribute('checked', 'checked');
        }

        if (event.target.matches('[data-variant-active]')) {
            const label = event.target.closest('label');
            if (label) {
                label.lastChild.textContent = event.target.checked ? 'Aktif' : 'Nonaktif';
            }
        }

        sync();
    });

    list.addEventListener('input', sync);
    baseSkuInput?.addEventListener('input', sync);
    render();
}
