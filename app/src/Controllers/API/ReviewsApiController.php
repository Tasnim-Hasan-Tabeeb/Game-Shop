<?php

namespace App\Controllers\API;

use App\Models\Review;
use App\Models\Purchase;
use App\Middleware\Sanitizer;

/**
 * Reviews API Controller
 */
class ReviewsApiController extends BaseApiController
{
    private Review $reviewModel;
    private Purchase $purchaseModel;

    public function __construct()
    {
        $this->reviewModel = new Review();
        $this->purchaseModel = new Purchase();
    }

    /**
     * GET /api/reviews?game_id={id} - Get reviews for a game
     */
    public function index(): void
    {
        $gameId = Sanitizer::get('game_id');

        if (!$gameId) {
            $this->error('Game ID is required', 400);
            return;
        }

        $reviews = $this->reviewModel->getByGameId((int) $gameId);
        $averageRating = $this->reviewModel->getAverageRating((int) $gameId);

        $this->success([
            'reviews' => $reviews,
            'average_rating' => $averageRating,
            'total_reviews' => count($reviews)
        ], 'Reviews retrieved successfully');
    }

    /**
     * POST /api/reviews - Create review
     */
    public function create(): void
    {
        $this->requireAuth();
        $userId = $this->getUserId();

        $data = $this->getJsonBody();

        // Validate required fields
        if (empty($data['game_id']) || empty($data['rating'])) {
            $this->error('Game ID and rating are required', 400);
            return;
        }

        $gameId = Sanitizer::int($data['game_id']);
        $rating = Sanitizer::int($data['rating']);
        $comment = Sanitizer::string($data['comment'] ?? '');

        // Validate rating range
        if ($rating < 1 || $rating > 5) {
            $this->error('Rating must be between 1 and 5', 400);
            return;
        }

        // Check if user owns the game
        if (!$this->purchaseModel->userOwnsGame($userId, $gameId)) {
            $this->error('You must own the game to review it', 403);
            return;
        }

        // Check if user has already reviewed
        if ($this->reviewModel->hasUserReviewed($userId, $gameId)) {
            $this->error('You have already reviewed this game', 400);
            return;
        }

        // Create review
        $reviewId = $this->reviewModel->create([
            'user_id' => $userId,
            'game_id' => $gameId,
            'rating' => $rating,
            'comment' => $comment
        ]);

        if ($reviewId) {
            $reviews = $this->reviewModel->getByGameId($gameId);
            $review = null;
            foreach ($reviews as $r) {
                if ($r['id'] == $reviewId) {
                    $review = $r;
                    break;
                }
            }

            $this->success($review, 'Review created successfully', 201);
        } else {
            $this->error('Failed to create review', 500);
        }
    }

    /**
     * PUT /api/reviews/{id} - Update review
     */
    public function update(array $vars): void
    {
        $this->requireAuth();
        $userId = $this->getUserId();

        $id = (int) $vars['id'];
        $data = $this->getJsonBody();

        // Get reviews by user to verify ownership
        $userReviews = $this->reviewModel->getByUserId($userId);
        $reviewExists = false;
        
        foreach ($userReviews as $review) {
            if ($review['id'] == $id) {
                $reviewExists = true;
                break;
            }
        }

        if (!$reviewExists) {
            $this->error('Review not found or access denied', 404);
            return;
        }

        $updateData = [];
        
        if (isset($data['rating'])) {
            $rating = Sanitizer::int($data['rating']);
            if ($rating < 1 || $rating > 5) {
                $this->error('Rating must be between 1 and 5', 400);
                return;
            }
            $updateData['rating'] = $rating;
        }

        if (isset($data['comment'])) {
            $updateData['comment'] = Sanitizer::string($data['comment']);
        }

        $success = $this->reviewModel->update($id, $updateData);

        if ($success) {
            $this->success(null, 'Review updated successfully');
        } else {
            $this->error('Failed to update review', 500);
        }
    }

    /**
     * DELETE /api/reviews/{id} - Delete review
     */
    public function delete(array $vars): void
    {
        $this->requireAuth();
        $userId = $this->getUserId();

        $id = (int) $vars['id'];

        // Get reviews by user to verify ownership
        $userReviews = $this->reviewModel->getByUserId($userId);
        $reviewExists = false;
        
        foreach ($userReviews as $review) {
            if ($review['id'] == $id) {
                $reviewExists = true;
                break;
            }
        }

        if (!$reviewExists) {
            $this->error('Review not found or access denied', 404);
            return;
        }

        $success = $this->reviewModel->delete($id);

        if ($success) {
            $this->success(null, 'Review deleted successfully');
        } else {
            $this->error('Failed to delete review', 500);
        }
    }

    /**
     * GET /api/reviews/user - Get current user's reviews
     */
    public function userReviews(): void
    {
        $this->requireAuth();
        $userId = $this->getUserId();

        $reviews = $this->reviewModel->getByUserId($userId);
        $this->success($reviews, 'User reviews retrieved successfully');
    }
}
