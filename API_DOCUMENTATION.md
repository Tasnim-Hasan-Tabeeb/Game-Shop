# Video Game Shop - API Documentation

## Base URL
```
http://localhost
```

## Authentication

Most endpoints require user authentication. The application uses session-based authentication.

### Login Required Endpoints
- All `/api/purchases/*` (except creating purchase)
- All `/api/reviews/*` (except getting reviews)
- All `/api/admin/*`

### Admin Required Endpoints
- `POST /api/games`
- `PUT /api/games/{id}`
- `DELETE /api/games/{id}`
- All `/api/admin/*`

---

## Authentication Endpoints

### Login
```http
POST /api/login
```

**Request Body (form-data):**
```
email: string (required)
password: string (required)
csrf_token: string (required)
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "redirect": "/dashboard"
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

---

### Register
```http
POST /api/register
```

**Request Body (form-data):**
```
username: string (required)
email: string (required)
password: string (required, min 6 chars)
confirm_password: string (required)
csrf_token: string (required)
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "redirect": "/dashboard"
}
```

---

### Forgot Password
```http
POST /api/forgot-password
```

**Request Body (form-data):**
```
email: string (required)
csrf_token: string (required)
```

**Response:**
```json
{
  "success": true,
  "message": "If the email exists, a password reset link has been sent"
}
```

---

## Games API

### Get All Games
```http
GET /api/games
```

**Query Parameters:**
- `genre` (optional) - Filter by genre
- `search` (optional) - Search by title

**Response:**
```json
{
  "success": true,
  "message": "Games retrieved successfully",
  "data": [
    {
      "id": 1,
      "title": "The Legend Quest",
      "description": "An epic adventure...",
      "price": "59.99",
      "image_url": "/images/games/legend-quest.jpg",
      "download_url": null,
      "genre": "RPG",
      "publisher": "Epic Games Studio",
      "release_date": "2024-01-15",
      "is_active": 1,
      "average_rating": 4.5
    }
  ]
}
```

---

### Get Game by ID
```http
GET /api/games/{id}
```

**Response:**
```json
{
  "success": true,
  "message": "Game retrieved successfully",
  "data": {
    "id": 1,
    "title": "The Legend Quest",
    "description": "An epic adventure...",
    "price": "59.99",
    "genre": "RPG",
    "reviews": [
      {
        "id": 1,
        "user_id": 2,
        "username": "john_doe",
        "rating": 5,
        "comment": "Amazing game!",
        "created_at": "2024-01-20 10:30:00"
      }
    ],
    "average_rating": 4.5
  }
}
```

---

### Create Game (Admin Only)
```http
POST /api/games
```

**Request Body (JSON):**
```json
{
  "title": "New Game",
  "description": "Game description",
  "price": 49.99,
  "image_url": "https://example.com/image.jpg",
  "download_url": "https://example.com/download",
  "genre": "Action",
  "publisher": "Game Studio",
  "release_date": "2024-06-01",
  "is_active": true
}
```

**Response:**
```json
{
  "success": true,
  "message": "Game created successfully",
  "data": {
    "id": 6,
    "title": "New Game",
    ...
  }
}
```

---

### Update Game (Admin Only)
```http
PUT /api/games/{id}
```

**Request Body (JSON):**
```json
{
  "title": "Updated Title",
  "price": 39.99,
  "is_active": false
}
```

**Response:**
```json
{
  "success": true,
  "message": "Game updated successfully",
  "data": { ... }
}
```

---

### Delete Game (Admin Only)
```http
DELETE /api/games/{id}
```

**Response:**
```json
{
  "success": true,
  "message": "Game deleted successfully"
}
```

---

### Get All Genres
```http
GET /api/games/genres
```

**Response:**
```json
{
  "success": true,
  "message": "Genres retrieved successfully",
  "data": ["RPG", "Action", "Racing", "Puzzle", "FPS"]
}
```

---

## Purchases API

### Get User's Purchases
```http
GET /api/purchases
```

**Authentication:** Required

**Response:**
```json
{
  "success": true,
  "message": "Purchases retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "game_id": 1,
      "game_title": "The Legend Quest",
      "image_url": "/images/games/legend-quest.jpg",
      "download_url": "https://example.com/download",
      "amount": "59.99",
      "payment_status": "completed",
      "purchase_date": "2024-01-20 15:30:00"
    }
  ]
}
```

---

### Get Purchase by ID
```http
GET /api/purchases/{id}
```

**Authentication:** Required (must own the purchase)

**Response:**
```json
{
  "success": true,
  "message": "Purchase retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 2,
    "game_id": 1,
    "game_title": "The Legend Quest",
    "username": "john_doe",
    "email": "john@example.com",
    "amount": "59.99",
    "payment_status": "completed",
    "transaction_id": "TXN_abc123",
    "purchase_date": "2024-01-20 15:30:00"
  }
}
```

---

### Create Purchase
```http
POST /api/purchases
```

**Authentication:** Required

**Request Body (JSON):**
```json
{
  "game_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Purchase initiated successfully",
  "data": {
    "purchase": { ... },
    "payment_url": "/payment/TXN_abc123",
    "transaction_id": "TXN_abc123"
  }
}
```

**Error Responses:**
```json
{
  "success": false,
  "message": "You already own this game"
}
```

---

### Check Game Ownership
```http
GET /api/purchases/check/{gameId}
```

**Authentication:** Required

**Response:**
```json
{
  "success": true,
  "message": "Ownership status retrieved",
  "data": {
    "owns": true
  }
}
```

---

### Get All Purchases (Admin)
```http
GET /api/admin/purchases
```

**Authentication:** Admin required

**Query Parameters:**
- `status` (optional) - Filter by payment status

**Response:**
```json
{
  "success": true,
  "message": "All purchases retrieved successfully",
  "data": [ ... ]
}
```

---

### Get Statistics (Admin)
```http
GET /api/admin/statistics
```

**Authentication:** Admin required

**Response:**
```json
{
  "success": true,
  "message": "Statistics retrieved successfully",
  "data": {
    "total_purchases": 150,
    "total_revenue": "7499.50",
    "unique_customers": 45
  }
}
```

---

## Reviews API

### Get Reviews for a Game
```http
GET /api/reviews?game_id={id}
```

**Query Parameters:**
- `game_id` (required) - Game ID

**Response:**
```json
{
  "success": true,
  "message": "Reviews retrieved successfully",
  "data": {
    "reviews": [
      {
        "id": 1,
        "user_id": 2,
        "game_id": 1,
        "username": "john_doe",
        "rating": 5,
        "comment": "Amazing game!",
        "created_at": "2024-01-20 10:30:00"
      }
    ],
    "average_rating": 4.5,
    "total_reviews": 1
  }
}
```

---

### Create Review
```http
POST /api/reviews
```

**Authentication:** Required (must own the game)

**Request Body (JSON):**
```json
{
  "game_id": 1,
  "rating": 5,
  "comment": "Great game!"
}
```

**Validation:**
- `game_id`: required, integer
- `rating`: required, integer (1-5)
- `comment`: optional, string

**Response:**
```json
{
  "success": true,
  "message": "Review created successfully",
  "data": {
    "id": 5,
    "user_id": 2,
    "game_id": 1,
    "username": "john_doe",
    "rating": 5,
    "comment": "Great game!",
    "created_at": "2024-01-21 14:00:00"
  }
}
```

**Error Responses:**
```json
{
  "success": false,
  "message": "You must own the game to review it"
}
```

```json
{
  "success": false,
  "message": "You have already reviewed this game"
}
```

---

### Update Review
```http
PUT /api/reviews/{id}
```

**Authentication:** Required (must own the review)

**Request Body (JSON):**
```json
{
  "rating": 4,
  "comment": "Updated review text"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Review updated successfully"
}
```

---

### Delete Review
```http
DELETE /api/reviews/{id}
```

**Authentication:** Required (must own the review)

**Response:**
```json
{
  "success": true,
  "message": "Review deleted successfully"
}
```

---

### Get Current User's Reviews
```http
GET /api/reviews/user
```

**Authentication:** Required

**Response:**
```json
{
  "success": true,
  "message": "User reviews retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 2,
      "game_id": 1,
      "game_title": "The Legend Quest",
      "rating": 5,
      "comment": "Great game!",
      "created_at": "2024-01-20 10:30:00"
    }
  ]
}
```

---

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Validation error message"
}
```

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Authentication required"
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Admin access required"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Resource not found"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Server error message"
}
```

---

## Rate Limiting

Currently, there is no rate limiting implemented. For production:
- Implement rate limiting middleware
- Set appropriate limits (e.g., 100 requests per minute per IP)
- Return 429 Too Many Requests when exceeded

---

## Security Headers

All API responses should include:
```
Content-Type: application/json
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
```

---

## Testing with Postman

1. Import the API endpoints into Postman
2. Create environment variables:
   - `base_url`: http://localhost
3. For authenticated requests:
   - First call `/api/login` to establish session
   - Use the same session for subsequent requests

---

## Testing with cURL

**Example: Get all games**
```bash
curl http://localhost/api/games
```

**Example: Login**
```bash
curl -X POST http://localhost/api/login \
  -F "email=admin@gameshop.com" \
  -F "password=admin123" \
  -F "csrf_token=YOUR_TOKEN" \
  -c cookies.txt
```

**Example: Create review (with session)**
```bash
curl -X POST http://localhost/api/reviews \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{"game_id": 1, "rating": 5, "comment": "Excellent!"}'
```

---

## Webhooks (Future Implementation)

For payment gateway integration, implement webhooks:

```http
POST /api/webhooks/payment
```

**Headers:**
```
X-Webhook-Signature: signature_from_payment_provider
```

**Payload:**
```json
{
  "event": "payment.completed",
  "transaction_id": "TXN_abc123",
  "status": "completed"
}
```

---

**Note:** This API uses session-based authentication. For mobile apps or SPAs, consider implementing JWT tokens for better scalability.
