const addressSummary = (address) => {
    return [
        address.address_line,
        address.subdistrict,
        address.district ? `Kec. ${address.district}` : '',
        address.city,
        address.province,
        address.postal_code,
    ]
        .filter((value) => value && value.trim() !== '')
        .join(', ');
};

const addressDetailMarkup = (address) => {
    const note = address.address_note
        ? `
            <div class="rounded-2xl bg-amber-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-700">Catatan kurir</p>
                <p class="mt-2 text-sm leading-7 text-amber-900">${address.address_note}</p>
            </div>
        `
        : '';

    return `
        <div class="space-y-4 text-sm">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-semibold text-stone-700">${address.label}</span>
                ${address.is_primary ? '<span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Utama</span>' : ''}
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-stone-50 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Penerima</p>
                    <p class="mt-2 font-semibold text-stone-900">${address.recipient_name}</p>
                    <p class="mt-1 text-stone-500">${address.recipient_phone}</p>
                </div>
                <div class="rounded-2xl bg-stone-50 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Wilayah</p>
                    <p class="mt-2 font-semibold text-stone-900">${address.city}, ${address.province}</p>
                    <p class="mt-1 text-stone-500">${address.postal_code}</p>
                </div>
            </div>
            <div class="rounded-2xl bg-stone-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-stone-500">Alamat lengkap</p>
                <p class="mt-2 leading-7 text-stone-800">${addressSummary(address)}</p>
            </div>
            ${note}
        </div>
    `;
};

