<?php
require_once 'config.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch fresh user data from the database
$stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// If the user no longer exists, destroy the session and send to login
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    .card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 360px; }
    h2 { text-align: center; margin-top: 0; }
    .avatar { width: 70px; height: 70px; border-radius: 50%; background: #007bff; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 15px; }
    table { width: 100%; margin-top: 10px; border-collapse: collapse; }
    td { padding: 8px 4px; font-size: 14px; border-bottom: 1px solid #eee; }
    td.label { color: #777; width: 40%; }
    td.value { font-weight: bold; color: #222; }
    .logout { display: block; text-align: center; margin-top: 20px; padding: 10px; background: #dc3545; color: #fff; border-radius: 4px; text-decoration: none; }
    .logout:hover { background: #c82333; }
</style>
</head>
<body>
<div class="card">
    <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
    <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>

    <table>
        <tr>
            <td class="label">User ID</td>
            <td class="value"><?= htmlspecialchars($user['id']) ?></td>
        </tr>
        <tr>
            <td class="label">Username</td>
            <td class="value"><?= htmlspecialchars($user['username']) ?></td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="value"><?= htmlspecialchars($user['email']) ?></td>
        </tr>
        <tr>
            <td class="label">Joined</td>
            <td class="value"><?= htmlspecialchars(date('F j, Y', strtotime($user['created_at']))) ?></td>
        </tr>
    </table>

    <a href="logout.php" class="logout">Logout</a>
</div>
</body>
</html>
