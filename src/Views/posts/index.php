<?php use Fluxor\View; ?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title) ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200">
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800"><?= View::e($title) ?></h1>
            <a href="/posts/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Create
                Post</a>
        </div>

        <?php if (empty($posts)): ?>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-500">No posts yet. Create your first post!</p>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($posts as $post): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-800">
                            <a href="/posts/<?= $post->getId() ?>"
                                class="hover:text-blue-600"><?= View::e($post->getTitle()) ?></a>
                        </h2>
                        <p class="text-gray-600 mt-2"><?= nl2br(View::e($post->getExcerpt())) ?></p>
                        <div class="mt-4 flex space-x-3">
                            <a href="/posts/<?= $post->getId() ?>/edit" class="text-blue-600 hover:underline">Edit</a>
                            <button data-id="<?= $post->getId() ?>"
                                class="delete-post text-red-600 hover:underline">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script src="<?= View::asset('js/post.js') ?>"></script>
<?php View::endSection(); ?>