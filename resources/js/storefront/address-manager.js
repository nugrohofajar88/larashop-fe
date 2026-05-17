export const initStorefrontAddressManager = () => {
    document.querySelectorAll('[data-customer-address-manager]').forEach((root) => {
        const modal = root.querySelector('[data-address-modal]');
        const modalPanel = root.querySelector('[data-address-modal-panel]');
        const modalTitle = root.querySelector('[data-address-modal-title]');
        const form = root.querySelector('[data-address-modal-form]');
        const submitButton = root.querySelector('[data-address-submit-button]');
        const formFeedback = root.querySelector('[data-address-form-feedback]');
        const openCreateButton = root.querySelector('[data-address-open-create]');
        const closeButtons = root.querySelectorAll('[data-address-modal-close]');
        const editButtons = root.querySelectorAll('[data-address-open-edit]');
        const destinationInput = root.querySelector('[data-address-destination-input]');
        const destinationResults = root.querySelector('[data-address-destination-results]');
        const destinationHint = root.querySelector('[data-address-destination-hint]');
        const destinationStatus = root.querySelector('[data-address-destination-status]');
        const modeField = root.querySelector('[data-address-mode-field]');
        const addressIdField = root.querySelector('[data-address-id-field]');
        const destinationIdField = root.querySelector('[data-address-destination-id-field]');
        const provinceField = root.querySelector('[data-address-province-field]');
        const cityField = root.querySelector('[data-address-city-field]');
        const districtField = root.querySelector('[data-address-district-field]');
        const subdistrictField = root.querySelector('[data-address-subdistrict-field]');
        const postalCodeField = root.querySelector('[data-address-postal-code-field]');
        const addressLineField = root.querySelector('[data-address-line-input]');
        const noteField = root.querySelector('[data-address-note-input]');
        const isPrimaryField = root.querySelector('[data-address-is-primary-input]');
        const labelInput = root.querySelector('[data-address-label-input]');
        const recipientNameInput = root.querySelector('[data-address-recipient-name-input]');
        const recipientPhoneInput = root.querySelector('[data-address-recipient-phone-input]');

        if (
            !modal ||
            !modalPanel ||
            !form ||
            !submitButton ||
            !formFeedback ||
            !destinationInput ||
            !destinationResults ||
            !destinationStatus ||
            !modeField ||
            !addressIdField ||
            !destinationIdField ||
            !provinceField ||
            !cityField ||
            !districtField ||
            !subdistrictField ||
            !postalCodeField ||
            !addressLineField ||
            !noteField ||
            !isPrimaryField ||
            !labelInput ||
            !recipientNameInput ||
            !recipientPhoneInput
        ) {
            return;
        }

        const saveAction = root.getAttribute('data-save-action') || form.getAttribute('action') || '';
        const detailTemplate = root.getAttribute('data-detail-template') || '';
        const searchUrl = root.getAttribute('data-search-url') || '';
        let searchTimer = null;
        let selectedDestination = null;

        const buildAddressPayload = () => {
            const provinceValue = selectedDestination?.province_name || provinceField.value;
            const cityValue = selectedDestination?.city_name || cityField.value;
            const districtValue = selectedDestination?.district_name || districtField.value;
            const subdistrictValue = selectedDestination?.subdistrict_name || subdistrictField.value;
            const postalCodeValue = selectedDestination?.zip_code || postalCodeField.value;
            const destinationIdValue = selectedDestination?.id || destinationIdField.value;

            return {
                _token: form.querySelector('[name="_token"]')?.value || '',
                _address_mode: modeField.value,
                _address_id: addressIdField.value,
                label: labelInput.value.trim(),
                recipient_name: recipientNameInput.value.trim(),
                recipient_phone: recipientPhoneInput.value.trim(),
                destination_id: destinationIdValue,
                province: provinceValue,
                city: cityValue,
                district: districtValue,
                subdistrict: subdistrictValue,
                postal_code: postalCodeValue,
                address_line: addressLineField.value.trim(),
                note: noteField.value.trim(),
                is_primary: isPrimaryField.checked ? '1' : '0',
            };
        };

        const hideDestinationResults = () => {
            destinationResults.classList.add('hidden');
        };

        const hideFormFeedback = () => {
            formFeedback.classList.add('hidden');
            formFeedback.textContent = '';
        };

        const showFormFeedback = (message) => {
            formFeedback.textContent = message;
            formFeedback.classList.remove('hidden');
        };

        const validateBeforeSubmit = () => {
            if (labelInput.value.trim() === '') {
                return 'Label alamat wajib diisi.';
            }

            if (recipientNameInput.value.trim() === '') {
                return 'Nama penerima wajib diisi.';
            }

            if (recipientPhoneInput.value.trim() === '') {
                return 'Nomor penerima wajib diisi.';
            }

            if (destinationInput.value.trim() === '') {
                return 'Wilayah tujuan wajib diisi.';
            }

            if (
                provinceField.value.trim() === '' ||
                cityField.value.trim() === '' ||
                districtField.value.trim() === '' ||
                subdistrictField.value.trim() === '' ||
                postalCodeField.value.trim() === ''
            ) {
                return 'Pilih salah satu hasil wilayah dari dropdown destinasi terlebih dulu.';
            }

            if (addressLineField.value.trim() === '') {
                return 'Alamat lengkap wajib diisi.';
            }

            return null;
        };

        const updateSubmitState = () => {
            const validationMessage = validateBeforeSubmit();
            const isValid = validationMessage === null;

            submitButton.disabled = !isValid;
            submitButton.classList.toggle('opacity-60', !isValid);
            submitButton.classList.toggle('cursor-not-allowed', !isValid);

            const hasDestination =
                provinceField.value.trim() !== '' &&
                cityField.value.trim() !== '' &&
                districtField.value.trim() !== '' &&
                subdistrictField.value.trim() !== '' &&
                postalCodeField.value.trim() !== '';

            destinationStatus.textContent = hasDestination ? 'Wilayah sudah dipilih.' : 'Wilayah belum dipilih.';
            destinationStatus.classList.toggle('text-emerald-700', hasDestination);
            destinationStatus.classList.toggle('text-amber-700', !hasDestination);
        };

        const setFieldValue = (field, value) => {
            field.value = value;
            field.setAttribute('value', value);
        };

        const setTextareaValue = (field, value) => {
            field.value = value;
            field.defaultValue = value;
        };

        const clearDestinationFields = () => {
            selectedDestination = null;
            [provinceField, cityField, districtField, subdistrictField, postalCodeField].forEach((field) => {
                field.value = '';
                field.setAttribute('value', '');
            });
            destinationIdField.value = '';
            destinationIdField.setAttribute('value', '');
        };

        const resetToCreateState = () => {
            form.setAttribute('action', saveAction);
            modeField.value = 'create';
            addressIdField.value = '';
            selectedDestination = null;
            modalTitle.textContent = 'Tambah alamat baru';
            submitButton.textContent = 'Simpan alamat';
            labelInput.value = '';
            recipientNameInput.value = '';
            recipientPhoneInput.value = '';
            destinationInput.value = '';
            isPrimaryField.checked = false;
            setTextareaValue(addressLineField, '');
            setTextareaValue(noteField, '');
            clearDestinationFields();
            destinationResults.innerHTML = '';
            destinationHint.textContent = 'Mulai ketik minimal 3 karakter lalu tunggu sebentar untuk mencari destinasi.';
            hideFormFeedback();
            hideDestinationResults();
            updateSubmitState();
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            hideDestinationResults();
        };

        const openModal = () => {
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        };

        const applyDestinationSelection = (destination) => {
            selectedDestination = {
                id: destination.id || '',
                label: destination.label || '',
                province_name: destination.province_name || '',
                city_name: destination.city_name || '',
                district_name: destination.district_name || '',
                subdistrict_name: destination.subdistrict_name || '',
                zip_code: destination.zip_code || '',
            };
            destinationInput.value = destination.label || '';
            setFieldValue(destinationIdField, destination.id || '');
            setFieldValue(provinceField, destination.province_name || '');
            setFieldValue(cityField, destination.city_name || '');
            setFieldValue(districtField, destination.district_name || '');
            setFieldValue(subdistrictField, destination.subdistrict_name || '');
            setFieldValue(postalCodeField, destination.zip_code || '');
            hideDestinationResults();
            destinationHint.textContent = 'Destinasi terpilih. Data wilayah akan disimpan otomatis.';
            updateSubmitState();
        };

        const fillDestination = (button) => {
            applyDestinationSelection({
                label: button.getAttribute('data-destination-label') || '',
                id: button.getAttribute('data-destination-id') || '',
                province_name: button.getAttribute('data-destination-province') || '',
                city_name: button.getAttribute('data-destination-city') || '',
                district_name: button.getAttribute('data-destination-district') || '',
                subdistrict_name: button.getAttribute('data-destination-subdistrict') || '',
                zip_code: button.getAttribute('data-destination-postal-code') || '',
            });
        };

        const renderDestinationResults = (items) => {
            destinationResults.innerHTML = '';

            if (!items.length) {
                destinationResults.innerHTML = '<p class="px-4 py-3 text-sm text-stone-500">Destinasi tidak ditemukan.</p>';
                destinationResults.classList.remove('hidden');
                return;
            }

            items.forEach((item) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'block w-full border-b border-stone-100 px-4 py-3 text-left text-sm text-stone-700 transition last:border-b-0 hover:bg-stone-50';
                button.textContent = item.label || '';
                button.setAttribute('data-destination-label', item.label || '');
                button.setAttribute('data-destination-id', item.id || '');
                button.setAttribute('data-destination-province', item.province_name || '');
                button.setAttribute('data-destination-city', item.city_name || '');
                button.setAttribute('data-destination-district', item.district_name || '');
                button.setAttribute('data-destination-subdistrict', item.subdistrict_name || '');
                button.setAttribute('data-destination-postal-code', item.zip_code || '');
                button.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    fillDestination(button);
                });
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    fillDestination(button);
                });
                destinationResults.appendChild(button);
            });

            destinationResults.classList.remove('hidden');
        };

        const setCreateMode = () => {
            resetToCreateState();
        };

        const setEditMode = (button) => {
            const id = button.getAttribute('data-address-id') || '';
            form.setAttribute('action', saveAction);
            modeField.value = 'edit';
            addressIdField.value = id;
            modalTitle.textContent = 'Edit alamat';
            submitButton.textContent = 'Simpan perubahan alamat';
            labelInput.value = button.getAttribute('data-address-label') || '';
            isPrimaryField.checked = button.getAttribute('data-address-is-primary') === '1';
            recipientNameInput.value = button.getAttribute('data-address-recipient-name') || '';
            recipientPhoneInput.value = button.getAttribute('data-address-recipient-phone') || '';
            destinationInput.value = button.getAttribute('data-address-destination-label') || '';
            setFieldValue(destinationIdField, button.getAttribute('data-address-destination-id') || '');
            setFieldValue(provinceField, button.getAttribute('data-address-province') || '');
            setFieldValue(cityField, button.getAttribute('data-address-city') || '');
            setFieldValue(districtField, button.getAttribute('data-address-district') || '');
            setFieldValue(subdistrictField, button.getAttribute('data-address-subdistrict') || '');
            setFieldValue(postalCodeField, button.getAttribute('data-address-postal-code') || '');
            selectedDestination = {
                id: destinationIdField.value,
                label: destinationInput.value,
                province_name: provinceField.value,
                city_name: cityField.value,
                district_name: districtField.value,
                subdistrict_name: subdistrictField.value,
                zip_code: postalCodeField.value,
            };
            setTextareaValue(addressLineField, button.getAttribute('data-address-line') || '');
            setTextareaValue(noteField, button.getAttribute('data-address-note') || '');
            destinationHint.textContent = 'Ubah pencarian jika ingin mengganti wilayah tujuan.';
            hideFormFeedback();
            updateSubmitState();
        };

        const loadAddressDetail = async (id) => {
            if (detailTemplate === '') {
                return null;
            }

            const response = await fetch(detailTemplate.replace('__ID__', id), {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Gagal memuat detail alamat.');
            }

            return payload.data || null;
        };

        const performSearch = async (term) => {
            if (term.length < 3 || searchUrl === '') {
                hideDestinationResults();
                destinationHint.textContent = 'Mulai ketik minimal 3 karakter untuk mencari destinasi.';
                return;
            }

            destinationHint.textContent = 'Mencari destinasi...';

            try {
                const response = await fetch(`${searchUrl}?search=${encodeURIComponent(term)}`, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                const payload = await response.json();

                if (!response.ok) {
                    destinationHint.textContent = payload.message || 'Gagal mengambil destinasi.';
                    hideDestinationResults();
                    return;
                }

                destinationHint.textContent = 'Pilih salah satu hasil yang paling sesuai.';
                renderDestinationResults(payload.data || []);
            } catch {
                destinationHint.textContent = 'Gagal mengambil destinasi.';
                hideDestinationResults();
            }
        };

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            form.setAttribute('action', saveAction);
            hideFormFeedback();

            if (modeField.value !== 'edit' || addressIdField.value === '') {
                addressIdField.value = '';
                modeField.value = 'create';
            }

            const validationMessage = validateBeforeSubmit();

            if (validationMessage) {
                showFormFeedback(validationMessage);
                return;
            }

            submitButton.disabled = true;
            submitButton.textContent = modeField.value === 'edit' ? 'Menyimpan perubahan...' : 'Menyimpan alamat...';

            try {
                const addressPayload = buildAddressPayload();
                const formData = new FormData();

                Object.entries(addressPayload).forEach(([key, value]) => {
                    formData.set(key, value ?? '');
                });

                const response = await fetch(saveAction, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const errors = payload.errors || {};
                    const firstError = Object.values(errors)
                        .flat()
                        .find((value) => typeof value === 'string' && value.trim() !== '');
                    showFormFeedback(firstError || payload.message || 'Gagal menyimpan alamat.');
                    return;
                }

                closeModal();
                window.location.reload();
            } catch {
                showFormFeedback('Gagal menyimpan alamat. Coba lagi.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = modeField.value === 'edit' ? 'Simpan perubahan alamat' : 'Simpan alamat';
            }
        });

        openCreateButton?.addEventListener('click', () => {
            setCreateMode();
            openModal();
        });

        editButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                const id = button.getAttribute('data-address-id') || '';

                setEditMode(button);
                openModal();

                if (id === '') {
                    return;
                }

                try {
                    const address = await loadAddressDetail(id);

                    if (!address) {
                        return;
                    }

                    labelInput.value = address.label || '';
                    isPrimaryField.checked = Boolean(address.is_primary);
                    recipientNameInput.value = address.name || address.recipient_name || '';
                    recipientPhoneInput.value = address.phone || address.recipient_phone || '';
                    destinationInput.value = [
                        address.subdistrict || '',
                        address.district || '',
                        address.city || '',
                        address.province || '',
                        address.postal_code || '',
                    ]
                        .filter((value) => value !== '')
                        .join(', ');
                    setFieldValue(provinceField, address.province || '');
                    setFieldValue(cityField, address.city || '');
                    setFieldValue(districtField, address.district || '');
                    setFieldValue(subdistrictField, address.subdistrict || '');
                    setFieldValue(postalCodeField, address.postal_code || '');
                    setFieldValue(destinationIdField, address.destination_id || '');
                    selectedDestination = {
                        id: destinationIdField.value,
                        label: destinationInput.value,
                        province_name: provinceField.value,
                        city_name: cityField.value,
                        district_name: districtField.value,
                        subdistrict_name: subdistrictField.value,
                        zip_code: postalCodeField.value,
                    };
                    setTextareaValue(addressLineField, address.address_line || '');
                    setTextareaValue(noteField, address.note || '');
                    updateSubmitState();
                } catch (error) {
                    showFormFeedback(error.message || 'Gagal memuat detail alamat.');
                }
            });
        });

        destinationInput.addEventListener('input', () => {
            clearDestinationFields();
            updateSubmitState();
            if (searchTimer) {
                window.clearTimeout(searchTimer);
            }

            const term = destinationInput.value.trim();
            searchTimer = window.setTimeout(() => {
                performSearch(term);
            }, 800);
        });

        [labelInput, recipientNameInput, recipientPhoneInput, addressLineField].forEach((field) => {
            field.addEventListener('input', updateSubmitState);
        });

        [addressLineField, noteField].forEach((field) => {
            field.addEventListener('focus', hideDestinationResults);
            field.addEventListener('click', (event) => {
                event.stopPropagation();
                hideDestinationResults();
            });
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                closeModal();
                resetToCreateState();
            });
        });

        modalPanel.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        modal.addEventListener('click', (event) => {
            if (!event.target.closest('[data-address-modal-panel]')) {
                closeModal();
                resetToCreateState();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
                resetToCreateState();
            }
        });

        if (root.getAttribute('data-open-on-load') === 'true') {
            if (root.getAttribute('data-initial-mode') === 'edit') {
                const activeEditButton = Array.from(editButtons).find(
                    (button) => button.getAttribute('data-address-id') === root.getAttribute('data-initial-address-id'),
                );

                if (activeEditButton) {
                    setEditMode(activeEditButton);
                } else {
                    setCreateMode();
                }
            } else {
                setCreateMode();
            }

            if (destinationInput.value.trim() !== '' && provinceField.value !== '') {
                destinationHint.textContent = 'Destinasi terpilih. Data wilayah akan disimpan otomatis.';
            }

            openModal();
        }

        updateSubmitState();
    });
};
