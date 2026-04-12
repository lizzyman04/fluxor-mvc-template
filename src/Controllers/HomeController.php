<?php

namespace Source\Controllers;

use Fluxor\Controller;
use Fluxor\Response;
use App\Core\Auth;
use App\Core\ORMHelper;
use Source\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user) {
            $posts = ORMHelper::findAllBy(Post::class, 'userId', $user['user_id']);

            return Response::view('home', [
                'title' => 'Home',
                'posts' => $posts,
                'user_logged_in' => true,
                'user_name' => $user['name'],
                'user_role' => $user['role']
            ]);
        }

        return Response::view('home', [
            'title' => 'Home',
            'message' => 'Welcome to Fluxor MVC Template',
            'user_logged_in' => false
        ]);
    }

    public function about()
    {
        return Response::view('about', [
            'title' => 'About Fluxor'
        ]);
    }
}