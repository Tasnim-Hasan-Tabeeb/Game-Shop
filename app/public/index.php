<?php

/**
 * Video Game Shop - Main Router
 * This is the central route handler of the application.
 * It uses FastRoute to map URLs to controller methods.
 */

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Start session
session_start();

/**
 * Define the routes for the application.
 */
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Public routes
    $r->addRoute('GET', '/', ['App\Controllers\ClientController', 'home']);
    $r->addRoute('GET', '/game/{id:\d+}', ['App\Controllers\ClientController', 'gameDetails']);
    
    // Authentication routes
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLogin']);
    $r->addRoute('GET', '/register', ['App\Controllers\AuthController', 'showRegister']);
    $r->addRoute('GET', '/forgot-password', ['App\Controllers\AuthController', 'showForgotPassword']);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout']);
    
    // Client/User routes
    $r->addRoute('GET', '/dashboard', ['App\Controllers\ClientController', 'dashboard']);
    $r->addRoute('GET', '/payment/{transactionId}', ['App\Controllers\ClientController', 'payment']);
    $r->addRoute('POST', '/payment/{transactionId}/process', ['App\Controllers\ClientController', 'processPayment']);
    $r->addRoute('GET', '/payment-success', ['App\Controllers\ClientController', 'paymentSuccess']);
    
    // Admin routes
    $r->addRoute('GET', '/admin/dashboard', ['App\Controllers\AdminController', 'dashboard']);
    $r->addRoute('GET', '/admin/games', ['App\Controllers\AdminController', 'games']);
    $r->addRoute('GET', '/admin/games/new', ['App\Controllers\AdminController', 'gameForm']);
    $r->addRoute('GET', '/admin/games/edit/{id:\d+}', ['App\Controllers\AdminController', 'gameForm']);
    $r->addRoute('POST', '/admin/games/save', ['App\Controllers\AdminController', 'saveGame']);
    $r->addRoute('POST', '/admin/games/save/{id:\d+}', ['App\Controllers\AdminController', 'saveGame']);
    $r->addRoute('GET', '/admin/purchases', ['App\Controllers\AdminController', 'purchases']);
    $r->addRoute('GET', '/admin/users', ['App\Controllers\AdminController', 'users']);
    $r->addRoute('GET', '/admin/users/edit/{id:\d+}', ['App\Controllers\AdminController', 'userForm']);
    $r->addRoute('POST', '/admin/users/save/{id:\d+}', ['App\Controllers\AdminController', 'saveUser']);
    $r->addRoute('POST', '/admin/users/delete/{id:\d+}', ['App\Controllers\AdminController', 'deleteUser']);
    
    // API routes - Authentication
    $r->addRoute('POST', '/api/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('POST', '/api/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('POST', '/api/forgot-password', ['App\Controllers\AuthController', 'forgotPassword']);
    $r->addRoute('POST', '/api/reset-password', ['App\Controllers\AuthController', 'resetPassword']);
    
    // API routes - Games
    $r->addRoute('GET', '/api/games', ['App\Controllers\API\GamesApiController', 'index']);
    $r->addRoute('GET', '/api/games/{id:\d+}', ['App\Controllers\API\GamesApiController', 'show']);
    $r->addRoute('POST', '/api/games', ['App\Controllers\API\GamesApiController', 'create']);
    $r->addRoute('PUT', '/api/games/{id:\d+}', ['App\Controllers\API\GamesApiController', 'update']);
    $r->addRoute('DELETE', '/api/games/{id:\d+}', ['App\Controllers\API\GamesApiController', 'delete']);
    $r->addRoute('GET', '/api/games/genres', ['App\Controllers\API\GamesApiController', 'genres']);
    
    // API routes - Purchases
    $r->addRoute('GET', '/api/purchases', ['App\Controllers\API\PurchasesApiController', 'index']);
    $r->addRoute('GET', '/api/purchases/{id:\d+}', ['App\Controllers\API\PurchasesApiController', 'show']);
    $r->addRoute('POST', '/api/purchases', ['App\Controllers\API\PurchasesApiController', 'create']);
    $r->addRoute('POST', '/api/purchases/{id:\d+}/complete', ['App\Controllers\API\PurchasesApiController', 'complete']);
    $r->addRoute('GET', '/api/purchases/check/{gameId:\d+}', ['App\Controllers\API\PurchasesApiController', 'checkOwnership']);
    $r->addRoute('GET', '/api/admin/purchases', ['App\Controllers\API\PurchasesApiController', 'adminIndex']);
    $r->addRoute('GET', '/api/admin/statistics', ['App\Controllers\API\PurchasesApiController', 'statistics']);
    
    // API routes - Reviews
    $r->addRoute('GET', '/api/reviews', ['App\Controllers\API\ReviewsApiController', 'index']);
    $r->addRoute('POST', '/api/reviews', ['App\Controllers\API\ReviewsApiController', 'create']);
    $r->addRoute('PUT', '/api/reviews/{id:\d+}', ['App\Controllers\API\ReviewsApiController', 'update']);
    $r->addRoute('DELETE', '/api/reviews/{id:\d+}', ['App\Controllers\API\ReviewsApiController', 'delete']);
    $r->addRoute('GET', '/api/reviews/user', ['App\Controllers\API\ReviewsApiController', 'userReviews']);
});

/**
 * Get the request method and URI from the server variables and invoke the dispatcher.
 */
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/**
 * Switch on the dispatcher result and call the appropriate controller method if found.
 */
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
    <div class="text-center">
        <h1 class="display-1">404</h1>
        <p class="lead">Page Not Found</p>
        <a href="/" class="btn btn-primary">Go Home</a>
    </div>
</body>
</html>';
        break;
        
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo 'Method Not Allowed';
        break;
        
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        
        // Instantiate controller and call method
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        
        // Call the controller method with route parameters
        if (!empty($vars)) {
            $controller->$method($vars);
        } else {
            $controller->$method();
        }
        break;
}
