<?php

namespace Source\Controllers;

use Core\Helpers\AuthHelper;
use Core\Helpers\ORMHelper;
use Source\Models\Post;
use Core\View;

class HomeController
{
    public function index()
    {
        $credentials = AuthHelper::check();

        if ($credentials) {
            $posts = ORMHelper::select(Post::class)
                ->where('user_id', $credentials['user_id'])
                ->fetchAll();

            View::render('home', [
                'title' => 'Home',
                'posts' => $posts,
                'user_logged_in' => true,
                'user_name' => $credentials['name'],
                'user_role' => $credentials['role']
            ]);
        } else {
            View::render('home', [
                'title' => 'Home',
                'message' => 'You are not logged in.',
                'user_logged_in' => false
            ]);
        }
    }

    public function notFound()
    {
        http_response_code(404);
        View::render('404', [
            'title' => 'Page Not Found',
            'message' => 'Page not found.',
        ]);
    }
}