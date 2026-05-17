export const initStorefrontCart = () => {
    const page = document.querySelector('[data-cart-page]');
    const summary = document.querySelector('[data-cart-summary]');
    const checkoutButton = document.querySelector('[data-cart-checkout]');
    const toggleAllButton = document.querySelector('[data-cart-toggle-all]');

    if (!page || !summary || !checkoutButton) {
        return;
    }

    const csrfToken = page.getAttribute('data-cart-csrf') || '';
    const updateTemplate = page.getAttribute('data-cart-update-template') || '';
    const deleteTemplate = page.getAttribute('data-cart-delete-template') || '';

    const formatRupiah = (value) => `Rp${new Intl.NumberFormat('id-ID').format(value)}`;

    const endpointFor = (template, id) => template.replace('__ID__', String(id));

    const updateSummary = () => {
        const items = Array.from(page.querySelectorAll('[data-cart-item]'));
        let selectedProductCount = 0;
        let selectedTotalValue = 0;
        let selectedItemCount = 0;

        items.forEach((item) => {
            const selected = item.querySelector('[data-cart-select]')?.checked;
            const qty = Number(item.querySelector('[data-cart-qty]')?.textContent || '0');
            const priceValue = Number(item.getAttribute('data-item-price-value') || '0');

            item.classList.toggle('opacity-70', !selected);

            if (selected) {
                selectedItemCount += 1;
                selectedProductCount += qty;
                selectedTotalValue += priceValue * qty;
            }
        });

        const countNode = summary.querySelector('[data-cart-summary-count]');
        const totalNode = summary.querySelector('[data-cart-summary-total]');

        if (countNode) {
            countNode.textContent = String(selectedProductCount);
        }

        if (totalNode) {
            totalNode.textContent = formatRupiah(selectedTotalValue);
        }

        checkoutButton.classList.toggle('pointer-events-none', selectedProductCount === 0);
        checkoutButton.classList.toggle('opacity-40', selectedProductCount === 0);
        checkoutButton.setAttribute('aria-disabled', selectedProductCount === 0 ? 'true' : 'false');

        if (toggleAllButton) {
            toggleAllButton.textContent = items.length > 0 && selectedItemCount === items.length
                ? 'Batal pilih semua'
                : 'Pilih semua';
        }
    };

    const updateSummaryFromPayload = (payload) => {
        const countNode = summary.querySelector('[data-cart-summary-count]');
        const totalNode = summary.querySelector('[data-cart-summary-total]');

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
        const subtotalNode = item.querySelector('[data-cart-subtotal]');
        if (subtotalNode) {
            subtotalNode.textContent = subtotalText;
        }
    };

    const syncQtyButtons = (item) => {
        const qty = Number(item.querySelector('[data-cart-qty]')?.textContent || '0');
        const stock = Number(item.getAttribute('data-item-stock') || '0');
        const decreaseButton = item.querySelector('[data-cart-decrease]');
        const increaseButton = item.querySelector('[data-cart-increase]');

        if (decreaseButton) {
            decreaseButton.disabled = qty <= 1;
        }

        if (increaseButton) {
            increaseButton.disabled = qty >= stock;
        }
    };

    const bindItem = (item) => {
        const itemId = item.getAttribute('data-item-id');
        const qtyNode = item.querySelector('[data-cart-qty]');
        const selectNode = item.querySelector('[data-cart-select]');
        const decreaseButton = item.querySelector('[data-cart-decrease]');
        const increaseButton = item.querySelector('[data-cart-increase]');
        const removeButton = item.querySelector('[data-cart-remove]');

        selectNode?.addEventListener('change', async () => {
            setItemBusy(item, true);

            try {
                const result = await request(endpointFor(updateTemplate, itemId), 'PUT', {
                    selected: selectNode.checked,
                });

                updateSummaryFromPayload(result.data.summary || {});
            } catch (error) {
                selectNode.checked = !selectNode.checked;
                window.alert(error.message || 'Gagal memperbarui pilihan item.');
            } finally {
                setItemBusy(item, false);
                updateSummary();
            }
        });

        decreaseButton?.addEventListener('click', async () => {
            if (!qtyNode) {
                return;
            }

            const previousQty = Number(qtyNode.textContent || '0');
            const nextQty = Math.max(1, previousQty - 1);

            if (nextQty === previousQty) {
                return;
            }

            qtyNode.textContent = String(nextQty);
            updateItemSubtotal(item, formatRupiah(nextQty * Number(item.getAttribute('data-item-price-value') || '0')));
            syncQtyButtons(item);
            updateSummary();
            setItemBusy(item, true);

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
                updateSummary();
            }
        });

        increaseButton?.addEventListener('click', async () => {
            if (!qtyNode) {
                return;
            }

            const previousQty = Number(qtyNode.textContent || '0');
            const stock = Number(item.getAttribute('data-item-stock') || '0');
            const nextQty = Math.min(stock, previousQty + 1);

            if (nextQty === previousQty) {
                return;
            }

            qtyNode.textContent = String(nextQty);
            updateItemSubtotal(item, formatRupiah(nextQty * Number(item.getAttribute('data-item-price-value') || '0')));
            syncQtyButtons(item);
            updateSummary();
            setItemBusy(item, true);

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
                updateSummary();
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
                return;
            }

            updateSummary();
        });

        syncQtyButtons(item);
    };

    page.querySelectorAll('[data-cart-item]').forEach(bindItem);

    toggleAllButton?.addEventListener('click', async () => {
        const items = Array.from(page.querySelectorAll('[data-cart-item]'));
        const shouldSelectAll = toggleAllButton.textContent !== 'Batal pilih semua';

        toggleAllButton.disabled = true;

        try {
            for (const item of items) {
                const selectNode = item.querySelector('[data-cart-select]');
                const itemId = item.getAttribute('data-item-id');

                if (!selectNode || String(selectNode.checked) === String(shouldSelectAll)) {
                    continue;
                }

                setItemBusy(item, true);
                selectNode.checked = shouldSelectAll;

                try {
                    const result = await request(endpointFor(updateTemplate, itemId), 'PUT', {
                        selected: shouldSelectAll,
                    });

                    updateSummaryFromPayload(result.data.summary || {});
                } finally {
                    setItemBusy(item, false);
                }
            }
        } catch (error) {
            window.alert(error.message || 'Gagal memperbarui pilihan item.');
        } finally {
            toggleAllButton.disabled = false;
            updateSummary();
        }
    });

    updateSummary();
};
