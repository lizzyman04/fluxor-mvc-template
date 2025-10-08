<?php
$title = 'Edit Post';
ob_start();
?>
<div class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
    <div class="container mx-auto p-6 max-w-2xl">
        <div class="bg-white rounded-lg shadow-card p-8">
            <h1 class="text-3xl font-heading font-bold text-gray-800 mb-6">Edit Post</h1>
            <form id="editPostForm" data-id="<?= $post->id ?>" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                <div>
                    <label for="title" class="block text-gray-700 font-semibold mb-2">Title</label>
                    <input type="text" name="title" id="title" value="<?= htmlspecialchars($post->title) ?>" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                </div>
                <div>
                    <label for="content" class="block text-gray-700 font-semibold mb-2">Content</label>
                    <textarea name="content" id="content" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 h-40"><?= htmlspecialchars($post->content) ?></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <a href="/posts/<?= $post->id ?>"
                        class="bg-gray-300 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-400 transition duration-200 font-semibold">Cancel</a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">Update
                        Post</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/post.js" defer></script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/../../layouts/main.php';
?>