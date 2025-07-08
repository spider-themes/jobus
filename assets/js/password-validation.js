jQuery(document).ready(function($) {
    const form = $('#candidate-change-password-form');
    const newPassword = $('#new_password');
    const confirmPassword = $('#confirm_password');
    const submitButton = form.find('button[type="submit"]');

    // Password strength meter
    newPassword.on('input', function() {
        const password = $(this).val();
        const strength = checkPasswordStrength(password);
        updateStrengthMeter(strength);
    });

    // Real-time password match validation
    confirmPassword.on('input', function() {
        validatePasswordMatch();
    });

    // Form submission validation
    form.on('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
    });

    function checkPasswordStrength(password) {
        let strength = 0;

        // Length check
        if (password.length >= 8) strength++;

        // Contains lowercase
        if (/[a-z]/.test(password)) strength++;

        // Contains uppercase
        if (/[A-Z]/.test(password)) strength++;

        // Contains numbers
        if (/[0-9]/.test(password)) strength++;

        // Contains special chars
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        return strength;
    }

    function updateStrengthMeter(strength) {
        const strengthMeter = $('#password-strength');
        let text = '';
        let color = '';

        switch(strength) {
            case 1:
                text = 'Very Weak';
                color = '#ff4d4d';
                break;
            case 2:
                text = 'Weak';
                color = '#ffa64d';
                break;
            case 3:
                text = 'Medium';
                color = '#ffd11a';
                break;
            case 4:
                text = 'Strong';
                color = '#80ff80';
                break;
            case 5:
                text = 'Very Strong';
                color = '#00cc00';
                break;
            default:
                text = 'Too Short';
                color = '#ff4d4d';
        }

        strengthMeter.text(text).css('color', color);
    }

    function validatePasswordMatch() {
        const newPass = newPassword.val();
        const confirmPass = confirmPassword.val();
        const matchStatus = $('#password-match-status');

        if (confirmPass) {
            if (newPass === confirmPass) {
                matchStatus.text('Passwords match').css('color', '#00cc00');
                return true;
            } else {
                matchStatus.text('Passwords do not match').css('color', '#ff4d4d');
                return false;
            }
        }
        return false;
    }

    function validateForm() {
        const oldPassword = $('#old_password').val();
        const newPass = newPassword.val();
        const strength = checkPasswordStrength(newPass);

        if (!oldPassword) {
            alert('Please enter your current password');
            return false;
        }

        if (strength < 3) {
            alert('Please choose a stronger password');
            return false;
        }

        if (!validatePasswordMatch()) {
            alert('Passwords do not match');
            return false;
        }

        return true;
    }
});

