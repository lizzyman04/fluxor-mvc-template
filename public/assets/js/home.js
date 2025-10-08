$(document).ready(function () {
    // Cache selector
    const $postLink = $('#post-a-new-blog');

    // Create modal for confirmation
    const $modal = $(`
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-card p-6 max-w-sm w-full">
                <h3 class="text-xl font-heading font-semibold text-gray-800 mb-4">Confirm New Post</h3>
                <p class="text-gray-600 font-body mb-6">Are you sure you want to post a new blog?</p>
                <div class="flex justify-end space-x-4">
                    <button class="cancel-btn bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-200">Cancel</button>
                    <button class="confirm-btn bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">Confirm</button>
                </div>
            </div>
        </div>
    `).appendTo('body');

    // Handle post link click
    $postLink.on('click', function (e) {
        e.preventDefault();
        const href = $(this).attr('href');
        $modal.removeClass('hidden').fadeIn(300);

        // Cancel button
        $modal.find('.cancel-btn').one('click', function () {
            $modal.fadeOut(300, function () {
                $modal.addClass('hidden');
            });
        });

        // Confirm button
        $modal.find('.confirm-btn').one('click', function () {
            window.location.href = href;
        });
    });

    // Close modal on click outside
    $modal.on('click', function (e) {
        if ($(e.target).is($modal)) {
            $modal.fadeOut(300, function () {
                $modal.addClass('hidden');
            });
        }
    });
});