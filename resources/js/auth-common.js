// resources/js/auth-common.js
// Common JavaScript functions for authentication pages

/**
 * Toggle password visibility
 * @param {string} fieldId - The ID of the password input field
 */
export function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const eyeIcon = document.getElementById('eye-icon-' + fieldId);

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.textContent = "visibility";
    } else {
        passwordField.type = 'password';
        eyeIcon.textContent = "visibility_off";
    }
}

/**
 * Calculate password strength
 * @param {string} password - The password to check
 * @returns {number} - Strength score from 0-5
 */
export function calculatePasswordStrength(password) {
    let strength = 0;

    // Check length
    if (password.length >= 8) strength += 1;
    // Check for uppercase
    if (/[A-Z]/.test(password)) strength += 1;
    // Check for lowercase
    if (/[a-z]/.test(password)) strength += 1;
    // Check for numbers
    if (/\d/.test(password)) strength += 1;
    // Check for special characters
    if (/[^A-Za-z0-9]/.test(password)) strength += 1;

    return strength;
}

/**
 * Get password strength message
 * @param {number} strength - The strength score
 * @returns {object} - Object with className and message
 */
export function getPasswordStrengthInfo(strength) {
    const strengthInfo = {
        0: { className: 'strength-weak', message: 'Weak password. Use at least 8 characters including uppercase, lowercase, number, and special character.' },
        1: { className: 'strength-weak', message: 'Weak password. Use at least 8 characters including uppercase, lowercase, number, and special character.' },
        2: { className: 'strength-medium', message: 'Medium password. Add more variety to strengthen it.' },
        3: { className: 'strength-medium', message: 'Good password. Consider adding a special character for maximum security.' },
        4: { className: 'strength-strong', message: 'Strong password. You\'re doing great!' },
        5: { className: 'strength-very-strong', message: 'Very strong password. Excellent security!' }
    };

    return strengthInfo[strength];
}

/**
 * Initialize password strength indicator
 */
export function initPasswordStrength() {
    const passwordInput = document.getElementById('password');
    const passwordStrength = document.getElementById('password-strength');
    const passwordHelp = document.getElementById('password-help');

    if (passwordInput && passwordStrength) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            const info = getPasswordStrengthInfo(strength);

            // Update strength indicator
            passwordStrength.className = 'password-strength ' + info.className;
            passwordHelp.textContent = info.message;
        });
    }
}

/**
 * Auto-focus first input on mobile devices
 */
export function autoFocusMobile() {
    if (window.innerWidth < 768) {
        const firstInput = document.querySelector('input[type="email"], input[type="text"]');
        if (firstInput) {
            firstInput.focus();
        }
    }
}

/**
 * Initialize all common authentication features
 */
export function initAuthCommon() {
    document.addEventListener('DOMContentLoaded', function() {
        initPasswordStrength();
        autoFocusMobile();

        // Make togglePassword available globally for inline onclick handlers
        window.togglePassword = togglePassword;
    });
}

// Auto-initialize if this script is loaded
if (document.readyState === 'loading') {
    initAuthCommon();
} else {
    // DOMContentLoaded already fired
    initPasswordStrength();
    autoFocusMobile();
    window.togglePassword = togglePassword;
}
