<?php

namespace Source\Controllers;

use Core\View;
use Source\Models\Post;
use Core\Helpers\ORMHelper;
use Core\Helpers\AuthHelper;
use Core\Helpers\ResponseHelper;
use Source\Models\User;

class PostController
{
    public function index()
    {
        $credentials = AuthHelper::check();
        if (!$credentials) {
            header('Location: /auth');
            exit;
        }

        $posts = ORMHelper::select(Post::class)
            ->where('user_id', $credentials['user_id'])
            ->fetchAll();

        View::render('modules/posts/index', [
            'title' => 'My Posts',
            'posts' => $posts,
            'user' => $credentials
        ]);
    }

    public function show($id)
    {
        $credentials = AuthHelper::check();
        if (!$credentials) {
            header('Location: /auth');
            exit;
        }

        $post = ORMHelper::select(Post::class)
            ->where('id', $id)
            ->where('user_id', $credentials['user_id'])
            ->fetchOne();

        if (!$post) {
            View::render('404', [
                'title' => 'Post Not Found',
                'message' => 'The post you are looking for does not exist or you do not have permission to view it.'
            ]);
            return;
        }

        View::render('modules/posts/show', [
            'title' => $post->title,
            'post' => $post,
            'user' => $credentials
        ]);
    }

    public function create()
    {
        $credentials = AuthHelper::check();
        if (!$credentials) {
            header('Location: /auth');
            exit;
        }

        View::render('modules/posts/create', [
            'title' => 'Create Post',
            'user' => $credentials,
            'csrf_token' => AuthHelper::csrfToken()
        ]);
    }

    public function store()
    {
        $credentials = AuthHelper::check();
        if (!$credentials) {
            ResponseHelper::error('Unauthorized', 401);
        }

        if (!AuthHelper::validateCsrf($_POST['csrf_token'] ?? '')) {
            ResponseHelper::error('Invalid CSRF token', 419);
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        if (empty($title) || empty($content)) {
            ResponseHelper::error('Title and content are required', 400);
        }

        $user = ORMHelper::select(User::class)
            ->where('id', $credentials['user_id'])
            ->fetchOne();

        $post = new Post();
        $post->title = $title;
        $post->content = $content;
        $post->user = $user;

        $manager = ORMHelper::getManager();
        $manager->persist($post);
        $manager->run();

        ResponseHelper::success([
            'message' => 'Post created successfully',
            'redirect' => '/posts'
        ], 201);
    }

    public function edit($id)
    {
        $credentials = AuthHelper::check();
        if (!$credentials) {
            header('Location: /auth');
            exit;
        }

        $post = ORMHelper::select(Post::class)
            ->where('id', $id)
            ->where('user_id', $credentials['user_id'])
            ->fetchOne();

        if (!$post) {
            View::render('404', [
                'title' => 'Post Not Found',
                'message' => 'The post you are trying to edit does not exist or you do not have permission to edit it.'
            ]);
            return;
        }

        View::render('modules/posts/edit', [
            'title' => 'Edit Post',
            'post' => $post,
            'user' => $credentials,
            'csrf_token' => AuthHelper::csrfToken()
        ]);
    }

    public function update($id)
    {
        $credentials = AuthHelper::check();
        if (!$credentials) {
            ResponseHelper::error('Unauthorized', 401);
        }

        if (!AuthHelper::validateCsrf($_POST['csrf_token'] ?? '')) {
            ResponseHelper::error('Invalid CSRF token', 419);
        }

        $post = ORMHelper::select(Post::class)
            ->where('id', $id)
            ->where('user_id', $credentials['user_id'])
            ->fetchOne();

        if (!$post) {
            ResponseHelper::error('Post not found', 404);
        }

        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';

        if (empty($title) || empty($content)) {
            ResponseHelper::error('Title and content are required', 400);
        }

        $post->title = $title;
        $post->content = $content;

        $manager = ORMHelper::getManager();
        $manager->persist($post);
        $manager->run();

        ResponseHelper::success([
            'message' => 'Post updated successfully',
            'redirect' => '/posts/' . $id
        ]);
    }

    public function delete($id)
    {
        $credentials = AuthHelper::check();
        if (!$credentials) {
            ResponseHelper::error('Unauthorized', 401);
        }

        $post = ORMHelper::select(Post::class)
            ->where('id', $id)
            ->where('user_id', $credentials['user_id'])
            ->fetchOne();

        if (!$post) {
            ResponseHelper::error('Post not found', 404);
        }

        $manager = ORMHelper::getManager();
        $manager->delete($post);
        $manager->run();

        ResponseHelper::success([
            'message' => 'Post deleted successfully',
            'redirect' => '/posts'
        ]);
    }
}