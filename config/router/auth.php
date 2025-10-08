<?php

return [
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
    ]
];