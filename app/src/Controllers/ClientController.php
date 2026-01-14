<?php

namespace App\Controllers;

use App\Models\Game;
use App\Models\Purchase;
use App\Models\Review;
use App\Middleware\Auth;
use App\Middleware\CSRF;

/**
 * Client/User Dashboard Controller
 */
class ClientController
{
    private Game $gameModel;
    private Purchase $purchaseModel;
    private Review $reviewModel;

    public function __construct()
    {
        $this->gameModel = new Game();
        $this->purchaseModel = new Purchase();
        $this->reviewModel = new Review();
    }

    /**
     * Home page (browse games)
     */
    public function home(): void
    {
        $games = $this->gameModel->getAll();
        $genres = $this->gameModel->getAllGenres();
        
        
        // Add average ratings
        foreach ($games as $key => $game) {
            $games[$key]['average_rating'] = $this->reviewModel->getAverageRating($game['id']);
        }
        
        // Unset any lingering references
        unset($game);

        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/client/home.php';
    }

    /**
     * User dashboard (my games)
     */
    public function dashboard(): void
    {
        Auth::requireClient();

        $userId = Auth::userId();
        $purchases = $this->purchaseModel->getByUserId($userId);
        
        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/client/dashboard.php';
    }

    /**
     * Game details page
     */
    public function gameDetails(array $vars): void
    {
        $id = (int) $vars['id'];
        $game = $this->gameModel->findById($id);

        if (!$game) {
            header('HTTP/1.0 404 Not Found');
            echo 'Game not found';
            exit;
        }

        // Get reviews
        $reviews = $this->reviewModel->getByGameId($id);
        $averageRating = $this->reviewModel->getAverageRating($id);

        // Check if user owns the game
        $userOwnsGame = false;
        $hasReviewed = false;
        
        if (Auth::check()) {
            $userId = Auth::userId();
            $userOwnsGame = $this->purchaseModel->userOwnsGame($userId, $id);
            $hasReviewed = $this->reviewModel->hasUserReviewed($userId, $id);
        }

        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/client/game-details.php';
    }

    /**
     * Payment page
     */
    public function payment(array $vars): void
    {
        Auth::require();

        $transactionId = $vars['transactionId'];
        $purchase = $this->purchaseModel->findByTransactionId($transactionId);

        if (!$purchase) {
            header('HTTP/1.0 404 Not Found');
            echo 'Transaction not found';
            exit;
        }

        // Verify user owns this transaction
        if ($purchase['user_id'] !== Auth::userId()) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Access denied';
            exit;
        }

        $game = $this->gameModel->findById($purchase['game_id']);

        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/client/payment.php';
    }

    /**
     * Process payment
     */
    public function processPayment(array $vars): void
    {
        Auth::require();
        CSRF::require();

        $transactionId = $vars['transactionId'];
        $purchase = $this->purchaseModel->findByTransactionId($transactionId);

        if (!$purchase) {
            header('Location: /');
            exit;
        }

        // Verify user owns this transaction
        if ($purchase['user_id'] !== Auth::userId()) {
            header('Location: /');
            exit;
        }

        // Simulate payment processing
        // In production, integrate with actual payment gateway (Stripe, PayPal, etc.)
        $this->purchaseModel->updateStatus($purchase['id'], 'completed');

        header('Location: /payment-success?transaction=' . $transactionId);
        exit;
    }

    /**
     * Payment success page
     */
    public function paymentSuccess(): void
    {
        Auth::require();

        $transactionId = $_GET['transaction'] ?? null;
        $purchase = null;

        if ($transactionId) {
            $purchase = $this->purchaseModel->findByTransactionId($transactionId);
        }

        $csrfToken = CSRF::generateToken();
        $user = Auth::user();

        require __DIR__ . '/../../views/client/payment-success.php';
    }
}
