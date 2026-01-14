# Video Game Shop - Quick Setup Guide

## Step-by-Step Installation

### 1. Start Docker Environment

```bash
# Navigate to project directory
cd web_development_1_boilerplate-main

# Start all containers
docker-compose up -d

# Verify containers are running
docker-compose ps
```

You should see:
- `nginx` - Running on port 80
- `php` - PHP-FPM service
- `mysql` - Running on port 3306
- `phpmyadmin` - Running on port 8080

### 2. Install Composer Dependencies

```bash
# Enter PHP container
docker-compose exec php bash

# Inside container, navigate to app directory
cd /app

# Install dependencies
composer install

# Exit container
exit
```

### 3. Setup Database

**Option A: Using phpMyAdmin (Recommended for beginners)**

1. Open browser and go to `http://localhost:8080`
2. Login credentials:
   - Server: `mysql`
   - Username: `developer`
   - Password: `secret123`
3. Select `developmentdb` from left sidebar
4. Click "Import" tab
5. Click "Choose File" and select `app/database/schema.sql`
6. Click "Go" at the bottom

**Option B: Using Command Line**

```bash
docker-compose exec mysql mysql -u developer -psecret123 developmentdb < app/database/schema.sql
```

### 4. Verify Installation

1. **Check Main Site**: Open `http://localhost` in your browser
   - You should see the home page with game listings

2. **Test Login**: Go to `http://localhost/login`
   - Email: `admin@gameshop.com`
   - Password: `admin123`

3. **Check Admin Dashboard**: After login, go to `http://localhost/admin/dashboard`
   - You should see statistics and admin panel

### 5. Test User Registration

1. Go to `http://localhost/register`
2. Create a new client account:
   - Username: `testuser`
   - Email: `test@example.com`
   - Password: `test123`
3. After registration, you'll be automatically logged in
4. Browse games and test the purchase flow

## Common Issues & Solutions

### Issue 1: "Database connection failed"

**Solution:**
```bash
# Restart MySQL container
docker-compose restart mysql

# Wait 10 seconds for MySQL to fully start
sleep 10

# Try accessing the site again
```

### Issue 2: "404 Not Found" for all pages

**Solution:**
```bash
# Restart Nginx
docker-compose restart nginx

# Check Nginx logs
docker-compose logs nginx
```

### Issue 3: Composer dependencies not installed

**Solution:**
```bash
# Remove vendor directory and reinstall
docker-compose exec php rm -rf /app/vendor
docker-compose exec php composer install -d /app
```

### Issue 4: Permission errors in PHP

**Solution:**
```bash
# Fix permissions
docker-compose exec php chown -R www-data:www-data /app
docker-compose exec php chmod -R 755 /app
```

## Application Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| Main Application | http://localhost | - |
| phpMyAdmin | http://localhost:8080 | developer / secret123 |
| Admin Dashboard | http://localhost/admin/dashboard | admin@gameshop.com / admin123 |

## Testing the Application

### Test Client Flow

1. **Register Account**
   - Go to `/register`
   - Create account with email/password

2. **Browse Games**
   - View home page at `/`
   - Use search and filters

3. **Purchase Game**
   - Click "View" on any game
   - Click "Buy Now"
   - Fill demo payment form
   - Complete purchase

4. **Access My Games**
   - Go to `/dashboard`
   - View purchased games
   - Click "Download Game" (if download URL is set)

5. **Leave Review**
   - Go to game details page
   - Fill review form
   - Submit rating and comment

### Test Admin Flow

1. **Login as Admin**
   - Email: `admin@gameshop.com`
   - Password: `admin123`

2. **View Dashboard**
   - See statistics (revenue, purchases, customers)
   - View recent purchases

3. **Add New Game**
   - Go to `/admin/games`
   - Click "Add New Game"
   - Fill game details:
     - Title: "Test Game"
     - Price: 19.99
     - Description: "A test game"
     - Genre: "Action"
   - Submit form

4. **Edit Game**
   - Click edit button on any game
   - Modify details
   - Save changes

5. **View Purchases & Users**
   - Check `/admin/purchases` for all transactions
   - Check `/admin/users` for user list

## API Testing

### Using cURL

**Login:**
```bash
curl -X POST http://localhost/api/login \
  -F "email=admin@gameshop.com" \
  -F "password=admin123" \
  -F "csrf_token=YOUR_TOKEN"
```

**Get All Games:**
```bash
curl http://localhost/api/games
```

**Get Game Details:**
```bash
curl http://localhost/api/games/1
```

**Create Review (requires authentication):**
```bash
curl -X POST http://localhost/api/reviews \
  -H "Content-Type: application/json" \
  -d '{"game_id": 1, "rating": 5, "comment": "Great game!"}'
```

## Development Tips

### Enable Error Reporting

Edit `app/public/index.php` and add at the top:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### View Logs

```bash
# PHP logs
docker-compose logs php

# Nginx logs
docker-compose logs nginx

# MySQL logs
docker-compose logs mysql

# Follow logs in real-time
docker-compose logs -f
```

### Database Backup

```bash
# Backup database
docker-compose exec mysql mysqldump -u developer -psecret123 developmentdb > backup.sql

# Restore database
docker-compose exec -T mysql mysql -u developer -psecret123 developmentdb < backup.sql
```

### Reset Everything

```bash
# Stop and remove all containers
docker-compose down

# Remove volumes (WARNING: This deletes all data!)
docker-compose down -v

# Start fresh
docker-compose up -d

# Reinstall dependencies
docker-compose exec php composer install -d /app

# Re-import database
docker-compose exec -T mysql mysql -u developer -psecret123 developmentdb < app/database/schema.sql
```

## Next Steps

1. **Customize Design**: Edit views in `app/views/`
2. **Add More Games**: Use admin panel or insert via SQL
3. **Configure Email**: Set up SMTP in `EmailService.php`
4. **Add Payment Gateway**: Integrate Stripe or PayPal in payment controller
5. **Deploy to Production**: Follow deployment best practices

## Support

If you encounter issues:
1. Check Docker containers are running: `docker-compose ps`
2. View logs: `docker-compose logs`
3. Verify database connection in phpMyAdmin
4. Check file permissions
5. Ensure ports 80, 3306, 8080 are not in use by other applications

---

**Happy Coding! 🎮**
