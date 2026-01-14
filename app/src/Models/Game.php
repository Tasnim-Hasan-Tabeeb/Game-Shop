<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Game model for managing video game data
 */
class Game
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new game
     */
    public function create(array $data): ?int
    {
        $sql = "INSERT INTO games (title, description, price, image_url, download_url, genre, publisher, release_date, is_active) 
                VALUES (:title, :description, :price, :image_url, :download_url, :genre, :publisher, :release_date, :is_active)";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'],
            ':image_url' => $data['image_url'] ?? null,
            ':download_url' => $data['download_url'] ?? null,
            ':genre' => $data['genre'] ?? null,
            ':publisher' => $data['publisher'] ?? null,
            ':release_date' => $data['release_date'] ?? null,
            ':is_active' => $data['is_active'] ?? true
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Find game by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM games WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        $game = $stmt->fetch();
        return $game ?: null;
    }

    /**
     * Get all games with optional filters
     */
    public function getAll(bool $activeOnly = true, ?string $genre = null): array
    {
        $conditions = [];
        $params = [];

        if ($activeOnly) {
            $conditions[] = "is_active = :is_active";
            $params[':is_active'] = 1;
        }

        if ($genre) {
            $conditions[] = "genre = :genre";
            $params[':genre'] = $genre;
        }

        $sql = "SELECT * FROM games";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Update game
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        $allowedFields = ['title', 'description', 'price', 'image_url', 'download_url', 'genre', 'publisher', 'release_date', 'is_active'];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "{$key} = :{$key}";
                $params[":{$key}"] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE games SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }

    /**
     * Delete game
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM games WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Search games by title
     */
    public function search(string $query): array
    {
        $sql = "SELECT * FROM games WHERE title LIKE :query AND is_active = 1 ORDER BY title";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':query' => "%{$query}%"]);

        return $stmt->fetchAll();
    }

    /**
     * Get games by genre
     */
    public function getByGenre(string $genre): array
    {
        return $this->getAll(true, $genre);
    }

    /**
     * Get all genres
     */
    public function getAllGenres(): array
    {
        $sql = "SELECT DISTINCT genre FROM games WHERE genre IS NOT NULL AND is_active = 1 ORDER BY genre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
