<?php

namespace Source\Controllers;

use Core\Helpers\AuthHelper;
use Core\Helpers\ResponseHelper;
use Core\Helpers\ORMHelper;
use Core\View;
use Source\Models\User;

class AuthController
{
    public function showAuth()
    {
        if (AuthHelper::check()) {
            header('Location: /');
            exit;
        }

        View::render('auth', [
            'title' => 'Authentication',
            'csrf_token' => AuthHelper::csrfToken()
        ]);
    }

    public function login()
    {
        if (!AuthHelper::validateCsrf($_POST['csrf_token'] ?? '')) {
            ResponseHelper::error('Invalid CSRF token', 419);
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        $user = ORMHelper::select(User::class)
            ->where('email', $email)
            ->fetchOne();

        if (!$user || !password_verify($password, $user->password)) {
            ResponseHelper::error('Invalid credentials', 401);
        }

        $credentials = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role
        ];

        AuthHelper::setup($credentials, $remember);

        ResponseHelper::success([
            'message' => 'Login successful',
            'redirect' => $_POST['redirect'] ?? '/'
        ]);
    }

    public function register()
    {
        if (!AuthHelper::validateCsrf($_POST['csrf_token'] ?? '')) {
            ResponseHelper::error('Invalid CSRF token', 419);
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        $existingUser = ORMHelper::select(User::class)
            ->where('email', $email)
            ->fetchOne();

        if ($existingUser) {
            ResponseHelper::error('User already exists', 409);
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->role = 'user';

        $manager = ORMHelper::getManager();
        $manager->persist($user);
        $manager->run();

        $credentials = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role
        ];

        AuthHelper::setup($credentials, $remember);

        ResponseHelper::success([
            'message' => 'Registration successful',
            'redirect' => '/'
        ], 201);
    }

    public function logout()
    {
        AuthHelper::logout();
        header('Location: /auth');
        exit;
    }
}