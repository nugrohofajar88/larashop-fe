export const initCheckoutSelector = () => {
    const page = document.querySelector('[data-checkout-page]');
    const loadingBanner = document.querySelector('[data-checkout-loading]');
    const summary = document.querySelector('[data-checkout-summary]');
    const addressRoot = document.querySelector('[data-checkout-selector="address"]');
    const shippingRoot = document.querySelector('[data-checkout-selector="shipping"]');
    const orderForm = document.querySelector('[data-checkout-order-form]');
    const addressIdField = orderForm?.querySelector('[data-checkout-address-id]');
    const shippingOptionIdField = orderForm?.querySelector('[data-checkout-shipping-option-id]');
    const useUniqueCodeBalanceField = orderForm?.querySelector('[data-checkout-use-unique-code-balance]');
    const paymentMethodField = orderForm?.querySelector('[data-checkout-payment-method]');
    const paymentSection = document.querySelector('[data-payment-method-section]');
    const codOption = paymentSection?.querySelector('[data-payment-option="cod"]');
    const codMaxAmount = Number(paymentSection?.getAttribute('data-cod-max-amount') || '5000000');
    const totalLabel = summary?.querySelector('[data-summary-total-label]');
    const paymentNote = document.querySelector('[data-payment-note]');

    if (!addressRoot || !shippingRoot || !summary) {
        return;
    }

    const formatRupiah = (value) => `Rp${new Intl.NumberFormat('id-ID').format(value)}`;

    const escapeHtml = (value) =>
        String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

    const loadingState = {
        active: false,
    };

    const getSelectedAddressId = () => {
        const selected = Array.from(addressRoot.querySelectorAll('[data-selector-input]')).find((input) => input.checked);

        return selected?.value || '';
    };

    const getSelectedShippingOptionId = () => {
        const inputs = Array.from(shippingRoot.querySelectorAll('[data-selector-input]'));
        const selected = inputs.find((input) => input.checked) || inputs[0];

        return selected?.getAttribute('data-option-id') || '';
    };

    const usesUniqueCodeBalance = () => {
        const checkbox = page.querySelector('[data-use-unique-code-balance]');

        return Boolean(checkbox?.checked);
    };

    const setLoading = (active) => {
        loadingState.active = active;

        if (loadingBanner) {
            loadingBanner.classList.toggle('hidden', !active);
        }

        [addressRoot, shippingRoot].forEach((root) => {
            const openButton = root.querySelector('[data-selector-open]');
            const applyButton = root.querySelector('[data-selector-apply]');

            if (openButton) {
                openButton.disabled = active;
                openButton.classList.toggle('opacity-60', active);
                openButton.classList.toggle('pointer-events-none', active);
            }

            if (applyButton) {
                applyButton.disabled = active;
                applyButton.classList.toggle('opacity-60', active);
                applyButton.classList.toggle('pointer-events-none', active);
            }
        });
    };

    const closeModal = (root) => {
        const modal = root.querySelector('[data-selector-modal]');

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    };

    const openModal = (root) => {
        const modal = root.querySelector('[data-selector-modal]');

        if (!modal || loadingState.active) {
            return;
        }

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };

    const syncOptionStyles = (root) => {
        root.querySelectorAll('[data-selector-option]').forEach((option) => {
            const input = option.querySelector('[data-selector-input]');
            const isChecked = Boolean(input?.checked);

            option.classList.toggle('border-emerald-500', isChecked);
            option.classList.toggle('bg-emerald-50/50', isChecked);
            option.classList.toggle('border-stone-200', !isChecked);
            option.classList.toggle('bg-white', !isChecked);
        });
    };

    // --- Metode pembayaran (Transfer/COD) ---------------------------------
    // COD cuma boleh muncul kalau kurir yang SEDANG terpilih mendukungnya
    // (is_cod per opsi ongkir) - jadi harus dievaluasi ulang tiap kali
    // pilihan pengiriman berubah, bukan cuma sekali di awal.

    const syncPaymentStyles = () => {
        if (!paymentSection) {
            return;
        }

        paymentSection.querySelectorAll('[data-payment-option]').forEach((option) => {
            const input = option.querySelector('[data-payment-input]');
            const isChecked = Boolean(input?.checked);

            option.classList.toggle('border-primary', isChecked);
            option.classList.toggle('bg-secondary-container/20', isChecked);
            option.classList.toggle('border-surface-container-highest', !isChecked);
        });
    };

    const updatePaymentMethodField = () => {
        const checked = Array.from(paymentSection?.querySelectorAll('[data-payment-input]') || []).find(
            (input) => input.checked
        );
        const value = checked?.value || 'transfer';

        if (paymentMethodField) {
            paymentMethodField.value = value;
        }

        if (totalLabel) {
            totalLabel.textContent = value === 'cod' ? 'Total bayar tunai' : 'Total transfer';
        }

        if (paymentNote) {
            paymentNote.textContent =
                value === 'cod'
                    ? 'Siapkan uang pas sesuai total di atas - kurir akan menagih saat barang diserahkan.'
                    : 'Setelah order dibuat, customer dapat melanjutkan transfer dan mengirim bukti pembayaran ke WhatsApp admin.';
        }
    };

    const updateCodAvailability = (isCod, shippingValue = 0) => {
        if (!codOption) {
            return;
        }

        const itemsTotalValue = Number(summary?.getAttribute('data-items-total-value') || '0');
        const withinCodLimit = (itemsTotalValue + shippingValue) <= codMaxAmount;
        const show = isCod && withinCodLimit;

        codOption.classList.toggle('hidden', !show);
        codOption.classList.toggle('flex', show);

        // Kurir baru tidak dukung COD (atau total kelewat batas COD) tapi COD masih
        // terpilih -> balik ke Transfer, jangan biarkan submit dengan metode yang
        // sudah tidak valid.
        if (!show) {
            const codInput = codOption.querySelector('[data-payment-input]');

            if (codInput?.checked) {
                const transferInput = paymentSection?.querySelector('[data-payment-option="transfer"] [data-payment-input]');

                if (transferInput) {
                    transferInput.checked = true;
                }

                syncPaymentStyles();
            }
        }

        updatePaymentMethodField();
    };

    const bindRoot = (root) => {
        const modal = root.querySelector('[data-selector-modal]');
        const openButton = root.querySelector('[data-selector-open]');
        const closeButtons = root.querySelectorAll('[data-selector-close]');
        const applyButton = root.querySelector('[data-selector-apply]');

        if (!modal || !openButton) {
            return;
        }

        openButton.onclick = () => openModal(root);

        closeButtons.forEach((button) => {
            button.onclick = () => closeModal(root);
        });

        modal.onclick = (event) => {
            if (event.target === modal) {
                closeModal(root);
            }
        };

        root.querySelectorAll('[data-selector-input]').forEach((input) => {
            input.onchange = () => syncOptionStyles(root);
        });

        if (applyButton && root.getAttribute('data-checkout-selector') === 'shipping') {
            applyButton.onclick = () => applyShippingSelection();
        }

        if (applyButton && root.getAttribute('data-checkout-selector') === 'address') {
            applyButton.onclick = () => applyAddressSelection();
        }

        syncOptionStyles(root);
    };

    const renderAddressOptions = (addresses) =>
        addresses
            .map(
                (address) => `
                    <label class="block cursor-pointer rounded-2xl border ${
                        address.selected ? 'border-emerald-500 bg-emerald-50/50' : 'border-stone-200 bg-white'
                    } px-4 py-4 transition" data-selector-option>
                        <div class="flex items-start gap-3">
                            <input
                                type="radio"
                                name="checkout_address"
                                class="mt-1"
                                data-selector-input
                                value="${escapeHtml(address.id)}"
                                data-label="${escapeHtml(address.label)}"
                                data-contact="${escapeHtml(`${address.name} · ${address.phone}`)}"
                                data-detail="${escapeHtml(address.detail)}"
                                data-note="${escapeHtml(address.note || '')}"
                                data-is-primary="${address.is_primary ? '1' : '0'}"
                                ${address.selected ? 'checked' : ''}
                            >
                            <div class="flex-1 text-sm leading-6 text-stone-700">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="font-semibold text-stone-950">${escapeHtml(address.label)}</p>
                                    ${
                                        address.is_primary
                                            ? '<span class="rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700">Utama</span>'
                                            : ''
                                    }
                                </div>
                                <p class="mt-1 text-stone-500">${escapeHtml(`${address.name} · ${address.phone}`)}</p>
                                <p class="mt-2">${escapeHtml(address.detail)}</p>
                                ${
                                    address.note
                                        ? `<p class="mt-2 text-xs text-stone-500">${escapeHtml(address.note)}</p>`
                                        : ''
                                }
                            </div>
                        </div>
                    </label>
                `
            )
            .join('');

    const renderShippingOptions = (shippingOptions) => {
        if (shippingOptions.length === 0) {
            return `
                <div class="rounded-2xl border border-dashed border-stone-200 bg-stone-50 px-4 py-5 text-sm text-stone-500">
                    Opsi pengiriman akan muncul setelah alamat tujuan dan origin aktif siap dipakai untuk perhitungan ongkir.
                </div>
            `;
        }

        return shippingOptions
            .map(
                (option) => `
                    <label class="block cursor-pointer rounded-2xl border ${
                        option.selected ? 'border-emerald-500 bg-emerald-50/50' : 'border-stone-200 bg-white'
                    } px-4 py-4 transition" data-selector-option>
                        <div class="flex items-start gap-3">
                            <input
                                type="radio"
                                name="shipping_service"
                                class="mt-1"
                                data-selector-input
                                value="${escapeHtml(option.service)}"
                                data-option-id="${escapeHtml(option.id)}"
                                data-service="${escapeHtml(option.service)}"
                                data-price="${escapeHtml(option.price)}"
                                data-price-value="${escapeHtml(option.price_value)}"
                                data-estimate="${option.estimate ? escapeHtml(`Estimasi ${option.estimate}`) : ''}"
                                data-is-cod="${option.is_cod ? '1' : '0'}"
                                ${option.selected ? 'checked' : ''}
                            >
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="font-semibold text-stone-900">${escapeHtml(option.service)}</p>
                                    <p class="font-semibold text-emerald-700">${escapeHtml(option.price)}</p>
                                </div>
                                ${option.estimate ? `<p class="mt-1 text-sm text-stone-500">Estimasi ${escapeHtml(option.estimate)}</p>` : ''}
                            </div>
                        </div>
                    </label>
                `
            )
            .join('');
    };

    const updateSummary = (paymentSummary) => {
        summary.setAttribute('data-items-total-value', paymentSummary.items_total_value || 0);
        summary.setAttribute('data-unique-code-value', paymentSummary.unique_code_value || 0);
        summary.setAttribute('data-used-unique-code-value', paymentSummary.used_unique_code_value || 0);

        const shippingNode = summary.querySelector('[data-summary-shipping]');
        const grandTotalNode = summary.querySelector('[data-summary-grand-total]');
        const uniqueCodeRow = summary.querySelector('[data-summary-unique-code-row]');
        const uniqueCodeNode = summary.querySelector('[data-summary-unique-code]');
        const usedUniqueCodeRow = summary.querySelector('[data-summary-used-unique-code-row]');
        const usedUniqueCodeNode = summary.querySelector('[data-summary-used-unique-code]');
        const useBalanceCheckbox = page.querySelector('[data-use-unique-code-balance]');

        if (shippingNode) {
            shippingNode.textContent = paymentSummary.shipping_total || formatRupiah(0);
        }

        if (uniqueCodeNode) {
            uniqueCodeNode.textContent = paymentSummary.unique_code || formatRupiah(0);
        }

        if (uniqueCodeRow) {
            uniqueCodeRow.classList.toggle('hidden', Number(paymentSummary.unique_code_value || 0) <= 0);
        }

        if (usedUniqueCodeNode) {
            usedUniqueCodeNode.textContent = `-${paymentSummary.used_unique_code || formatRupiah(0)}`;
        }

        if (usedUniqueCodeRow) {
            usedUniqueCodeRow.classList.toggle('hidden', Number(paymentSummary.used_unique_code_value || 0) <= 0);
        }

        if (useBalanceCheckbox) {
            useBalanceCheckbox.checked = Boolean(paymentSummary.use_unique_code_balance);
        }

        if (useUniqueCodeBalanceField) {
            useUniqueCodeBalanceField.value = Boolean(paymentSummary.use_unique_code_balance) ? '1' : '0';
        }

        if (grandTotalNode) {
            grandTotalNode.textContent = paymentSummary.grand_total || formatRupiah(0);
        }
    };

    const updateAddressSection = (payload) => {
        const selectedAddress = payload.address || {};
        const label = addressRoot.querySelector('[data-address-label]');
        const contact = addressRoot.querySelector('[data-address-contact]');
        const detail = addressRoot.querySelector('[data-address-detail]');
        const note = addressRoot.querySelector('[data-address-note]');
        const primaryBadge = addressRoot.querySelector('[data-address-primary-badge]');
        const optionsContainer = addressRoot.querySelector('[data-selector-options]');

        if (label) {
            label.textContent = selectedAddress.label || '';
        }

        if (contact) {
            contact.textContent = selectedAddress.name && selectedAddress.phone
                ? `${selectedAddress.name} · ${selectedAddress.phone}`
                : '';
        }

        if (detail) {
            detail.textContent = selectedAddress.detail || '';
        }

        if (note) {
            const noteText = selectedAddress.note || '';
            note.textContent = noteText;
            note.classList.toggle('hidden', noteText === '');
        }

        if (primaryBadge) {
            primaryBadge.classList.toggle('hidden', !selectedAddress.is_primary);
        }

        if (optionsContainer) {
            optionsContainer.innerHTML = renderAddressOptions(payload.addresses || []);
        }

        if (addressIdField && selectedAddress?.id) {
            addressIdField.value = selectedAddress.id;
        }
    };

    const updateShippingSection = (payload) => {
        const shippingOptions = payload.shipping_options || [];
        const selectedShipping = shippingOptions.find((option) => option.selected) || shippingOptions[0] || null;
        const service = shippingRoot.querySelector('[data-shipping-service]');
        const price = shippingRoot.querySelector('[data-shipping-price]');
        const estimate = shippingRoot.querySelector('[data-shipping-estimate]');
        const optionsContainer = shippingRoot.querySelector('[data-selector-options]');
        const openButton = shippingRoot.querySelector('[data-selector-open]');
        const applyButton = shippingRoot.querySelector('[data-selector-apply]');

        if (service) {
            service.textContent = selectedShipping?.service || 'Opsi pengiriman belum tersedia';
        }

        if (price) {
            price.textContent = selectedShipping?.price || 'Rp0';
        }

        if (estimate) {
            estimate.textContent = selectedShipping?.estimate ? `Estimasi ${selectedShipping.estimate}` : '';
        }

        if (optionsContainer) {
            optionsContainer.innerHTML = renderShippingOptions(shippingOptions);
        }

        if (shippingOptionIdField) {
            shippingOptionIdField.value = selectedShipping?.id || '';
        }

        if (openButton) {
            openButton.classList.toggle('hidden', shippingOptions.length === 0);
        }

        if (applyButton) {
            applyButton.parentElement?.classList.toggle('hidden', shippingOptions.length === 0);
        }

        updateCodAvailability(Boolean(selectedShipping?.is_cod), Number(selectedShipping?.price_value || 0));
    };

    const refreshCheckout = async (addressId) => {
        const refreshUrl = addressRoot.getAttribute('data-checkout-refresh-url');

        if (!refreshUrl) {
            return;
        }

        setLoading(true);
        closeModal(addressRoot);

        try {
            const url = new URL(refreshUrl, window.location.origin);

            if (addressId) {
                url.searchParams.set('address_id', addressId);
            }

            url.searchParams.set('use_unique_code_balance', usesUniqueCodeBalance() ? '1' : '0');

            const response = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Gagal memuat ulang checkout.');
            }

            updateAddressSection(payload.data || {});
            updateShippingSection(payload.data || {});
            updateSummary(payload.data?.payment_summary || {});

            bindRoot(addressRoot);
            bindRoot(shippingRoot);
        } catch (error) {
            window.alert(error.message || 'Gagal memuat layanan kurir.');
        } finally {
            setLoading(false);
        }
    };

    const applyAddressSelection = () => {
        const selected = Array.from(addressRoot.querySelectorAll('[data-selector-input]')).find((input) => input.checked);

        if (!selected) {
            return;
        }

        refreshCheckout(selected.value);
    };

    const applyShippingSelection = () => {
        const selected = Array.from(shippingRoot.querySelectorAll('[data-selector-input]')).find((input) => input.checked);

        if (!selected) {
            return;
        }

        const service = shippingRoot.querySelector('[data-shipping-service]');
        const price = shippingRoot.querySelector('[data-shipping-price]');
        const estimate = shippingRoot.querySelector('[data-shipping-estimate]');
        const shippingValue = Number(selected.getAttribute('data-price-value') || '0');
        const itemsTotalValue = Number(summary.getAttribute('data-items-total-value') || '0');
        const uniqueCodeValue = Number(summary.getAttribute('data-unique-code-value') || '0');
        const usedUniqueCodeValue = Number(summary.getAttribute('data-used-unique-code-value') || '0');
        const shippingNode = summary.querySelector('[data-summary-shipping]');
        const grandTotalNode = summary.querySelector('[data-summary-grand-total]');

        if (service) {
            service.textContent = selected.getAttribute('data-service') || '';
        }

        if (price) {
            price.textContent = selected.getAttribute('data-price') || '';
        }

        if (estimate) {
            estimate.textContent = selected.getAttribute('data-estimate') || '';
        }

        if (shippingNode) {
            shippingNode.textContent = formatRupiah(shippingValue);
        }

        if (grandTotalNode) {
            grandTotalNode.textContent = formatRupiah(Math.max(0, itemsTotalValue + shippingValue + uniqueCodeValue - usedUniqueCodeValue));
        }

        if (shippingOptionIdField) {
            shippingOptionIdField.value = selected.getAttribute('data-option-id') || '';
        }

        syncOptionStyles(shippingRoot);
        updateCodAvailability(selected.getAttribute('data-is-cod') === '1', shippingValue);
        closeModal(shippingRoot);
    };

    bindRoot(addressRoot);
    bindRoot(shippingRoot);

    const uniqueCodeBalanceCheckbox = page.querySelector('[data-use-unique-code-balance]');

    if (uniqueCodeBalanceCheckbox) {
        uniqueCodeBalanceCheckbox.addEventListener('change', () => {
            if (useUniqueCodeBalanceField) {
                useUniqueCodeBalanceField.value = uniqueCodeBalanceCheckbox.checked ? '1' : '0';
            }

            refreshCheckout(getSelectedAddressId());
        });
    }

    if (addressIdField) {
        addressIdField.value = getSelectedAddressId();
    }

    // Pastikan ada opsi pengiriman terpilih kalau server tidak menandai default,
    // supaya shipping_option_id tidak kosong saat submit.
    const shippingInputs = Array.from(shippingRoot.querySelectorAll('[data-selector-input]'));
    if (shippingInputs.length > 0 && !shippingInputs.some((input) => input.checked)) {
        shippingInputs[0].checked = true;
        syncOptionStyles(shippingRoot);
    }

    if (shippingOptionIdField) {
        shippingOptionIdField.value = getSelectedShippingOptionId();
    }

    // Metode pembayaran: state awal ikut kurir yang aktif terpilih di atas,
    // lalu re-evaluasi tiap kali radio Transfer/COD diklik langsung.
    if (paymentSection) {
        const initialShippingInput = shippingInputs.find((input) => input.checked);
        updateCodAvailability(
            initialShippingInput?.getAttribute('data-is-cod') === '1',
            Number(initialShippingInput?.getAttribute('data-price-value') || '0')
        );

        paymentSection.querySelectorAll('[data-payment-input]').forEach((input) => {
            input.addEventListener('change', () => {
                syncPaymentStyles();
                updatePaymentMethodField();
            });
        });
    }

    // Cegah submit kalau opsi pengiriman belum ada/terpilih (hindari error mentah dari server).
    if (orderForm) {
        orderForm.addEventListener('submit', (event) => {
            if (!shippingOptionIdField || shippingOptionIdField.value === '') {
                event.preventDefault();
                window.alert('Opsi pengiriman belum tersedia. Periksa alamat tujuan, lalu pilih layanan pengiriman dulu ya.');
            }
        });
    }

    page.querySelectorAll('[data-selector-open]').forEach((button) => {
        button.disabled = false;
    });
};
