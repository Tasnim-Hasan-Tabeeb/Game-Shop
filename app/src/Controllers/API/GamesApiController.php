<?php

namespace App\Controllers\API;

use App\Models\Game;
use App\Models\Review;
use App\Middleware\Sanitizer;

/**
 * Games API Controller
 */
class GamesApiController extends BaseApiController
{
    private Game $gameModel;
    private Review $reviewModel;

    public function __construct()
    {
        $this->gameModel = new Game();
        $this->reviewModel = new Review();
    }

    /**
     * GET /api/games - Get all games
     */
    public function index(): void
    {
        $genre = Sanitizer::get('genre');
        $search = Sanitizer::get('search');

        if ($search) {
            $games = $this->gameModel->search($search);
        } elseif ($genre) {
            $games = $this->gameModel->getByGenre($genre);
        } else {
            $games = $this->gameModel->getAll();
        }

        // Add average ratings to games
        foreach ($games as &$game) {
            $game['average_rating'] = $this->reviewModel->getAverageRating($game['id']);
        }

        $this->success($games, 'Games retrieved successfully');
    }

    /**
     * GET /api/games/{id} - Get single game
     */
    public function show(array $vars): void
    {
        $id = (int) $vars['id'];
        $game = $this->gameModel->findById($id);

        if (!$game) {
            $this->error('Game not found', 404);
            return;
        }

        // Add reviews and average rating
        $game['reviews'] = $this->reviewModel->getByGameId($id);
        $game['average_rating'] = $this->reviewModel->getAverageRating($id);

        $this->success($game, 'Game retrieved successfully');
    }

    /**
     * POST /api/games - Create game (admin only)
     */
    public function create(): void
    {
        $this->requireAdmin();

        $data = $this->getJsonBody();

        // Validate required fields
        if (empty($data['title']) || empty($data['price'])) {
            $this->error('Title and price are required', 400);
            return;
        }

        // Sanitize data
        $gameData = [
            'title' => Sanitizer::string($data['title']),
            'description' => Sanitizer::string($data['description'] ?? ''),
            'price' => Sanitizer::float($data['price']),
            'image_url' => Sanitizer::url($data['image_url'] ?? ''),
            'download_url' => Sanitizer::url($data['download_url'] ?? ''),
            'genre' => Sanitizer::string($data['genre'] ?? ''),
            'publisher' => Sanitizer::string($data['publisher'] ?? ''),
            'release_date' => $data['release_date'] ?? null,
            'is_active' => $data['is_active'] ?? true
        ];

        $gameId = $this->gameModel->create($gameData);

        if ($gameId) {
            $game = $this->gameModel->findById($gameId);
            $this->success($game, 'Game created successfully', 201);
        } else {
            $this->error('Failed to create game', 500);
        }
    }

    /**
     * PUT /api/games/{id} - Update game (admin only)
     */
    public function update(array $vars): void
    {
        $this->requireAdmin();

        $id = (int) $vars['id'];
        $data = $this->getJsonBody();

        $game = $this->gameModel->findById($id);
        if (!$game) {
            $this->error('Game not found', 404);
            return;
        }

        // Sanitize data
        $updateData = [];
        if (isset($data['title'])) $updateData['title'] = Sanitizer::string($data['title']);
        if (isset($data['description'])) $updateData['description'] = Sanitizer::string($data['description']);
        if (isset($data['price'])) $updateData['price'] = Sanitizer::float($data['price']);
        if (isset($data['image_url'])) $updateData['image_url'] = Sanitizer::url($data['image_url']);
        if (isset($data['download_url'])) $updateData['download_url'] = Sanitizer::url($data['download_url']);
        if (isset($data['genre'])) $updateData['genre'] = Sanitizer::string($data['genre']);
        if (isset($data['publisher'])) $updateData['publisher'] = Sanitizer::string($data['publisher']);
        if (isset($data['release_date'])) $updateData['release_date'] = $data['release_date'];
        if (isset($data['is_active'])) $updateData['is_active'] = (bool) $data['is_active'];

        $success = $this->gameModel->update($id, $updateData);

        if ($success) {
            $game = $this->gameModel->findById($id);
            $this->success($game, 'Game updated successfully');
        } else {
            $this->error('Failed to update game', 500);
        }
    }

    /**
     * DELETE /api/games/{id} - Delete game (admin only)
     */
    public function delete(array $vars): void
    {
        $this->requireAdmin();

        $id = (int) $vars['id'];
        $game = $this->gameModel->findById($id);

        if (!$game) {
            $this->error('Game not found', 404);
            return;
        }

        $success = $this->gameModel->delete($id);

        if ($success) {
            $this->success(null, 'Game deleted successfully');
        } else {
            $this->error('Failed to delete game', 500);
        }
    }

    /**
     * GET /api/games/genres - Get all genres
     */
    public function genres(): void
    {
        $genres = $this->gameModel->getAllGenres();
        $this->success($genres, 'Genres retrieved successfully');
    }
}
