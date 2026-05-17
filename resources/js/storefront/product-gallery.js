export const initStorefrontProductGallery = () => {
    document.querySelectorAll('[data-storefront-gallery]').forEach((gallery) => {
        const mainImage = gallery.querySelector('[data-gallery-main-image]');
        const mainTrigger = gallery.querySelector('[data-gallery-main-trigger]');
        const thumbnails = Array.from(gallery.querySelectorAll('[data-gallery-thumb]'));
        const lightbox = gallery.querySelector('[data-gallery-lightbox]');
        const lightboxImage = gallery.querySelector('[data-gallery-lightbox-image]');
        const lightboxLabel = gallery.querySelector('[data-gallery-lightbox-label]');
        const closeButton = gallery.querySelector('[data-gallery-close]');
        const prevButton = gallery.querySelector('[data-gallery-prev]');
        const nextButton = gallery.querySelector('[data-gallery-next]');

        if (!mainImage || thumbnails.length === 0) {
            return;
        }

        let activeIndex = Math.max(
            0,
            thumbnails.findIndex((thumbnail) => thumbnail.getAttribute('aria-pressed') === 'true'),
        );
        let touchStartX = 0;

        const syncView = () => {
            const activeThumbnail = thumbnails[activeIndex];

            if (!activeThumbnail) {
                return;
            }

            const nextImage = activeThumbnail.getAttribute('data-gallery-image');
            const nextAlt = activeThumbnail.getAttribute('data-gallery-alt');
            const nextLabel = activeThumbnail.textContent?.trim() ?? '';

            if (nextImage) {
                mainImage.src = nextImage;
                mainImage.alt = nextAlt || mainImage.alt;
            }

            if (lightboxImage && nextImage) {
                lightboxImage.src = nextImage;
                lightboxImage.alt = nextAlt || mainImage.alt;
            }

            if (lightboxLabel) {
                lightboxLabel.textContent = nextLabel;
            }
        };

        const setActiveThumbnail = (activeThumbnail) => {
            thumbnails.forEach((thumbnail) => {
                const isActive = thumbnail === activeThumbnail;
                thumbnail.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                thumbnail.classList.toggle('border-emerald-500', isActive);
                thumbnail.classList.toggle('ring-2', isActive);
                thumbnail.classList.toggle('ring-emerald-200', isActive);
                thumbnail.classList.toggle('bg-emerald-50/70', isActive);
                thumbnail.classList.toggle('border-stone-200', !isActive);
                thumbnail.classList.toggle('bg-white', !isActive);
            });
        };

        const setActiveIndex = (index) => {
            activeIndex = (index + thumbnails.length) % thumbnails.length;
            const activeThumbnail = thumbnails[activeIndex];
            setActiveThumbnail(activeThumbnail);
            syncView();
        };

        const openLightbox = () => {
            if (!lightbox) {
                return;
            }

            syncView();
            lightbox.classList.remove('hidden');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        };

        const closeLightbox = () => {
            if (!lightbox) {
                return;
            }

            lightbox.classList.add('hidden');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        };

        thumbnails.forEach((thumbnail, index) => {
            thumbnail.addEventListener('click', () => {
                setActiveIndex(index);
            });
        });

        if (mainTrigger) {
            mainTrigger.addEventListener('click', openLightbox);

            mainTrigger.addEventListener('touchstart', (event) => {
                touchStartX = event.changedTouches[0]?.clientX ?? 0;
            });

            mainTrigger.addEventListener('touchend', (event) => {
                const touchEndX = event.changedTouches[0]?.clientX ?? 0;
                const deltaX = touchEndX - touchStartX;

                if (Math.abs(deltaX) < 40) {
                    return;
                }

                if (deltaX < 0) {
                    setActiveIndex(activeIndex + 1);
                    return;
                }

                setActiveIndex(activeIndex - 1);
            });
        }

        closeButton?.addEventListener('click', closeLightbox);
        prevButton?.addEventListener('click', () => setActiveIndex(activeIndex - 1));
        nextButton?.addEventListener('click', () => setActiveIndex(activeIndex + 1));

        lightbox?.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (!lightbox || lightbox.classList.contains('hidden')) {
                return;
            }

            if (event.key === 'Escape') {
                closeLightbox();
            }

            if (event.key === 'ArrowLeft') {
                setActiveIndex(activeIndex - 1);
            }

            if (event.key === 'ArrowRight') {
                setActiveIndex(activeIndex + 1);
            }
        });

        setActiveIndex(activeIndex);
    });
};
