$(document).ready(function () {
    const $loginForm = $('#loginForm');
    const $registerForm = $('#registerForm');
    const $errorContainer = $('<div class="fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg bg-red-100 text-red-700 hidden"></div>').appendTo('body');
    const $successContainer = $('<div class="fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg bg-green-100 text-green-700 hidden"></div>').appendTo('body');

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function showNotification($container, message) {
        $container.text(message).removeClass('hidden').fadeIn(300);
        setTimeout(() => $container.fadeOut(300, () => $container.addClass('hidden')), 3000);
    }

    // Login form submission
    $loginForm.on('submit', function (e) {
        e.preventDefault();

        const email = $('input[name="email"]', this).val().trim();
        const password = $('input[name="password"]', this).val();

        if (!email || !password) {
            showNotification($errorContainer, 'Please fill in all fields for login.');
            return;
        }
        if (!emailRegex.test(email)) {
            showNotification($errorContainer, 'Please enter a valid email address.');
            return;
        }

        $.ajax({
            url: '/auth/login',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $loginForm.find('button').prop('disabled', true).text('Logging in...');
            },
            success: function (response) {
                if (response.success) {
                    showNotification($successContainer, 'Login successful! Redirecting...');
                    setTimeout(() => window.location.href = response.data.redirect || '/', 1000);
                } else {
                    showNotification($errorContainer, response.message || 'Login failed.');
                }
            },
            error: function (jqXHR) {
                const errorMsg = jqXHR.responseJSON?.message || 'An error occurred during login.';
                showNotification($errorContainer, errorMsg);
            },
            complete: function () {
                $loginForm.find('button').prop('disabled', false).text('Login');
            }
        });
    });

    // Register form submission
    $registerForm.on('submit', function (e) {
        e.preventDefault();

        const name = $('input[name="name"]', this).val().trim();
        const email = $('input[name="email"]', this).val().trim();
        const password = $('input[name="password"]', this).val();

        if (!name || !email || !password) {
            showNotification($errorContainer, 'Please fill in all required fields for registration.');
            return;
        }
        if (!emailRegex.test(email)) {
            showNotification($errorContainer, 'Please enter a valid email address.');
            return;
        }

        $.ajax({
            url: '/auth/register',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $registerForm.find('button').prop('disabled', true).text('Registering...');
            },
            success: function (response) {
                if (response.success) {
                    showNotification($successContainer, 'Registration successful! Redirecting...');
                    setTimeout(() => window.location.href = response.data.redirect || '/', 1000);
                } else {
                    showNotification($errorContainer, response.message || 'Registration failed.');
                }
            },
            error: function (jqXHR) {
                const errorMsg = jqXHR.responseJSON?.message || 'An error occurred during registration.';
                showNotification($errorContainer, errorMsg);
            },
            complete: function () {
                $registerForm.find('button').prop('disabled', false).text('Register');
            }
        });
    });
});