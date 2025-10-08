$(document).ready(function () {
    const $createForm = $('#createPostForm');
    const $editForm = $('#editPostForm');
    const $deleteButtons = $('.delete-post');
    const $errorContainer = $('<div class="fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg bg-red-100 text-red-700 hidden"></div>').appendTo('body');
    const $successContainer = $('<div class="fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg bg-green-100 text-green-700 hidden"></div>').appendTo('body');

    function showNotification($container, message) {
        $container.text(message).removeClass('hidden').fadeIn(300);
        setTimeout(() => $container.fadeOut(300, () => $container.addClass('hidden')), 3000);
    }

    // Create post
    $createForm.on('submit', function (e) {
        e.preventDefault();
        const title = $('input[name="title"]', this).val().trim();
        const content = $('textarea[name="content"]', this).val().trim();

        if (!title || !content) {
            showNotification($errorContainer, 'Title and content are required.');
            return;
        }

        $.ajax({
            url: '/posts/store',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $createForm.find('button').prop('disabled', true).text('Creating...');
            },
            success: function (response) {
                if (response.success) {
                    showNotification($successContainer, 'Post created! Redirecting...');
                    setTimeout(() => window.location.href = response.data.redirect, 1000);
                } else {
                    showNotification($errorContainer, response.data.error || 'Failed to create post.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Create post error:', textStatus, errorThrown);
                showNotification($errorContainer, 'An error occurred while creating the post.');
            },
            complete: function () {
                $createForm.find('button').prop('disabled', false).text('Create Post');
            }
        });
    });

    // Update post
    $editForm.on('submit', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const title = $('input[name="title"]', this).val().trim();
        const content = $('textarea[name="content"]', this).val().trim();

        if (!title || !content) {
            showNotification($errorContainer, 'Title and content are required.');
            return;
        }

        $.ajax({
            url: `/posts/${id}/update`,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $editForm.find('button').prop('disabled', true).text('Updating...');
            },
            success: function (response) {
                if (response.success) {
                    showNotification($successContainer, 'Post updated! Redirecting...');
                    setTimeout(() => window.location.href = response.data.redirect, 1000);
                } else {
                    showNotification($errorContainer, response.data.error || 'Failed to update post.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('Update post error:', textStatus, errorThrown);
                showNotification($errorContainer, 'An error occurred while updating the post.');
            },
            complete: function () {
                $editForm.find('button').prop('disabled', false).text('Update Post');
            }
        });
    });

    // Delete post
    $deleteButtons.on('click', function () {
        const id = $(this).data('id');
        const $modal = $(`
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-card p-6 max-w-sm w-full">
                    <h3 class="text-xl font-heading font-semibold text-gray-800 mb-4">Confirm Deletion</h3>
                    <p class="text-gray-600 font-body mb-6">Are you sure you want to delete this post?</p>
                    <div class="flex justify-end space-x-4">
                        <button class="cancel-btn bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-200">Cancel</button>
                        <button class="confirm-btn bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">Delete</button>
                    </div>
                </div>
            </div>
        `).appendTo('body');

        $modal.find('.cancel-btn').one('click', function () {
            $modal.fadeOut(300, function () { $modal.remove(); });
        });

        $modal.find('.confirm-btn').one('click', function () {
            $.ajax({
                url: `/posts/${id}/delete`,
                method: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $modal.find('.confirm-btn').prop('disabled', true).text('Deleting...');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification($successContainer, 'Post deleted! Redirecting...');
                        setTimeout(() => window.location.href = response.data.redirect, 1000);
                    } else {
                        showNotification($errorContainer, response.data.error || 'Failed to delete post.');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Delete post error:', textStatus, errorThrown);
                    showNotification($errorContainer, 'An error occurred while deleting the post.');
                },
                complete: function () {
                    $modal.fadeOut(300, function () { $modal.remove(); });
                }
            });
        });

        $modal.on('click', function (e) {
            if ($(e.target).is($modal)) {
                $modal.fadeOut(300, function () { $modal.remove(); });
            }
        });
    });
});