<?php

return [
    '/api/users' => [
        'methods' => ['GET'],
        'controller' => 'Api\\UserController@index'
    ],
    '/api/users/{id}' => [
        'methods' => ['GET'],
        'controller' => 'Api\\UserController@show'
    ],
    '/api/posts' => [
        'methods' => ['GET'],
        'controller' => 'Api\\PostController@index'
    ],
    '/api/posts/{id}' => [
        'methods' => ['GET'],
        'controller' => 'Api\\PostController@show'
    ],
    '/api/auth/login' => [
        'methods' => ['POST'],
        'controller' => 'Api\\AuthController@login'
    ]
];