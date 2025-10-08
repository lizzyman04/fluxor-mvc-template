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
            url: '/login',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $loginForm.find('button').prop('disabled', true).text('Logging in...');
            },
            success: function (response) {
                if (response.success) {
                    showNotification($successContainer, 'Login successful! Redirecting...');
                    setTimeout(() => window.location.href = response.data.redirect ?? '/', 1000);
                } else {
                    showNotification($errorContainer, response.data.error || 'Login failed.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Login error:', textStatus, errorThrown);
                showNotification($errorContainer, 'An error occurred during login.');
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
            url: '/register',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $registerForm.find('button').prop('disabled', true).text('Registering...');
            },
            success: function (data) {
                if (response.data.success) {
                    showNotification($successContainer, 'Registration successful! Redirecting...');
                    setTimeout(() => window.location.href = response.data.redirect, 1000);
                } else {
                    showNotification($errorContainer, response.data.error || 'Registration failed.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Registration error:', textStatus, errorThrown);
                showNotification($errorContainer, 'An error occurred during registration.');
            },
            complete: function () {
                $registerForm.find('button').prop('disabled', false).text('Register');
            }
        });
    });
});