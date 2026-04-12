<?php use Fluxor\View; ?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title) ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-md w-full">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Login</h1>
        <form id="loginForm" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= View::e($csrf_token) ?>">
            <input type="hidden" name="redirect" value="<?= View::e($redirect) ?>">
            <div>
                <input type="email" name="email" placeholder="Email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-gray-600">Remember me</span>
                </label>
                <a href="/auth/register" class="text-blue-600 hover:underline">Register</a>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold">Login</button>
        </form>
    </div>
</div>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script src="<?= View::asset('js/auth.js') ?>"></script>
<?php View::endSection(); ?>