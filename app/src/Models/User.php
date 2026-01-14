<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * User model for managing user data
 */
class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new user
     */
    public function create(array $data): ?int
    {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':role' => $data['role'] ?? 'client'
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Find user by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, username, email, role, created_at, updated_at FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Verify user password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Update user
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            if (in_array($key, ['username', 'email', 'password'])) {
                if ($key === 'password') {
                    $value = password_hash($value, PASSWORD_BCRYPT);
                }
                $fields[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }

    /**
     * Delete user
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get all users with optional role filter
     */
    public function getAll(?string $role = null): array
    {
        if ($role) {
            $sql = "SELECT id, username, email, role, created_at FROM users WHERE role = :role ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':role' => $role]);
        } else {
            $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }
}
