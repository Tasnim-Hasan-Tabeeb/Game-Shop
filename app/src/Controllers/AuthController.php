<?php

namespace App\Controllers;

use App\Models\User;
use App\Middleware\Auth;
use App\Middleware\CSRF;
use App\Middleware\Sanitizer;

/**
 * Authentication controller
 */
class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Show login form
     */
    public function showLogin(): void
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $this->redirectBasedOnRole();
            return;
        }

        $csrfToken = CSRF::generateToken();
        require __DIR__ . '/../../views/auth/login.php';
    }

    /**
     * Handle login
     */
    public function login(): void
    {
        // Verify CSRF token
        CSRF::require();

        $email = Sanitizer::post('email');
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($email) || empty($password)) {
            $this->jsonResponse(['success' => false, 'message' => 'Email and password are required'], 400);
            return;
        }

        if (!Sanitizer::isValidEmail($email)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        // Find user
        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid credentials'], 401);
            return;
        }

        // Login user
        Auth::login($user);

        // Determine redirect URL based on role
        $redirectUrl = $user['role'] === 'admin' ? '/admin/dashboard' : '/dashboard';

        $this->jsonResponse([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => $redirectUrl
        ]);
    }

    /**
     * Show registration form
     */
    public function showRegister(): void
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $this->redirectBasedOnRole();
            return;
        }

        $csrfToken = CSRF::generateToken();
        require __DIR__ . '/../../views/auth/register.php';
    }

    /**
     * Handle registration
     */
    public function register(): void
    {
        // Verify CSRF token
        CSRF::require();

        $username = Sanitizer::post('username');
        $email = Sanitizer::post('email');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate input
        if (empty($username) || empty($email) || empty($password)) {
            $this->jsonResponse(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (!Sanitizer::isValidEmail($email)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        if (strlen($password) < 6) {
            $this->jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
            return;
        }

        if ($password !== $confirmPassword) {
            $this->jsonResponse(['success' => false, 'message' => 'Passwords do not match'], 400);
            return;
        }

        // Check if user already exists
        if ($this->userModel->findByEmail($email)) {
            $this->jsonResponse(['success' => false, 'message' => 'Email already registered'], 400);
            return;
        }

        if ($this->userModel->findByUsername($username)) {
            $this->jsonResponse(['success' => false, 'message' => 'Username already taken'], 400);
            return;
        }

        // Create user
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'client'
        ]);

        if ($userId) {
            // Auto-login after registration
            $user = $this->userModel->findById($userId);
            Auth::login($user);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Registration successful',
                'redirect' => '/dashboard'
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Registration failed'], 500);
        }
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword(): void
    {
        $csrfToken = CSRF::generateToken();
        require __DIR__ . '/../../views/auth/forgot-password.php';
    }

    /**
     * Handle forgot password
     */
    public function forgotPassword(): void
    {
        // CSRF verification
        CSRF::require();

        $email = Sanitizer::post('email');

        if (empty($email) || !Sanitizer::isValidEmail($email)) {
            $this->jsonResponse(['success' => false, 'message' => 'Valid email is required'], 400);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        // Always return success to prevent email enumeration
        $this->jsonResponse([
            'success' => true,
            'message' => 'If the email exists, a password reset link has been sent'
        ]);

        // TODO: Implement actual password reset email sending
    }

    /**
     * Handle password reset (direct method without email)
     */
    public function resetPassword(): void
    {
        // CSRF verification
        CSRF::require();

        $email = Sanitizer::post('email');
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate input
        if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
            $this->jsonResponse(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (!Sanitizer::isValidEmail($email)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        if (strlen($newPassword) < 6) {
            $this->jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->jsonResponse(['success' => false, 'message' => 'Passwords do not match'], 400);
            return;
        }

        // Find user
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            // For security, don't reveal if email exists or not
            $this->jsonResponse(['success' => false, 'message' => 'Invalid email address'], 400);
            return;
        }

        // Update password
        $success = $this->userModel->update($user['id'], [
            'password' => $newPassword
        ]);

        if ($success) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Password reset successfully! Redirecting to login...'
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to reset password'], 500);
        }
    }

    /**
     * Redirect based on user role
     */
    private function redirectBasedOnRole(): void
    {
        if (Auth::isAdmin()) {
            header('Location: /admin/dashboard');
        } else {
            header('Location: /dashboard');
        }
        exit;
    }

    /**
     * Send JSON response
     */
    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
