1. **Import database schema**
   - Access phpMyAdmin at `http://localhost:8080`
   - Login:
     - Username: `developer`
     - Password: `secret123`
   - Db: Select `developmentdb`
   - Import sql schema location: `app/database/schema.sql`

1. **Access the application**
   - Main site: `http://localhost`
   - phpMyAdmin: `http://localhost:8080`

# Default Admin Account
- **Email**: admin@gameshop.com
- **Password**: password

# Default Client Account
- **Email**: tabeeb788@gmail.com
- **Password**: password
or Create a new account.

## Usage

# For Clients
1. Register a new account or login
2. Browse available games
3. View game details and reviews
4. Purchase games (demo payment)
5. Access purchased games from dashboard
7. Leave reviews for purchased game
8. A coming soon button is used instead of download.

# For Administrators
1. Login with admin credentials
2. Access admin dashboard at `/admin/dashboard`
3. Add new games with details and pricing
4. Edit or delete existing games
5. View all purchases and customer information
6. Monitor sales statistics

## GDPR Compliance ✅
- Privacy Policy link is included in the site footer: `app/views/layout/footer.php` (add your full policy text there).
- User deletion is implemented in `app/src/Models/User.php` (delete method) and related data (purchases, reviews, sessions) are removed via DB cascades: see `app/database/schema.sql` (ON DELETE CASCADE).
- Sessions and session cookies are explicitly destroyed on logout: `app/src/Middleware/Auth.php` (session and cookie cleanup).

## WCAG / Accessibility Efforts ♿️
- Images include descriptive `alt` attributes using the game title: `app/views/client/home.php`, `app/views/client/game-details.php`.
- Form elements have explicit `<label for="...">` bindings to improve keyboard/assistive-device usability: `app/views/client/payment.php`.
- Navigation and interactive elements use semantic roles and attributes (e.g., `role="button"` in `app/views/layout/header.php`).
- Input sanitization and CSRF protection reduce risks with user-submitted data: see `app/src/Middleware/Sanitizer.php` and `app/src/Middleware/CSRF.php`.


