<?php

namespace Source\Controllers;

use Fluxor\Controller;
use Fluxor\Response;
use App\Core\Auth;
use App\Core\ORMHelper;
use Source\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Response::redirect('/');
        }

        return Response::view('auth/login', [
            'title' => 'Login',
            'csrf_token' => Auth::csrfToken(),
            'redirect' => $this->request->input('redirect', '/')
        ]);
    }

    public function login()
    {
        if (!Auth::validateCsrf($this->request->input('csrf_token'))) {
            return Response::error('Invalid CSRF token', 419);
        }

        $email = $this->request->input('email');
        $password = $this->request->input('password');
        $remember = (bool) $this->request->input('remember');
        $redirect = $this->request->input('redirect', '/');

        $user = ORMHelper::findOneBy(User::class, 'email', $email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            return Response::error('Invalid credentials', 401);
        }

        $credentials = [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'role' => $user->getRole()
        ];

        Auth::login($credentials, $remember);

        return Response::success(['redirect' => $redirect], 'Login successful');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return Response::redirect('/');
        }

        return Response::view('auth/register', [
            'title' => 'Register',
            'csrf_token' => Auth::csrfToken()
        ]);
    }

    public function register()
    {
        if (!Auth::validateCsrf($this->request->input('csrf_token'))) {
            return Response::error('Invalid CSRF token', 419);
        }

        $name = $this->request->input('name');
        $email = $this->request->input('email');
        $password = $this->request->input('password');
        $remember = (bool) $this->request->input('remember');

        $existingUser = ORMHelper::findOneBy(User::class, 'email', $email);

        if ($existingUser) {
            return Response::error('User already exists', 409);
        }

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

        $manager = ORMHelper::getManager();
        $manager->persist($user);
        $manager->run();

        $credentials = [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'role' => $user->getRole()
        ];

        Auth::login($credentials, $remember);

        return Response::success(['redirect' => '/'], 'Registration successful', 201);
    }

    public function logout()
    {
        Auth::logout();
        return Response::redirect('/');
    }
}