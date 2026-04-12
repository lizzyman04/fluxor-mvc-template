<?php use Fluxor\View; ?>
<?php View::extend('layouts/main'); ?>

<?php View::section('title'); ?>
<?= View::e($title) ?>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200">
    <div class="container mx-auto p-6 max-w-2xl">
        <div class="bg-white rounded-lg shadow p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6"><?= View::e($title) ?></h1>
            <form id="editPostForm" data-id="<?= $post->getId() ?>" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= View::e($csrf_token) ?>">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Title</label>
                    <input type="text" name="title" value="<?= View::e($post->getTitle()) ?>" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Content</label>
                    <textarea name="content" required rows="10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"><?= View::e($post->getContent()) ?></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <a href="/posts/<?= $post->getId() ?>"
                        class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400">Cancel</a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php View::endSection(); ?>

<?php View::section('scripts'); ?>
<script src="<?= View::asset('js/post.js') ?>"></script>
<?php View::endSection(); ?>