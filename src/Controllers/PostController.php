<?php

namespace Source\Controllers;

use Fluxor\Controller;
use Fluxor\Response;
use App\Core\Auth;
use App\Core\ORMHelper;
use Source\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::requireAuth();

        $posts = ORMHelper::findAllBy(Post::class, 'userId', $user['user_id']);

        return Response::view('posts/index', [
            'title' => 'My Posts',
            'posts' => $posts,
            'user' => $user,
        ]);
    }

    public function show()
    {
        error_log("Called");
        $user = Auth::requireAuth();
        $id = $this->request->param('id');

        $post = ORMHelper::findOneBy(Post::class, 'id', $id);

        if (!$post || $post->getUserId() !== $user['user_id']) {
            return Response::view('errors/404', [
                'title' => 'Post Not Found',
                'message' => 'The post you are looking for does not exist or you do not have permission to view it.',
            ], 404);
        }

        return Response::view('posts/show', [
            'title' => $post->getTitle(),
            'post' => $post,
            'user' => $user,
        ]);
    }

    public function create()
    {
        $user = Auth::requireAuth();

        return Response::view('posts/create', [
            'title' => 'Create Post',
            'user' => $user,
            'csrf_token' => Auth::csrfToken(),
        ]);
    }

    public function store()
    {
        $user = Auth::requireAuth();

        if (!Auth::validateCsrf($this->request->input('csrf_token'))) {
            return Response::error('Invalid CSRF token', 419);
        }

        $title = $this->request->input('title');
        $content = $this->request->input('content');

        if (empty($title) || empty($content)) {
            return Response::error('Title and content are required', 400);
        }

        $post = new Post($title, $content, $user['user_id']);

        $manager = ORMHelper::getManager();
        $manager->persist($post);
        $manager->run();

        return Response::success(['redirect' => '/posts'], 'Post created successfully', 201);
    }

    public function edit()
    {
        $user = Auth::requireAuth();
        $id = $this->request->param('id');

        $post = ORMHelper::findOneBy(Post::class, 'id', $id);

        if (!$post || $post->getUserId() !== $user['user_id']) {
            return Response::view('errors/404', [
                'title' => 'Post Not Found',
                'message' => 'The post you are trying to edit does not exist or you do not have permission to edit it.',
            ], 404);
        }

        return Response::view('posts/edit', [
            'title' => 'Edit Post',
            'post' => $post,
            'user' => $user,
            'csrf_token' => Auth::csrfToken(),
        ]);
    }

    public function update()
    {
        $user = Auth::requireAuth();
        $id = $this->request->param('id');

        if (!Auth::validateCsrf($this->request->input('csrf_token'))) {
            return Response::error('Invalid CSRF token', 419);
        }

        $post = ORMHelper::findOneBy(Post::class, 'id', $id);

        if (!$post || $post->getUserId() !== $user['user_id']) {
            return Response::error('Post not found', 404);
        }

        $title = $this->request->input('title');
        $content = $this->request->input('content');

        if (empty($title) || empty($content)) {
            return Response::error('Title and content are required', 400);
        }

        $post->setTitle($title);
        $post->setContent($content);

        $manager = ORMHelper::getManager();
        $manager->persist($post);
        $manager->run();

        return Response::success(['redirect' => "/posts/{$id}"], 'Post updated successfully');
    }

    public function delete()
    {
        $user = Auth::requireAuth();
        $id = $this->request->param('id');

        $post = ORMHelper::findOneBy(Post::class, 'id', $id);

        if (!$post || $post->getUserId() !== $user['user_id']) {
            return Response::error('Post not found', 404);
        }

        $manager = ORMHelper::getManager();
        $manager->delete($post);
        $manager->run();

        return Response::success(['redirect' => '/posts'], 'Post deleted successfully');
    }
}