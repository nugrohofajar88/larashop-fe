export const initAdminShipmentSettings = () => {
    document.querySelectorAll('[data-admin-shipment-settings]').forEach((root) => {
        const form = root.querySelector('[data-admin-shipment-settings-form]');
        const submitButton = root.querySelector('[data-admin-shipment-submit]');
        const feedback = root.querySelector('[data-admin-shipment-feedback]');
        const destinationInput = root.querySelector('[data-admin-shipment-destination-input]');
        const destinationResults = root.querySelector('[data-admin-shipment-destination-results]');
        const destinationHint = root.querySelector('[data-admin-shipment-destination-hint]');
        const destinationStatus = root.querySelector('[data-admin-shipment-destination-status]');
        const originIdField = root.querySelector('[data-admin-shipment-origin-id]');
        const provinceField = root.querySelector('[data-admin-shipment-province]');
        const cityField = root.querySelector('[data-admin-shipment-city]');
        const districtField = root.querySelector('[data-admin-shipment-district]');
        const subdistrictField = root.querySelector('[data-admin-shipment-subdistrict]');
        const postalCodeField = root.querySelector('[data-admin-shipment-postal-code]');
        const labelInput = root.querySelector('[data-admin-shipment-label]');
        const contactNameInput = root.querySelector('[data-admin-shipment-contact-name]');
        const contactPhoneInput = root.querySelector('[data-admin-shipment-contact-phone]');
        const addressLineInput = root.querySelector('[data-admin-shipment-address-line]');

        if (
            !form ||
            !submitButton ||
            !feedback ||
            !destinationInput ||
            !destinationResults ||
            !destinationHint ||
            !destinationStatus ||
            !originIdField ||
            !provinceField ||
            !cityField ||
            !districtField ||
            !subdistrictField ||
            !postalCodeField ||
            !labelInput ||
            !contactNameInput ||
            !contactPhoneInput ||
            !addressLineInput
        ) {
            return;
        }

        const searchUrl = root.getAttribute('data-destination-search-url') || '';
        let searchTimer = null;

        const setFieldValue = (field, value) => {
            field.value = value;
            field.setAttribute('value', value);
        };

        const hideFeedback = () => {
            feedback.classList.add('hidden');
            feedback.textContent = '';
        };

        const showFeedback = (message) => {
            feedback.textContent = message;
            feedback.classList.remove('hidden');
        };

        const clearDestinationFields = () => {
            [originIdField, provinceField, cityField, districtField, subdistrictField, postalCodeField].forEach((field) => {
                setFieldValue(field, '');
            });
        };

        const hideDestinationResults = () => {
            destinationResults.classList.add('hidden');
        };

        const validateForm = () => {
            if (labelInput.value.trim() === '') {
                return 'Label origin wajib diisi.';
            }

            if (contactNameInput.value.trim() === '') {
                return 'Nama PIC wajib diisi.';
            }

            if (contactPhoneInput.value.trim() === '') {
                return 'Nomor PIC wajib diisi.';
            }

            if (destinationInput.value.trim() === '') {
                return 'Wilayah origin wajib diisi.';
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

            if (addressLineInput.value.trim() === '') {
                return 'Alamat lengkap wajib diisi.';
            }

            return null;
        };

        const updateSubmitState = () => {
            const validationMessage = validateForm();
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

            destinationStatus.textContent = hasDestination ? 'Wilayah origin sudah dipilih.' : 'Wilayah belum dipilih.';
            destinationStatus.classList.toggle('text-emerald-700', hasDestination);
            destinationStatus.classList.toggle('text-amber-700', !hasDestination);
        };

        const applyDestinationSelection = (destination) => {
            destinationInput.value = destination.label || '';
            setFieldValue(originIdField, destination.id || '');
            setFieldValue(provinceField, destination.province_name || '');
            setFieldValue(cityField, destination.city_name || '');
            setFieldValue(districtField, destination.district_name || '');
            setFieldValue(subdistrictField, destination.subdistrict_name || '');
            setFieldValue(postalCodeField, destination.zip_code || '');
            hideDestinationResults();
            destinationHint.textContent = 'Destinasi origin terpilih. Data wilayah akan disimpan otomatis.';
            updateSubmitState();
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
                button.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    applyDestinationSelection(item);
                });
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    applyDestinationSelection(item);
                });
                destinationResults.appendChild(button);
            });

            destinationResults.classList.remove('hidden');
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

        destinationInput.addEventListener('input', () => {
            clearDestinationFields();
            updateSubmitState();
            hideFeedback();

            if (searchTimer) {
                window.clearTimeout(searchTimer);
            }

            const term = destinationInput.value.trim();
            searchTimer = window.setTimeout(() => {
                performSearch(term);
            }, 800);
        });

        [labelInput, contactNameInput, contactPhoneInput, addressLineInput].forEach((field) => {
            field.addEventListener('input', () => {
                hideFeedback();
                updateSubmitState();
            });
        });

        [addressLineInput].forEach((field) => {
            field.addEventListener('focus', hideDestinationResults);
            field.addEventListener('click', (event) => {
                event.stopPropagation();
                hideDestinationResults();
            });
        });

        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) {
                hideDestinationResults();
            }
        });

        form.addEventListener('submit', (event) => {
            const validationMessage = validateForm();

            if (validationMessage) {
                event.preventDefault();
                showFeedback(validationMessage);
                return;
            }

            hideFeedback();
        });

        if (destinationInput.value.trim() !== '' && provinceField.value.trim() !== '') {
            destinationHint.textContent = 'Destinasi origin terpilih. Data wilayah akan disimpan otomatis.';
        }

        updateSubmitState();
    });
};
