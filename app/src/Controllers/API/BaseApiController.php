<?php

namespace App\Controllers\API;

use App\Middleware\Auth;

/**
 * Base API Controller
 */
class BaseApiController
{
    /**
     * Send JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Send success response
     */
    protected function success($data = null, string $message = 'Success', int $statusCode = 200): void
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        $this->json($response, $statusCode);
    }

    /**
     * Send error response
     */
    protected function error(string $message, int $statusCode = 400, $errors = null): void
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        $this->json($response, $statusCode);
    }

    /**
     * Get JSON body from request
     */
    protected function getJsonBody(): ?array
    {
        $body = file_get_contents('php://input');
        return json_decode($body, true);
    }

    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->error('Authentication required', 401);
        }
    }

    /**
     * Require admin role
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        
        if (!Auth::isAdmin()) {
            $this->error('Admin access required', 403);
        }
    }

    /**
     * Get current user ID
     */
    protected function getUserId(): int
    {
        $userId = Auth::userId();
        
        if (!$userId) {
            $this->error('Authentication required', 401);
        }

        return $userId;
    }
}
