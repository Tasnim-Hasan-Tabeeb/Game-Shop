<?php

namespace App\Middleware;

/**
 * Authentication middleware
 */
class Auth
{
    /**
     * Start session if not already started
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin(): bool
    {
        self::startSession();
        return self::check() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Check if user is client
     */
    public static function isClient(): bool
    {
        self::startSession();
        return self::check() && isset($_SESSION['role']) && $_SESSION['role'] === 'client';
    }

    /**
     * Get current user ID
     */
    public static function userId(): ?int
    {
        self::startSession();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user data
     */
    public static function user(): ?array
    {
        self::startSession();
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ];
    }

    /**
     * Login user
     */
    public static function login(array $user): void
    {
        self::startSession();
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
    }

    /**
     * Logout user
     */
    public static function logout(): void
    {
        self::startSession();
        
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy the session
        session_destroy();
    }

    /**
     * Require authentication (redirect if not authenticated)
     */
    public static function require(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Require admin role (redirect if not admin)
     */
    public static function requireAdmin(): void
    {
        self::require();
        
        if (!self::isAdmin()) {
            header('Location: /');
            exit;
        }
    }

    /**
     * Require client role (redirect if not client)
     */
    public static function requireClient(): void
    {
        self::require();
        
        if (!self::isClient()) {
            header('Location: /admin/dashboard');
            exit;
        }
    }
}
