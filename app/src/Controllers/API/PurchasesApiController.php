<?php

namespace App\Controllers\API;

use App\Models\Purchase;
use App\Models\Game;
use App\Middleware\Sanitizer;

/**
 * Purchases API Controller
 */
class PurchasesApiController extends BaseApiController
{
    private Purchase $purchaseModel;
    private Game $gameModel;

    public function __construct()
    {
        $this->purchaseModel = new Purchase();
        $this->gameModel = new Game();
    }

    /**
     * GET /api/purchases - Get user's purchases
     */
    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->getUserId();

        $purchases = $this->purchaseModel->getByUserId($userId);
        $this->success($purchases, 'Purchases retrieved successfully');
    }

    /**
     * GET /api/purchases/{id} - Get single purchase
     */
    public function show(array $vars): void
    {
        $this->requireAuth();
        
        $id = (int) $vars['id'];
        $purchase = $this->purchaseModel->findById($id);

        if (!$purchase) {
            $this->error('Purchase not found', 404);
            return;
        }

        // Verify user owns this purchase
        if ($purchase['user_id'] !== $this->getUserId()) {
            $this->error('Access denied', 403);
            return;
        }

        $this->success($purchase, 'Purchase retrieved successfully');
    }

    /**
     * POST /api/purchases - Create purchase (initiate payment)
     */
    public function create(): void
    {
        $this->requireAuth();
        $userId = $this->getUserId();

        $data = $this->getJsonBody();

        // Validate required fields
        if (empty($data['game_id'])) {
            $this->error('Game ID is required', 400);
            return;
        }

        $gameId = Sanitizer::int($data['game_id']);

        // Check if game exists
        $game = $this->gameModel->findById($gameId);
        if (!$game) {
            $this->error('Game not found', 404);
            return;
        }

        // Check if user already owns the game
        if ($this->purchaseModel->userOwnsGame($userId, $gameId)) {
            $this->error('You already own this game', 400);
            return;
        }

        // Create purchase record
        $transactionId = 'TXN_' . uniqid() . '_' . time();
        
        $purchaseId = $this->purchaseModel->create([
            'user_id' => $userId,
            'game_id' => $gameId,
            'amount' => $game['price'],
            'payment_status' => 'pending',
            'transaction_id' => $transactionId
        ]);

        if ($purchaseId) {
            $purchase = $this->purchaseModel->findById($purchaseId);
            
            // Return purchase info with payment details
            $this->success([
                'purchase' => $purchase,
                'payment_url' => '/payment/' . $transactionId,
                'transaction_id' => $transactionId
            ], 'Purchase initiated successfully', 201);
        } else {
            $this->error('Failed to create purchase', 500);
        }
    }

    /**
     * POST /api/purchases/{id}/complete - Complete purchase (webhook/callback)
     */
    public function complete(array $vars): void
    {
        $id = (int) $vars['id'];
        $purchase = $this->purchaseModel->findById($id);

        if (!$purchase) {
            $this->error('Purchase not found', 404);
            return;
        }

        // Update payment status
        $success = $this->purchaseModel->updateStatus($id, 'completed');

        if ($success) {
            $this->success(null, 'Purchase completed successfully');
        } else {
            $this->error('Failed to complete purchase', 500);
        }
    }

    /**
     * GET /api/purchases/check/{gameId} - Check if user owns a game
     */
    public function checkOwnership(array $vars): void
    {
        $this->requireAuth();
        $userId = $this->getUserId();
        $gameId = (int) $vars['gameId'];

        $owns = $this->purchaseModel->userOwnsGame($userId, $gameId);

        $this->success(['owns' => $owns], 'Ownership status retrieved');
    }

    /**
     * GET /api/admin/purchases - Get all purchases (admin only)
     */
    public function adminIndex(): void
    {
        $this->requireAdmin();

        $status = Sanitizer::get('status');
        $purchases = $this->purchaseModel->getAll($status);

        $this->success($purchases, 'All purchases retrieved successfully');
    }

    /**
     * GET /api/admin/statistics - Get purchase statistics (admin only)
     */
    public function statistics(): void
    {
        $this->requireAdmin();

        $stats = $this->purchaseModel->getStatistics();
        $this->success($stats, 'Statistics retrieved successfully');
    }
}
