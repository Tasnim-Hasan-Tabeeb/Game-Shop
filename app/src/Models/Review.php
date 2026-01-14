<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Review model for managing game reviews
 */
class Review
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new review
     */
    public function create(array $data): ?int
    {
        $sql = "INSERT INTO reviews (user_id, game_id, rating, comment) 
                VALUES (:user_id, :game_id, :rating, :comment)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':game_id' => $data['game_id'],
            ':rating' => $data['rating'],
            ':comment' => $data['comment'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Get reviews by game ID
     */
    public function getByGameId(int $gameId): array
    {
        $sql = "SELECT r.*, u.username 
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.game_id = :game_id
                ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':game_id' => $gameId]);

        return $stmt->fetchAll();
    }

    /**
     * Get reviews by user ID
     */
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT r.*, g.title as game_title 
                FROM reviews r
                JOIN games g ON r.game_id = g.id
                WHERE r.user_id = :user_id
                ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll();
    }

    /**
     * Check if user has reviewed a game
     */
    public function hasUserReviewed(int $userId, int $gameId): bool
    {
        $sql = "SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND game_id = :game_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':game_id' => $gameId
        ]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get average rating for a game
     */
    public function getAverageRating(int $gameId): float
    {
        $sql = "SELECT AVG(rating) as avg_rating FROM reviews WHERE game_id = :game_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':game_id' => $gameId]);

        $result = $stmt->fetch();
        return $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
    }

    /**
     * Update review
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['rating'])) {
            $fields[] = "rating = :rating";
            $params[':rating'] = $data['rating'];
        }

        if (isset($data['comment'])) {
            $fields[] = "comment = :comment";
            $params[':comment'] = $data['comment'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE reviews SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }

    /**
     * Delete review
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM reviews WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get all reviews (admin)
     */
    public function getAll(): array
    {
        $sql = "SELECT r.*, u.username, g.title as game_title 
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                JOIN games g ON r.game_id = g.id
                ORDER BY r.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
