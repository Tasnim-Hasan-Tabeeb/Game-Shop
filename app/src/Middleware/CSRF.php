<?php

namespace App\Middleware;

/**
 * CSRF Protection middleware
 */
class CSRF
{
    /**
     * Generate CSRF token
     */
    public static function generateToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Get CSRF token
     */
    public static function getToken(): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['csrf_token'] ?? null;
    }

    /**
     * Verify CSRF token
     */
    public static function verify(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $sessionToken = self::getToken();
        
        if (!$sessionToken || !$token) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Verify CSRF token from request
     */
    public static function verifyRequest(): bool
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return self::verify($token);
    }

    /**
     * Generate HTML hidden input field for CSRF token
     */
    public static function field(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Require valid CSRF token (throw exception if invalid)
     */
    public static function require(): void
    {
        if (!self::verifyRequest()) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
    }
}
