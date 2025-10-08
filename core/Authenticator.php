<?php

namespace Core;

class Authenticator
{
    private static function getConfig(string $key, $default = null)
    {
        $envKey = 'AUTH_' . strtoupper($key);
        return $_ENV[$envKey] ?? $default;
    }

    public static function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', '1');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_samesite', 'Strict');

            session_start();

            if (!isset($_SESSION['last_regeneration'])) {
                self::regenerateSession();
            } elseif (time() - $_SESSION['last_regeneration'] > self::getConfig('SESSION_REGENERATE', 1800)) {
                self::regenerateSession();
            }
        }
    }

    private static function regenerateSession(): void
    {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }

    public static function generateToken(array $data, string $purpose = 'auth'): string
    {
        $secret = self::getConfig('SECRET_KEY', 'fallback-secret-key-change-in-production');
        $algorithm = self::getConfig('TOKEN_ALGORITHM', 'sha256');
        $timestamp = time();
        $random = bin2hex(random_bytes(32));

        $dataString = json_encode($data);
        $payload = implode('|', [
            $dataString,
            $purpose,
            $timestamp,
            $random,
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);

        $signature = hash_hmac($algorithm, $payload, $secret);

        return base64_encode(implode('|', [
            $dataString,
            $purpose,
            $timestamp,
            $random,
            $signature
        ]));
    }

    public static function validateToken(string $token, string $purpose = 'auth'): ?array
    {
        $secret = self::getConfig('SECRET_KEY', 'my-secret-key');
        $algorithm = self::getConfig('TOKEN_ALGORITHM', 'sha256');

        $parts = explode('|', base64_decode($token));
        if (count($parts) !== 5) {
            return null;
        }

        [$dataString, $tokenPurpose, $timestamp, $random, $signature] = $parts;

        $tokenExpiry = self::getConfig('TOKEN_EXPIRY', 86400);
        if (time() - $timestamp > $tokenExpiry) {
            return null;
        }

        if ($tokenPurpose !== $purpose) {
            return null;
        }

        $expectedPayload = implode('|', [
            $dataString,
            $purpose,
            $timestamp,
            $random,
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ]);

        $expectedSignature = hash_hmac($algorithm, $expectedPayload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        return json_decode($dataString, true);
    }

    public static function setupSession(array $credentials, bool $remember = false): void
    {
        self::startSecureSession();

        $_SESSION['auth_credentials'] = $credentials;
        $_SESSION['auth_login_time'] = time();
        $_SESSION['auth_session_id'] = bin2hex(random_bytes(16));

        if ($remember) {
            $rememberToken = self::generateToken($credentials, 'remember');
            $rememberExpiry = self::getConfig('REMEMBER_EXPIRY', 2592000);
            setcookie('remember_token', $rememberToken, [
                'expires' => time() + $rememberExpiry,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
    }

    public static function check(): ?array
    {
        self::startSecureSession();

        if (isset($_SESSION['auth_credentials'])) {
            $credentials = self::validateSession();
            if ($credentials) {
                return $credentials;
            }
        }

        if (isset($_COOKIE['remember_token'])) {
            $credentials = self::validateRememberToken($_COOKIE['remember_token']);
            if ($credentials) {
                self::setupSession($credentials, true);
                return $credentials;
            }
        }

        return null;
    }

    private static function validateSession(): ?array
    {
        if (!isset($_SESSION['auth_credentials'], $_SESSION['auth_login_time'])) {
            return null;
        }

        $sessionExpiry = self::getConfig('SESSION_EXPIRY', 1800);
        if (time() - $_SESSION['auth_login_time'] > $sessionExpiry) {
            self::destroySession();
            return null;
        }

        return $_SESSION['auth_credentials'];
    }

    private static function validateRememberToken(string $token): ?array
    {
        return self::validateToken($token, 'remember');
    }

    public static function destroySession(): void
    {
        self::startSecureSession();

        $_SESSION = [];

        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        session_destroy();
    }

    public static function requireAuth(string $redirectUrl = '/auth'): array
    {
        $credentials = self::check();

        if (!$credentials) {
            header('Location: ' . $redirectUrl . '?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }

        return $credentials;
    }

    public static function csrfToken(): string
    {
        self::startSecureSession();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function validateCsrf(string $token): bool
    {
        self::startSecureSession();

        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}