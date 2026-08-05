<?php
require_once 'config.php';

// If already logged in, go straight to profile
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$errors = [];
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);

$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Please enter both username/email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];

            header('Location: profile.php');
            exit;
        } else {
            $errors[] = 'Invalid username/email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 320px; }
    h2 { text-align: center; margin-top: 0; }
    label { display: block; margin-top: 12px; font-size: 14px; color: #333; }
    input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
    button { width: 100%; margin-top: 20px; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; }
    button:hover { background: #0069d9; }
    .errors { background: #ffe0e0; color: #a00; padding: 10px; border-radius: 4px; margin-top: 15px; font-size: 13px; }
    .success { background: #e0ffe4; color: #157347; padding: 10px; border-radius: 4px; margin-top: 15px; font-size: 13px; }
    .errors ul { margin: 0; padding-left: 18px; }
    .link { text-align: center; margin-top: 15px; font-size: 14px; }
</style>
</head>
<body>
<div class="card">
    <h2>Login</h2>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="username">Username or Email</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <div class="link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</div>
</body>
</html>
