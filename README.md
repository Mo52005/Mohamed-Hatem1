# PHP Login / Register / Profile System

## Files
- `config.php` – Database connection + session start (edit DB credentials here)
- `schema.sql` – Run this in MySQL to create the database and `users` table
- `register.php` – Registration page
- `login.php` – Login page
- `profile.php` – Protected page showing the logged-in user's info
- `logout.php` – Destroys the session and logs the user out

## Setup
1. Create the database:
   ```bash
   mysql -u root -p < schema.sql
   ```
2. Open `config.php` and set `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` to match your MySQL setup.
3. Place the folder in your web server root (e.g. `htdocs`, `www`, or run with PHP's built-in server):
   ```bash
   php -S localhost:8000
   ```
4. Visit `http://localhost:8000/register.php` to create an account, then log in at `login.php`.

## How it works
- Passwords are hashed with `password_hash()` (bcrypt) and verified with `password_verify()`.
- Sessions track the logged-in user (`$_SESSION['user_id']`); `session_regenerate_id()` runs on login to prevent session fixation.
- `profile.php` re-checks the session on every load and re-fetches the user's data from the database, so it always shows current info.
- All output is escaped with `htmlspecialchars()` to prevent XSS, and all queries use prepared statements to prevent SQL injection.
