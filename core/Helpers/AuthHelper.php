<?php

namespace Core\Helpers;

use Core\Authenticator;
use Source\Models\User;

class AuthHelper
{
    /**
     * Validate user credentials against database
     * Modify this method according to your user model structure
     */
    private static function validateCredentials(array $credentials): bool
    {
        if (empty($credentials) || !isset($credentials['user_id'])) {
            return false;
        }

        // Example validation - adjust according to your user model
        // This checks if the user still exists in the database
        $user = ORMHelper::select(User::class)
            ->where('id', $credentials['user_id'])
            ->fetchOne();

        return $user !== null;
    }

    public static function check(): ?array
    {
        $credentials = Authenticator::check();

        if (!$credentials) {
            return null;
        }

        if (!self::validateCredentials($credentials)) {
            Authenticator::destroySession();
            return null;
        }

        return $credentials;
    }

    public static function setup(array $credentials, bool $remember = false): void
    {
        Authenticator::setupSession($credentials, $remember);
    }

    public static function logout(): void
    {
        Authenticator::destroySession();
    }

    public static function csrfToken(): string
    {
        return Authenticator::csrfToken();
    }

    public static function validateCsrf(string $token): bool
    {
        return Authenticator::validateCsrf($token);
    }
}