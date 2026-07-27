export const initQuickAdd = () => {
    const toastRoot = document.querySelector('[data-quick-add-toast]');
    const modal = document.querySelector('[data-quick-add-modal]');
    const triggers = document.querySelectorAll('[data-quick-add-trigger]');

    if (!toastRoot || !modal || !triggers.length) {
        return;
    }

    const csrfToken = toastRoot.getAttribute('data-quick-add-csrf') || '';
    const endpoint = toastRoot.getAttribute('data-quick-add-endpoint') || '';
    const placeholder = toastRoot.getAttribute('data-quick-add-placeholder') || '';

    const modalImage = modal.querySelector('[data-quick-add-modal-image]');
    const modalTitle = modal.querySelector('[data-quick-add-modal-title]');
    const modalVariants = modal.querySelector('[data-quick-add-modal-variants]');
    const modalQty = modal.querySelector('[data-quick-add-modal-qty]');
    const modalDecrease = modal.querySelector('[data-quick-add-modal-decrease]');
    const modalIncrease = modal.querySelector('[data-quick-add-modal-increase]');
    const modalError = modal.querySelector('[data-quick-add-modal-error]');
    const modalCancel = modal.querySelector('[data-quick-add-modal-cancel]');
    const modalClose = modal.querySelector('[data-quick-add-modal-close]');
    const modalConfirm = modal.querySelector('[data-quick-add-modal-confirm]');

    let activeProduct = null;
    let activeVariantId = null;

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        activeProduct = null;
        activeVariantId = null;
        modalError.classList.add('hidden');
    };

    const updateCartBadges = (count) => {
        const value = Number(count) || 0;
        document.querySelectorAll('[data-cart-badge]').forEach((badge) => {
            badge.textContent = value > 99 ? '99+' : String(value);
            badge.classList.toggle('hidden', value <= 0);
        });
    };

    const enqueueToast = (variant, message) => {
        const isError = variant === 'error';
        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 text-body-sm shadow-lg transition-opacity duration-300 ${
            isError
                ? 'border-error-container bg-error-container/90 text-on-error-container'
                : 'border-secondary-container bg-secondary-container/90 text-on-secondary-container'
        }`;

        const icon = document.createElement('span');
        icon.className = 'material-symbols-outlined text-xl';
        icon.textContent = isError ? 'error' : 'check_circle';

        const text = document.createElement('span');
        text.textContent = message;

        toast.append(icon, text);
        toastRoot.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    const findVariant = (product, variantId) => (product.variants || []).find((v) => v.id === variantId);

    const submitAdd = async (product, variantId, quantity, onError) => {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    product_id: product.id,
                    product_slug: product.slug,
                    product_variant_id: variantId,
                    quantity,
                }),
            });

            const body = await response.json();

            if (!response.ok) {
                throw new Error(body.message || 'Gagal menambahkan ke keranjang.');
            }

            updateCartBadges(body.data?.summary?.selected_product_count);

            const variant = findVariant(product, variantId);
            const label = variant && (product.variants || []).length > 1 ? ` (${variant.label})` : '';
            enqueueToast('success', `${product.name}${label} ditambahkan ke keranjang.`);

            return true;
        } catch (error) {
            const message = error.message || 'Gagal menambahkan ke keranjang.';
            if (onError) {
                onError(message);
            } else {
                enqueueToast('error', message);
            }

            return false;
        }
    };

    const clampQty = () => {
        const variant = findVariant(activeProduct, activeVariantId);
        const stock = Number(variant?.stock || 0);
        let qty = Number(modalQty.textContent) || 1;
        qty = Math.max(1, Math.min(qty, stock || 1));
        modalQty.textContent = String(qty);
        modalDecrease.disabled = qty <= 1;
        modalIncrease.disabled = qty >= stock;
    };

    const renderVariants = (product) => {
        modalVariants.innerHTML = '';

        const rows = [];
        const selectRow = (variantId) => {
            rows.forEach((row) => row.setSelected(row.variant.id === variantId));
        };

        product.variants.forEach((variant) => {
            const outOfStock = Number(variant.stock) <= 0;
            const label = document.createElement('label');
            label.className = `flex cursor-pointer items-center justify-between rounded-xl border px-4 py-3 transition-colors ${
                outOfStock ? 'cursor-not-allowed opacity-50' : 'hover:border-orange-500'
            } border-surface-container-highest`;

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'quick_add_variant';
            radio.className = 'peer sr-only';
            radio.value = String(variant.id);
            radio.disabled = outOfStock;

            const text = document.createElement('span');
            text.className = 'text-sm font-semibold text-on-surface';
            text.textContent = variant.label;

            const price = document.createElement('span');
            price.className = 'text-sm font-bold text-larashop-rose';
            price.textContent = variant.price;

            label.append(radio, text, price);

            const setSelected = (selected) => {
                label.classList.toggle('border-orange-500', selected);
                label.classList.toggle('bg-orange-500', selected);
                label.classList.toggle('border-surface-container-highest', !selected);
                text.classList.toggle('text-white', selected);
                text.classList.toggle('text-on-surface', !selected);
                price.classList.toggle('text-white', selected);
                price.classList.toggle('text-larashop-rose', !selected);
            };

            rows.push({ variant, setSelected });

            radio.addEventListener('change', () => {
                activeVariantId = variant.id;
                selectRow(variant.id);
                modalQty.textContent = '1';
                clampQty();
                modalError.classList.add('hidden');
            });

            if (!outOfStock && activeVariantId === null) {
                activeVariantId = variant.id;
                radio.checked = true;
            }

            modalVariants.appendChild(label);
        });

        selectRow(activeVariantId);
    };

    const openVariantModal = (product) => {
        activeProduct = product;
        activeVariantId = null;
        modalQty.textContent = '1';
        modalError.classList.add('hidden');
        modalTitle.textContent = product.name;
        modalImage.src = product.image || placeholder;
        renderVariants(product);
        clampQty();
        openModal();
    };

    modalDecrease.addEventListener('click', () => {
        modalQty.textContent = String(Math.max(1, Number(modalQty.textContent) - 1));
        clampQty();
    });

    modalIncrease.addEventListener('click', () => {
        modalQty.textContent = String(Number(modalQty.textContent) + 1);
        clampQty();
    });

    modalConfirm.addEventListener('click', async () => {
        if (!activeProduct || activeVariantId === null) {
            modalError.textContent = 'Pilih varian terlebih dahulu.';
            modalError.classList.remove('hidden');
            return;
        }

        const quantity = Number(modalQty.textContent) || 1;
        modalConfirm.disabled = true;

        const ok = await submitAdd(activeProduct, activeVariantId, quantity, (message) => {
            modalError.textContent = message;
            modalError.classList.remove('hidden');
        });

        modalConfirm.disabled = false;

        if (ok) {
            closeModal();
        }
    });

    modalCancel.addEventListener('click', closeModal);
    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            if (trigger.disabled) {
                return;
            }

            let product;

            try {
                product = JSON.parse(trigger.getAttribute('data-product') || '{}');
            } catch {
                enqueueToast('error', 'Data produk tidak valid.');
                return;
            }

            const variants = product.variants || [];

            if (variants.length <= 1) {
                const variant = variants[0];

                if (variant && Number(variant.stock) <= 0) {
                    enqueueToast('error', 'Stok produk ini sedang kosong.');
                    return;
                }

                submitAdd(product, variant ? variant.id : null, 1);
                return;
            }

            openVariantModal(product);
        });
    });
};
