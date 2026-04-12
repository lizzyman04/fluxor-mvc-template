<?php use Fluxor\View; ?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
404 - Page Not Found
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-card p-8 text-center max-w-md mx-auto">
        <h1 class="text-6xl font-heading font-bold text-clifford mb-4">404</h1>
        <p class="text-lg font-body text-gray-600 mb-6">Oops! The page you are looking for does not exist.</p>
        <a href="/"
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">Go
            Back Home</a>
    </div>
</div>
<?php View::endSection(); ?>