<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\Purchase;
use App\Models\User;
use App\Middleware\Auth;
use App\Middleware\CSRF;

/**
 * Admin Dashboard Controller
 */
class AdminController
{
    private Game $gameModel;
    private Purchase $purchaseModel;
    private User $userModel;

    public function __construct()
    {
        $this->gameModel = new Game();
        $this->purchaseModel = new Purchase();
        $this->userModel = new User();
    }

    /**
     * Admin dashboard
     */
    public function dashboard(): void
    {
        Auth::requireAdmin();

        $stats = $this->purchaseModel->getStatistics();
        $recentPurchases = $this->purchaseModel->getAll();
        $recentPurchases = array_slice($recentPurchases, 0, 10);

        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/admin/dashboard.php';
    }

    /**
     * Manage games
     */
    public function games(): void
    {
        Auth::requireAdmin();

        $games = $this->gameModel->getAll(false); // Get all games including inactive
        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/admin/games.php';
    }

    /**
     * Add/Edit game form
     */
    public function gameForm(?array $vars = null): void
    {
        Auth::requireAdmin();

        $game = null;
        $isEdit = false;

        if ($vars && isset($vars['id'])) {
            $game = $this->gameModel->findById((int) $vars['id']);
            $isEdit = true;

            if (!$game) {
                header('Location: /admin/games');
                exit;
            }
        }

        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/admin/game-form.php';
    }

    /**
     * Save game (create or update)
     */
    public function saveGame(?array $vars = null): void
    {
        Auth::requireAdmin();
        CSRF::require();

        $gameId = isset($vars['id']) ? (int) $vars['id'] : null;
        $isEdit = $gameId !== null;

        // Handle file upload
        $imageUrl = $_POST['existing_image_url'] ?? '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->handleImageUpload($_FILES['image']);
            
            if ($imageUrl === false) {
                $_SESSION['error'] = 'Failed to upload image';
                header('Location: ' . ($isEdit ? "/admin/games/edit/$gameId" : '/admin/games/new'));
                exit;
            }
        }

        $gameData = [
            'title' => \App\Middleware\Sanitizer::post('title'),
            'description' => \App\Middleware\Sanitizer::post('description'),
            'price' => \App\Middleware\Sanitizer::float($_POST['price']),
            'image_url' => $imageUrl,
            'download_url' => \App\Middleware\Sanitizer::url($_POST['download_url'] ?? ''),
            'genre' => \App\Middleware\Sanitizer::post('genre'),
            'publisher' => \App\Middleware\Sanitizer::post('publisher'),
            'release_date' => $_POST['release_date'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if ($isEdit) {
            $success = $this->gameModel->update($gameId, $gameData);
            $_SESSION['success'] = $success ? 'Game updated successfully!' : 'Failed to update game';
        } else {
            $newId = $this->gameModel->create($gameData);
            $_SESSION['success'] = $newId ? 'Game created successfully!' : 'Failed to create game';
        }

        header('Location: /admin/games');
        exit;
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload(array $file): string|false
    {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }

        // Create directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../public/images/games/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('game_') . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/images/games/' . $filename;
        }

        return false;
    }

    /**
     * Manage purchases
     */
    public function purchases(): void
    {
        Auth::requireAdmin();

        $purchases = $this->purchaseModel->getAll();
        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/admin/purchases.php';
    }

    /**
     * Manage users
     */
    public function users(): void
    {
        Auth::requireAdmin();

        $users = $this->userModel->getAll();
        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/admin/users.php';
    }

    /**
     * Show user edit form
     */
    public function userForm(?array $vars = null): void
    {
        Auth::requireAdmin();

        $userItem = null;
        $isEdit = false;

        if ($vars && isset($vars['id'])) {
            $userItem = $this->userModel->findById((int) $vars['id']);
            $isEdit = true;

            if (!$userItem) {
                header('Location: /admin/users');
                exit;
            }
        }

        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/admin/user-form.php';
    }

    /**
     * Save user changes
     */
    public function saveUser(?array $vars = null): void
    {
        Auth::requireAdmin();
        CSRF::require();

        $userId = isset($vars['id']) ? (int) $vars['id'] : null;
        if (!$userId) {
            http_response_code(400);
            $_SESSION['error'] = 'Invalid user ID';
            header('Location: /admin/users');
            exit;
        }

        $data = [];
        $username = \App\Middleware\Sanitizer::post('username');
        $email = \App\Middleware\Sanitizer::email($_POST['email'] ?? null);
        $role = $_POST['role'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($username !== null && $username !== '') {
            $data['username'] = $username;
        }
        if ($email !== null && $email !== '') {
            $data['email'] = $email;
        }
        if ($role !== null && in_array($role, ['admin', 'client'])) {
            $data['role'] = $role;
        }
        if ($password !== null && $password !== '') {
            $data['password'] = $password;
        }

        $success = $this->userModel->update($userId, $data);
        $_SESSION['success'] = $success ? 'User updated successfully!' : 'Failed to update user';

        header('Location: /admin/users');
        exit;
    }

    public function deleteUser(?array $vars = null): void
    {
        Auth::requireAdmin();
        CSRF::require();

        if (!$vars || !isset($vars['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            exit;
        }

        $userId = (int) $vars['id'];
        $success = $this->userModel->delete($userId);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
    }
}
