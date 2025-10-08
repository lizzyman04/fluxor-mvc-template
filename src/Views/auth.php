<?php
$title = 'Authentication';
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
    <div class="container mx-auto max-w-4xl p-6">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="text-center py-8">
                <h1 class="text-4xl font-heading font-bold text-gray-800">Authentication</h1>
            </div>
            <div class="grid md:grid-cols-2 gap-8 p-8">
                <!-- Login Form -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-heading font-semibold text-gray-700">Login</h2>
                    <form id="loginForm" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div>
                            <input type="text" name="email" placeholder="Email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </div>
                        <div>
                            <input type="password" name="password" placeholder="Password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                            Login
                        </button>
                    </form>
                </div>
                <!-- Register Form -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-heading font-semibold text-gray-700">Register</h2>
                    <form id="registerForm" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <div>
                            <input type="text" name="name" placeholder="Name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </div>
                        <div>
                            <input type="text" name="email" placeholder="Email" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </div>
                        <div>
                            <input type="password" name="password" placeholder="Password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </div>
                        <div>
                            <input type="text" name="address" placeholder="Address (optional)"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                        </div>
                        <button type="submit"
                            class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition duration-200 font-semibold">
                            Register
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/auth.js"></script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layouts/main.php';
?>