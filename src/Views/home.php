<?php
$title = 'Home';
ob_start();
?>
<div class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-200 flex flex-col">
    <div class="container mx-auto p-6 flex-grow">
        <h1 class="text-4xl font-heading font-bold text-gray-800 text-center mb-8">Welcome to the Blog</h1>

        <?php if ($user_logged_in): ?>
            <div class="bg-white rounded-lg shadow-card p-6 mb-6">
                <p class="text-lg font-body text-gray-600">Logged in as <span
                        class="font-semibold text-clifford"><?= htmlspecialchars($user_name) ?></span>!</p>
            </div>

            <h2 class="text-2xl font-heading font-semibold text-gray-700 mb-4">Your Blogs</h2>

            <p class="<?php echo count($posts) === 0 ? 'text-gray-500 italic' : 'text-gray-600'; ?> mb-6">
                <?php echo count($posts) === 0 ? 'No blogs yet.' : ''; ?>
            </p>

            <?php if (count($posts) > 0): ?>
                <ul class="space-y-6">
                    <?php foreach ($posts as $post): ?>
                        <li class="bg-white rounded-lg shadow-card p-6">
                            <h3 class="text-xl font-heading font-semibold text-gray-800">
                                <a href="/posts/<?= $post->id ?>" class="text-blue-600 hover:text-blue-800 transition duration-200">
                                    <?= htmlspecialchars($post->title) ?>
                                </a>
                            </h3>
                            <p class="mt-2 text-gray-600 font-body"><?= nl2br(htmlspecialchars($post->content)) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="mt-6 flex space-x-4">
                <a href="/new-post"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold"
                    id="post-a-new-blog">Post
                    a new blog</a>
                <a href="/logout"
                    class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition duration-200 font-semibold">Logout</a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-card p-6 text-center">
                <p class="text-lg font-body text-gray-600 mb-4"><?= htmlspecialchars($message) ?></p>
                <div class="flex justify-center space-x-4">
                    <a href="/auth"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">Login</a>
                    <a href="/auth"
                        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200 font-semibold">Register</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="<?= BASE_URL ?>assets/js/home.js" defer></script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/layouts/main.php';
?>