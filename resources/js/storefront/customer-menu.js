export const initCustomerMenu = () => {
    document.querySelectorAll('[data-customer-menu]').forEach((menu) => {
        const trigger = menu.querySelector('[data-customer-menu-trigger]');
        const panel = menu.querySelector('[data-customer-menu-panel]');

        if (!trigger || !panel) {
            return;
        }

        const closeMenu = () => {
            panel.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
        };

        const openMenu = () => {
            panel.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
        };

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();

            if (panel.classList.contains('hidden')) {
                document.querySelectorAll('[data-customer-menu-panel]').forEach((otherPanel) => {
                    otherPanel.classList.add('hidden');
                });
                document.querySelectorAll('[data-customer-menu-trigger]').forEach((otherTrigger) => {
                    otherTrigger.setAttribute('aria-expanded', 'false');
                });
                openMenu();
                return;
            }

            closeMenu();
        });

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target)) {
                closeMenu();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeMenu();
            }
        });
    });
};
