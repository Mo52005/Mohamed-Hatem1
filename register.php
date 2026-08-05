<?php
require_once 'config.php';

// If already logged in, go straight to profile
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$errors = [];
$username = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // ---- Validation ----
    if ($username === '' || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // ---- Check for existing user ----
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email is already registered.';
        }
    }

    // ---- Insert new user ----
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, $hashedPassword]);

        $_SESSION['success'] = 'Registration successful! Please log in.';
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 320px; }
    h2 { text-align: center; margin-top: 0; }
    label { display: block; margin-top: 12px; font-size: 14px; color: #333; }
    input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
    button { width: 100%; margin-top: 20px; padding: 10px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; }
    button:hover { background: #218838; }
    .errors { background: #ffe0e0; color: #a00; padding: 10px; border-radius: 4px; margin-top: 15px; font-size: 13px; }
    .errors ul { margin: 0; padding-left: 18px; }
    .link { text-align: center; margin-top: 15px; font-size: 14px; }
</style>
</head>
<body>
<div class="card">
    <h2>Create Account</h2>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Register</button>
    </form>

    <div class="link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>
</body>
</html>