export const initAddressBook = () => {
    document.querySelectorAll('[data-address-book]').forEach((root) => {
        const stateNode = root.querySelector('[data-address-book-state]');
        const list = root.querySelector('[data-address-book-list]');
        const emptyState = root.querySelector('[data-address-book-empty]');
        const input = root.querySelector('[data-address-book-input]');
        const modal = root.querySelector('[data-address-modal]');
        const modalTitle = root.querySelector('[data-address-modal-title]');
        const detailPanel = root.querySelector('[data-address-detail-panel]');
        const form = root.querySelector('[data-address-form]');
        const addButton = root.querySelector('[data-address-add]');
        const saveButton = root.querySelector('[data-address-save]');
        const closeButtons = root.querySelectorAll('[data-address-modal-close], [data-address-modal-cancel]');
        const mode = root.getAttribute('data-address-book-mode') || 'editable';

        if (!stateNode || !list || !emptyState || !modal || !modalTitle || !detailPanel) {
            return;
        }

        let addresses = JSON.parse(stateNode.textContent || '[]');
        let currentMode = 'detail';
        let editingId = null;

        const fields = form
            ? {
                  id: form.querySelector('[name="address_id"]'),
                  label: form.querySelector('[name="label"]'),
                  recipientName: form.querySelector('[name="recipient_name"]'),
                  recipientPhone: form.querySelector('[name="recipient_phone"]'),
                  province: form.querySelector('[name="province"]'),
                  city: form.querySelector('[name="city"]'),
                  district: form.querySelector('[name="district"]'),
                  subdistrict: form.querySelector('[name="subdistrict"]'),
                  postalCode: form.querySelector('[name="postal_code"]'),
                  addressLine: form.querySelector('[name="address_line"]'),
                  addressNote: form.querySelector('[name="address_note"]'),
                  isPrimary: form.querySelector('[name="is_primary"]'),
              }
            : null;

        const syncInput = () => {
            if (input) {
                input.value = JSON.stringify(addresses);
            }
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        };

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        };

        const resetForm = (address = null) => {
            if (!fields || !form) {
                return;
            }

            fields.id.value = address?.id ?? '';
            fields.recipientName.value = address?.recipient_name ?? '';
            fields.recipientPhone.value = address?.recipient_phone ?? '';
            fields.province.value = address?.province ?? '';
            fields.city.value = address?.city ?? '';
            fields.district.value = address?.district ?? '';
            fields.subdistrict.value = address?.subdistrict ?? '';
            fields.postalCode.value = address?.postal_code ?? '';
            fields.addressLine.value = address?.address_line ?? '';
            fields.addressNote.value = address?.address_note ?? '';
            fields.isPrimary.checked = Boolean(address?.is_primary ?? addresses.length === 0);
            fields.label.value = address?.label ?? 'Alamat';
        };

        const showDetail = (address) => {
            currentMode = 'detail';
            modalTitle.textContent = 'Detail Alamat';
            detailPanel.innerHTML = addressDetailMarkup(address);
            detailPanel.classList.remove('hidden');
            form?.classList.add('hidden');
            saveButton?.classList.add('hidden');
            openModal();
        };

        const showEditor = (address = null) => {
            currentMode = address ? 'edit' : 'create';
            editingId = address?.id ?? null;
            modalTitle.textContent = address ? 'Edit Alamat' : 'Alamat Baru';
            detailPanel.classList.add('hidden');
            detailPanel.innerHTML = '';
            form?.classList.remove('hidden');
            saveButton?.classList.remove('hidden');
            resetForm(address);
            openModal();
        };

        const makePrimary = (selectedId) => {
            addresses = addresses.map((address) => ({
                ...address,
                is_primary: address.id === selectedId,
            }));
            syncInput();
            render();
        };

        const removeAddress = (selectedId) => {
            const wasPrimary = addresses.find((address) => address.id === selectedId)?.is_primary;
            addresses = addresses.filter((address) => address.id !== selectedId);

            if (wasPrimary && addresses[0]) {
                addresses[0].is_primary = true;
            }

            syncInput();
            render();
        };

        const render = () => {
            list.innerHTML = '';

            if (addresses.length === 0) {
                emptyState.classList.remove('hidden');
                syncInput();
                return;
            }

            emptyState.classList.add('hidden');

            addresses.forEach((address) => {
                const card = document.createElement('article');
                card.className = 'rounded-[1.75rem] border border-stone-200 bg-white px-5 py-5 shadow-sm';

                const actions = mode === 'editable'
                    ? `
                        <div class="mt-5 flex flex-wrap gap-2">
                            <button type="button" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700" data-address-action="detail">Detail</button>
                            <button type="button" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700" data-address-action="edit">Edit</button>
                            ${address.is_primary ? '' : '<button type="button" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-semibold text-white" data-address-action="primary">Pilih</button>'}
                            <button type="button" class="rounded-full bg-rose-50 px-4 py-2 text-sm font-medium text-rose-700" data-address-action="remove">Hapus</button>
                        </div>
                    `
                    : `
                        <div class="mt-5 flex flex-wrap gap-2">
                            <button type="button" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700" data-address-action="detail">Detail</button>
                        </div>
                    `;

                card.innerHTML = `
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-lg font-semibold text-stone-950">${address.label}</p>
                                ${address.is_primary ? '<span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Utama</span>' : ''}
                            </div>
                            <p class="mt-2 text-sm text-stone-500">${address.recipient_name} Â· ${address.recipient_phone}</p>
                            <p class="mt-4 text-sm leading-7 text-stone-700">${addressSummary(address)}</p>
                        </div>
                    </div>
                    ${actions}
                `;

                card.querySelector('[data-address-action="detail"]')?.addEventListener('click', () => showDetail(address));
                card.querySelector('[data-address-action="edit"]')?.addEventListener('click', () => showEditor(address));
                card.querySelector('[data-address-action="primary"]')?.addEventListener('click', () => makePrimary(address.id));
                card.querySelector('[data-address-action="remove"]')?.addEventListener('click', () => removeAddress(address.id));

                list.appendChild(card);
            });

            syncInput();
        };

        addButton?.addEventListener('click', () => showEditor());

        saveButton?.addEventListener('click', () => {
            if (!fields) {
                return;
            }

            const nextAddress = {
                id: fields.id.value || `addr-${Date.now()}`,
                label: fields.label.value.trim() || 'Rumah',
                recipient_name: fields.recipientName.value.trim(),
                recipient_phone: fields.recipientPhone.value.trim(),
                province: fields.province.value.trim(),
                city: fields.city.value.trim(),
                district: fields.district.value.trim(),
                subdistrict: fields.subdistrict.value.trim(),
                postal_code: fields.postalCode.value.trim(),
                address_line: fields.addressLine.value.trim(),
                address_note: fields.addressNote.value.trim(),
                is_primary: fields.isPrimary.checked,
            };

            if (
                !nextAddress.recipient_name ||
                !nextAddress.recipient_phone ||
                !nextAddress.province ||
                !nextAddress.city ||
                !nextAddress.district ||
                !nextAddress.subdistrict ||
                !nextAddress.postal_code ||
                !nextAddress.address_line
            ) {
                return;
            }

            if (currentMode === 'edit' && editingId) {
                addresses = addresses.map((address) => (address.id === editingId ? nextAddress : address));
            } else {
                addresses.push(nextAddress);
            }

            if (nextAddress.is_primary || addresses.filter((address) => address.is_primary).length === 0) {
                addresses = addresses.map((address) => ({
                    ...address,
                    is_primary: address.id === nextAddress.id,
                }));
            } else {
                addresses = addresses.map((address) => ({
                    ...address,
                    is_primary: address.id === nextAddress.id ? false : address.is_primary,
                }));
            }

            closeModal();
            render();
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        render();
    });
};
