const createPreviewCard = (image, index, onPrimary, onRemove) => {
    const card = document.createElement('article');
    card.className = 'rounded-[1.5rem] border border-stone-200 bg-white p-4';

    const preview = image.preview
        ? `<img src="${image.preview}" alt="${image.label}" class="h-24 w-24 rounded-2xl object-cover">`
        : `<div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-[linear-gradient(135deg,_#dff6e7,_#f8efe0)] text-center text-xs font-semibold text-stone-600">Preview</div>`;

    card.innerHTML = `
        <div class="flex items-start gap-4">
            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-stone-100">
                ${preview}
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="font-semibold text-stone-900">${image.label}</p>
                    ${image.isPrimary ? '<span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Utama</span>' : ''}
                    ${image.source === 'new' ? '<span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">Baru</span>' : ''}
                </div>
                <p class="mt-2 truncate text-sm text-stone-500">${image.name}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    ${image.isPrimary ? '' : '<button type="button" data-action="primary" class="rounded-full border border-stone-300 px-3 py-2 text-xs font-medium text-stone-700">Jadikan utama</button>'}
                    <button type="button" data-action="remove" class="rounded-full bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700">Hapus</button>
                </div>
            </div>
        </div>
    `;

    const primaryButton = card.querySelector('[data-action="primary"]');
    const removeButton = card.querySelector('[data-action="remove"]');

    if (primaryButton) {
        primaryButton.addEventListener('click', () => onPrimary(index));
    }

    if (removeButton) {
        removeButton.addEventListener('click', () => onRemove(index));
    }

    return card;
};

export const initAdminProductGallery = () => {
    document.querySelectorAll('[data-product-gallery]').forEach((gallery) => {
        const input = gallery.querySelector('[data-gallery-input]');
        const list = gallery.querySelector('[data-gallery-list]');
        const emptyState = gallery.querySelector('[data-gallery-empty]');
        const primaryField = gallery.querySelector('[data-gallery-primary]');
        const removedField = gallery.querySelector('[data-gallery-removed]');
        const stateNode = gallery.querySelector('[data-gallery-state]');

        if (!input || !list || !emptyState || !primaryField || !removedField || !stateNode) {
            return;
        }

        let images = JSON.parse(stateNode.textContent || '[]').map((image, index) => ({
            id: image.id ?? `existing-${index}`,
            name: image.name,
            label: image.label,
            isPrimary: Boolean(image.is_primary),
            preview: image.preview ?? image.path ?? null,
            source: 'existing',
        }));

        let removedExistingIds = [];
        let dt = new DataTransfer();

        const syncFields = () => {
            const primaryImage = images.find((image) => image.isPrimary);
            primaryField.value = primaryImage ? primaryImage.id : '';
            removedField.value = JSON.stringify(removedExistingIds);
            input.files = dt.files;
        };

        const render = () => {
            list.innerHTML = '';

            if (images.length === 0) {
                emptyState.classList.remove('hidden');
                syncFields();
                return;
            }

            emptyState.classList.add('hidden');

            images.forEach((image, index) => {
                list.appendChild(
                    createPreviewCard(
                        image,
                        index,
                        (selectedIndex) => {
                            images = images.map((item, itemIndex) => ({
                                ...item,
                                isPrimary: itemIndex === selectedIndex,
                            }));
                            render();
                        },
                        (selectedIndex) => {
                            const [removedImage] = images.splice(selectedIndex, 1);

                            if (removedImage?.source === 'existing') {
                                removedExistingIds.push(removedImage.id);
                            }

                            if (removedImage?.source === 'new') {
                                const rebuilt = new DataTransfer();
                                images
                                    .filter((item) => item.source === 'new' && item.file)
                                    .forEach((item) => rebuilt.items.add(item.file));
                                dt = rebuilt;
                            }

                            if (images.length > 0 && !images.some((item) => item.isPrimary)) {
                                images[0].isPrimary = true;
                            }

                            render();
                        },
                    ),
                );
            });

            syncFields();
        };

        input.addEventListener('change', (event) => {
            const files = Array.from(event.target.files || []);

            files.forEach((file, index) => {
                dt.items.add(file);
                images.push({
                    id: `new-${Date.now()}-${index}`,
                    name: file.name,
                    label: images.length === 0 && index === 0 ? 'Foto utama' : `Foto tambahan ${images.length + 1}`,
                    isPrimary: images.length === 0 && index === 0,
                    preview: URL.createObjectURL(file),
                    source: 'new',
                    file,
                });
            });

            if (images.length > 0 && !images.some((item) => item.isPrimary)) {
                images[0].isPrimary = true;
            }

            render();
        });

        render();
    });
};
