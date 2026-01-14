<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Database connection class using PDO
 */
class Database
{
    private static ?PDO $connection = null;

    /**
     * Get database connection instance
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $host = getenv('DB_HOST') ?: 'mysql';
                $port = getenv('DB_PORT') ?: '3306';
                $dbname = getenv('DB_DATABASE') ?: 'developmentdb';
                $username = getenv('DB_USERNAME') ?: 'developer';
                $password = getenv('DB_PASSWORD') ?: 'secret123';

                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    /**
     * Close database connection
     */
    public static function closeConnection(): void
    {
        self::$connection = null;
    }
}
