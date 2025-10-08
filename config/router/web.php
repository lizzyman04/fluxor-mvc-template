<?php

return [
    '/' => 'HomeController@index',
    '/about' => 'HomeController@about',
    '/contact' => 'HomeController@contact',

    // Auth routes
    '/auth' => 'AuthController@showAuth',
    '/login' => [
        'methods' => ['POST'],
        'controller' => 'AuthController@login'
    ],
    '/register' => [
        'methods' => ['POST'],
        'controller' => 'AuthController@register'
    ],
    '/logout' => [
        'methods' => ['GET', 'POST'],
        'controller' => 'AuthController@logout'
    ],

    // Post routes
    '/posts' => 'PostController@index',
    '/posts/{id}' => [
        'methods' => ['GET'],
        'controller' => 'PostController@show'
    ],
    '/posts/create' => [
        'methods' => ['GET', 'POST'],
        'controller' => 'PostController@create'
    ],
    '/posts/store' => [
        'methods' => ['POST'],
        'controller' => 'PostController@store'
    ],
    '/posts/{id}/edit' => [
        'methods' => ['GET', 'POST'],
        'controller' => 'PostController@edit'
    ],
    '/posts/{id}/update' => [
        'methods' => ['POST'],
        'controller' => 'PostController@update'
    ],
    '/posts/{id}/delete' => [
        'methods' => ['POST'],
        'controller' => 'PostController@delete'
    ],
    '/new-post' => 'PostController@create'
];