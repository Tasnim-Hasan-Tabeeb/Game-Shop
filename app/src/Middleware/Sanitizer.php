<?php

namespace App\Middleware;

/**
 * Input sanitization and validation middleware
 */
class Sanitizer
{
    /**
     * Sanitize string input
     */
    public static function string(?string $input): string
    {
        if ($input === null) {
            return '';
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email
     */
    public static function email(?string $email): string
    {
        if ($email === null) {
            return '';
        }
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize integer
     */
    public static function int($input): int
    {
        return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize float
     */
    public static function float($input): float
    {
        return (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Sanitize URL
     */
    public static function url(?string $url): string
    {
        if ($url === null) {
            return '';
        }
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    /**
     * Validate email format
     */
    public static function isValidEmail(?string $email): bool
    {
        if ($email === null) {
            return false;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL format
     */
    public static function isValidUrl(?string $url): bool
    {
        if ($url === null) {
            return false;
        }
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Sanitize array of inputs
     */
    public static function array(array $input): array
    {
        $sanitized = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::array($value);
            } else {
                $sanitized[$key] = self::string($value);
            }
        }
        return $sanitized;
    }

    /**
     * Get sanitized POST data
     */
    public static function post(string $key, $default = null)
    {
        if (!isset($_POST[$key])) {
            return $default;
        }

        $value = $_POST[$key];
        
        if (is_array($value)) {
            return self::array($value);
        }
        
        return self::string($value);
    }

    /**
     * Get sanitized GET data
     */
    public static function get(string $key, $default = null)
    {
        if (!isset($_GET[$key])) {
            return $default;
        }

        $value = $_GET[$key];
        
        if (is_array($value)) {
            return self::array($value);
        }
        
        return self::string($value);
    }
}
