document.addEventListener('DOMContentLoaded', function() {
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const mobileSignUp = document.getElementById('mobileSignUp');
    const mobileSignIn = document.getElementById('mobileSignIn');
    const container = document.getElementById('container');
    const registerForm = document.getElementById('registerForm');
    
    // Only proceed if elements exist (for pages that have them)
    if (registerForm) {
        // Form validation for registration - client-side only
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            clearErrors(registerForm);

            // Validate username
            const username = document.getElementById('regUsername').value;
            if (username.length < 4) {
                showError('username_error', 'Username must be at least 4 characters');
                isValid = false;
            }

            // Validate email
            const email = document.getElementById('regEmail').value;
            if (!validateEmail(email)) {
                showError('email_error', 'Please enter a valid email');
                isValid = false;
            }

            // Validate password
            const password = document.getElementById('regPassword').value;
            if (password.length < 6) {
                showError('password_error', 'Password must be at least 6 characters');
                isValid = false;
            }

            // Validate confirm password
            const confirmPassword = document.getElementById('regConfirmPassword').value;
            if (password !== confirmPassword) {
                showError('confirm_password_error', 'Passwords do not match');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                registerForm.classList.add('shake');
                setTimeout(() => {
                    registerForm.classList.remove('shake');
                }, 500);
            }
            // If valid, allow form to submit to PHP
        });

        // Real-time validation for better UX (Registration)
        document.getElementById('regUsername').addEventListener('input', function() {
            if (this.value.length >= 4) {
                validateField(this, 'username_error');
            }
        });

        document.getElementById('regEmail').addEventListener('input', function() {
            if (validateEmail(this.value)) {
                validateField(this, 'email_error');
            }
        });

        document.getElementById('regPassword').addEventListener('input', function() {
            if (this.value.length >= 6) {
                validateField(this, 'password_error');
            }
        });

        document.getElementById('regConfirmPassword').addEventListener('input', function() {
            const password = document.getElementById('regPassword').value;
            if (this.value === password && password.length >= 6) {
                validateField(this, 'confirm_password_error');
            }
        });
    }

    // Toggle between sign-in and sign-up forms
    function toggleForms(showSignUp) {
        if (showSignUp) {
            container.classList.add('right-panel-active');
        } else {
            container.classList.remove('right-panel-active');
        }
    }

    // Desktop buttons
    if (signUpButton) signUpButton.addEventListener('click', () => toggleForms(true));
    if (signInButton) signInButton.addEventListener('click', () => toggleForms(false));

    // Mobile buttons
    if (mobileSignUp) mobileSignUp.addEventListener('click', () => toggleForms(true));
    if (mobileSignIn) mobileSignIn.addEventListener('click', () => toggleForms(false));

    // Helper functions
    function showError(elementId, message) {
        const element = document.getElementById(elementId);
        if (element) {
            const errorContainer = element.closest('.error-message');
            const inputField = errorContainer.previousElementSibling;
            
            element.textContent = message;
            errorContainer.classList.add('show');
            inputField.classList.add('error');
            inputField.classList.remove('valid');
        }
    }

    function clearErrors(form) {
        form.querySelectorAll('.error-message').forEach(el => {
            el.classList.remove('show');
        });
        form.querySelectorAll('input').forEach(el => {
            el.classList.remove('error', 'valid');
        });
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function validateField(inputElement, errorElementId) {
        const errorContainer = document.getElementById(errorElementId).closest('.error-message');
        inputElement.classList.remove('error');
        inputElement.classList.add('valid');
        errorContainer.classList.remove('show');
    }
});