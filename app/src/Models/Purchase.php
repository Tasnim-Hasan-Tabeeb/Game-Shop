<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Purchase model for managing game purchases
 */
class Purchase
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new purchase
     */
    public function create(array $data): ?int
    {
        $sql = "INSERT INTO purchases (user_id, game_id, amount, payment_status, transaction_id) 
                VALUES (:user_id, :game_id, :amount, :payment_status, :transaction_id)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':game_id' => $data['game_id'],
            ':amount' => $data['amount'],
            ':payment_status' => $data['payment_status'] ?? 'pending',
            ':transaction_id' => $data['transaction_id'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Find purchase by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT p.*, g.title as game_title, g.image_url, g.download_url, u.username, u.email 
                FROM purchases p
                JOIN games g ON p.game_id = g.id
                JOIN users u ON p.user_id = u.id
                WHERE p.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $purchase = $stmt->fetch();
        return $purchase ?: null;
    }

    /**
     * Get purchases by user ID
     */
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT p.*, g.title as game_title, g.image_url, g.download_url, g.description 
                FROM purchases p
                JOIN games g ON p.game_id = g.id
                WHERE p.user_id = :user_id AND p.payment_status = 'completed'
                ORDER BY p.purchase_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * Check if user owns a game
     */
    public function userOwnsGame(int $userId, int $gameId): bool
    {
        $sql = "SELECT COUNT(*) FROM purchases 
                WHERE user_id = :user_id AND game_id = :game_id AND payment_status = 'completed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':game_id' => $gameId
        ]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Update purchase status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE purchases SET payment_status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status
        ]);
    }

    /**
     * Get all purchases (admin)
     */
    public function getAll(?string $status = null): array
    {
        $sql = "SELECT p.*, g.title as game_title, u.username, u.email 
                FROM purchases p
                JOIN games g ON p.game_id = g.id
                JOIN users u ON p.user_id = u.id";
        
        $params = [];
        
        if ($status) {
            $sql .= " WHERE p.payment_status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY p.purchase_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Get purchase statistics
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_purchases,
                    SUM(amount) as total_revenue,
                    COUNT(DISTINCT user_id) as unique_customers
                FROM purchases 
                WHERE payment_status = 'completed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Find purchase by transaction ID
     */
    public function findByTransactionId(string $transactionId): ?array
    {
        $sql = "SELECT * FROM purchases WHERE transaction_id = :transaction_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':transaction_id' => $transactionId]);
        
        $purchase = $stmt->fetch();
        return $purchase ?: null;
    }
}
