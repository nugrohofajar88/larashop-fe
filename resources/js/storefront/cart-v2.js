export const initStorefrontCartV2 = () => {
    const page = document.querySelector('[data-cart-v2-page]');
    const summary = document.querySelector('[data-cart-v2-summary]');
    const checkoutButton = document.querySelector('[data-cart-v2-checkout]');

    if (!page || !summary || !checkoutButton) {
        return;
    }

    const csrfToken = page.getAttribute('data-cart-v2-csrf') || '';
    const updateTemplate = page.getAttribute('data-cart-v2-update-template') || '';
    const deleteTemplate = page.getAttribute('data-cart-v2-delete-template') || '';

    const formatRupiah = (value) => `Rp${new Intl.NumberFormat('id-ID').format(value)}`;

    const endpointFor = (template, id) => template.replace('__ID__', String(id));

    const setItemBusy = (item, busy) => {
        item.classList.toggle('pointer-events-none', busy);
        item.classList.toggle('opacity-70', busy);
    };

    const request = async (url, method, payload = null) => {
        const response = await fetch(url, {
            method,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: payload ? JSON.stringify(payload) : null,
        });

        const body = await response.json();

        if (!response.ok) {
            throw new Error(body.message || 'Gagal memperbarui keranjang.');
        }

        return body;
    };

    const updateItemSubtotal = (item, subtotalText) => {
        const subtotalNode = item.querySelector('[data-cart-v2-subtotal]');
        if (subtotalNode) {
            subtotalNode.textContent = subtotalText;
        }
    };

    const updateSummaryFromPayload = (payload) => {
        const countNode = summary.querySelector('[data-cart-v2-summary-count]');
        const totalNode = summary.querySelector('[data-cart-v2-summary-total]');

        if (countNode) {
            countNode.textContent = String(payload.selected_product_count ?? 0);
        }

        if (totalNode) {
            totalNode.textContent = payload.selected_total || formatRupiah(payload.selected_total_value ?? 0);
        }

        const selectedProductCount = Number(payload.selected_product_count ?? 0);
        checkoutButton.classList.toggle('pointer-events-none', selectedProductCount === 0);
        checkoutButton.classList.toggle('opacity-40', selectedProductCount === 0);
        checkoutButton.setAttribute('aria-disabled', selectedProductCount === 0 ? 'true' : 'false');
    };

    const syncQtyButtons = (item) => {
        const qty = Number(item.querySelector('[data-cart-v2-qty]')?.textContent || '0');
        const stock = Number(item.getAttribute('data-item-stock') || '0');
        const decreaseButton = item.querySelector('[data-cart-v2-decrease]');
        const increaseButton = item.querySelector('[data-cart-v2-increase]');

        if (decreaseButton) {
            decreaseButton.disabled = qty <= 1;
        }

        if (increaseButton) {
            increaseButton.disabled = qty >= stock;
        }
    };

    const changeQty = async (item, qtyNode, nextQty, previousQty) => {
        qtyNode.textContent = String(nextQty);
        updateItemSubtotal(item, formatRupiah(nextQty * Number(item.getAttribute('data-item-price-value') || '0')));
        syncQtyButtons(item);
        setItemBusy(item, true);

        const itemId = item.getAttribute('data-item-id');

        try {
            const result = await request(endpointFor(updateTemplate, itemId), 'PUT', {
                quantity: nextQty,
            });

            const data = result.data.item || {};
            qtyNode.textContent = String(data.qty ?? nextQty);
            updateItemSubtotal(item, data.subtotal || formatRupiah((data.qty ?? nextQty) * Number(item.getAttribute('data-item-price-value') || '0')));
            updateSummaryFromPayload(result.data.summary || {});
        } catch (error) {
            qtyNode.textContent = String(previousQty);
            updateItemSubtotal(item, formatRupiah(previousQty * Number(item.getAttribute('data-item-price-value') || '0')));
            window.alert(error.message || 'Gagal memperbarui jumlah item.');
        } finally {
            setItemBusy(item, false);
            syncQtyButtons(item);
        }
    };

    const bindItem = (item) => {
        const itemId = item.getAttribute('data-item-id');
        const qtyNode = item.querySelector('[data-cart-v2-qty]');
        const decreaseButton = item.querySelector('[data-cart-v2-decrease]');
        const increaseButton = item.querySelector('[data-cart-v2-increase]');
        const removeButton = item.querySelector('[data-cart-v2-remove]');

        decreaseButton?.addEventListener('click', () => {
            if (!qtyNode) {
                return;
            }

            const previousQty = Number(qtyNode.textContent || '0');
            const nextQty = Math.max(1, previousQty - 1);

            if (nextQty !== previousQty) {
                changeQty(item, qtyNode, nextQty, previousQty);
            }
        });

        increaseButton?.addEventListener('click', () => {
            if (!qtyNode) {
                return;
            }

            const previousQty = Number(qtyNode.textContent || '0');
            const stock = Number(item.getAttribute('data-item-stock') || '0');
            const nextQty = Math.min(stock, previousQty + 1);

            if (nextQty !== previousQty) {
                changeQty(item, qtyNode, nextQty, previousQty);
            }
        });

        removeButton?.addEventListener('click', async () => {
            setItemBusy(item, true);

            try {
                const result = await request(endpointFor(deleteTemplate, itemId), 'DELETE');
                item.remove();
                updateSummaryFromPayload(result.data.summary || {});
            } catch (error) {
                setItemBusy(item, false);
                window.alert(error.message || 'Gagal menghapus item.');
            }
        });

        syncQtyButtons(item);
    };

    page.querySelectorAll('[data-cart-v2-item]').forEach(bindItem);
};
