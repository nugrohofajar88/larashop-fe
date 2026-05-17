export const initStorefrontProfile = () => {
    document.querySelectorAll('[data-customer-profile-form]').forEach((form) => {
        const submitButton = form.querySelector('[data-profile-submit]');
        const feedback = form.querySelector('[data-profile-feedback]');
        const nameField = form.querySelector('[name="name"]');
        const usernameField = form.querySelector('[name="username"]');
        const phoneField = form.querySelector('[name="phone"]');
        const emailField = form.querySelector('[name="email"]');
        const passwordField = form.querySelector('[name="password"]');
        const passwordConfirmationField = form.querySelector('[name="password_confirmation"]');

        if (
            !submitButton ||
            !feedback ||
            !nameField ||
            !usernameField ||
            !phoneField ||
            !emailField ||
            !passwordField ||
            !passwordConfirmationField
        ) {
            return;
        }

        const hideFeedback = () => {
            feedback.classList.add('hidden');
            feedback.textContent = '';
        };

        const showFeedback = (message) => {
            feedback.textContent = message;
            feedback.classList.remove('hidden');
        };

        const validate = () => {
            if (nameField.value.trim() === '') {
                return 'Nama lengkap wajib diisi.';
            }

            if (usernameField.value.trim() === '') {
                return 'Username wajib diisi.';
            }

            if (phoneField.value.trim() === '') {
                return 'Nomor WhatsApp wajib diisi.';
            }

            const emailValue = emailField.value.trim();
            if (emailValue !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                return 'Format email belum valid.';
            }

            const passwordValue = passwordField.value;
            const passwordConfirmationValue = passwordConfirmationField.value;

            if (passwordValue !== '' || passwordConfirmationValue !== '') {
                if (passwordValue.length < 8) {
                    return 'Password baru minimal 8 karakter.';
                }

                if (passwordValue !== passwordConfirmationValue) {
                    return 'Konfirmasi password baru belum sama.';
                }
            }

            return null;
        };

        const updateState = () => {
            const validationMessage = validate();
            const isValid = validationMessage === null;

            submitButton.disabled = !isValid;
            submitButton.classList.toggle('opacity-60', !isValid);
            submitButton.classList.toggle('cursor-not-allowed', !isValid);

            if (validationMessage) {
                showFeedback(validationMessage);
                return;
            }

            hideFeedback();
        };

        [nameField, usernameField, phoneField, emailField, passwordField, passwordConfirmationField].forEach((field) => {
            field.addEventListener('input', updateState);
        });

        form.addEventListener('submit', (event) => {
            const validationMessage = validate();

            if (validationMessage) {
                event.preventDefault();
                showFeedback(validationMessage);
                return;
            }

            hideFeedback();
        });

        updateState();
    });
};
