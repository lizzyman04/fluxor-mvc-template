<?php use Fluxor\View; ?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title) ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200">
    <div class="container mx-auto p-6 max-w-3xl">
        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-4"><?= View::e($post->getTitle()) ?></h1>
            <div class="prose max-w-none">
                <p class="text-gray-700 leading-relaxed"><?= nl2br(View::e($post->getContent())) ?></p>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="/posts/<?= $post->getId() ?>/edit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Edit</a>
                <button data-id="<?= $post->getId() ?>"
                    class="delete-post bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
                <a href="/posts" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">Back</a>
            </div>
        </div>
    </div>
</div>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script src="<?= View::asset('js/post.js') ?>"></script>
<?php View::endSection(); ?>