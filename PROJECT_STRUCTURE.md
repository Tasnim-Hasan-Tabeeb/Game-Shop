# PROJECT_STRUCTURE

## Root
- `.git/`
- `.gitignore`
- `docker-compose.yml`
- `nginx.conf`
- `PHP.Dockerfile`
- `README.md`
- `SETUP_GUIDE.md`
- `app/`

## app/
- `composer.json`
- `composer.lock`
- `database/`
- `public/`
- `src/`
- `vendor/`
- `views/`

## app/database/
- `schema.sql`

## app/public/
- `index.php` (single entry point + all route definitions via FastRoute)
- `hello.php`
- `contact.php`
- `images/`
- `images/games/` (uploaded game images)

## app/src/
- `Config/`
- `Controllers/`
- `Controllers/API/`
- `Middleware/`
- `Models/`

## app/src/Config/
- `Database.php` (PDO MySQL connection singleton)

## app/src/Controllers/
- `AdminController.php`
- `AuthController.php`
- `ClientController.php`
- `HelloController.php`
- `HomeController.php`

## app/src/Controllers/API/
- `BaseApiController.php`
- `GamesApiController.php`
- `PurchasesApiController.php`
- `ReviewsApiController.php`

## app/src/Middleware/
- `Auth.php`
- `CSRF.php`
- `Sanitizer.php`

## app/src/Models/
- `Game.php`
- `Purchase.php`
- `Review.php`
- `User.php`

## app/views/
- `admin/`
- `auth/`
- `client/`
- `layout/`

## app/views/admin/
- `dashboard.php`
- `games.php`
- `game-form.php`
- `purchases.php`
- `users.php`
- `user-form.php`

## app/views/auth/
- `login.php`
- `register.php`
- `forgot-password.php`

## app/views/client/
- `home.php`
- `dashboard.php`
- `game-details.php`
- `payment.php`
- `payment-success.php`

## app/views/layout/
- `header.php`
- `footer.php`

## app/vendor/
Composer-managed dependencies (not hand-edited):
- `nikic/fast-route`
- `autoload.php`
- `composer/*`

## Architectural conventions
- Entry point: `app/public/index.php`.
- Routing: centralized in `app/public/index.php` using FastRoute.
- Handler format: `[ControllerClass, method]`.
- Controllers:
  - Web page controllers in `app/src/Controllers`.
  - JSON/API controllers in `app/src/Controllers/API`.
- Models: database access and domain operations in `app/src/Models`.
- Middleware:
  - auth/authorization checks (`Auth`),
  - CSRF generation/validation (`CSRF`),
  - input sanitation (`Sanitizer`).
- Views:
  - PHP templates under `app/views` grouped by domain (`admin`, `auth`, `client`).
  - shared layout fragments under `app/views/layout`.
- Database:
  - schema SQL in `app/database/schema.sql`.
  - connection abstraction in `app/src/Config/Database.php`.
- Static assets:
  - served from `app/public`.
  - uploaded game images in `app/public/images/games`.

## How to mirror this structure in another project
1. Keep one public entrypoint (`public/index.php`) with all route registration.
2. Keep PSR-4 app namespace mapped to `src/` in `composer.json`.
3. Split controllers into page controllers and `Controllers/API` for JSON endpoints.
4. Keep middleware and models in separate `src/Middleware` and `src/Models` folders.
5. Keep view templates under `views/{admin,auth,client,layout}`.
6. Keep SQL schema under `database/schema.sql`.
7. Keep Docker + nginx at project root and web root at `app/public`.
