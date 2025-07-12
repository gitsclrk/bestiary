<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// You can lock this down with an 'is_admin' field later
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Ops | Camp Valhalla</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="terminal-body">
<div class="terminal-frame fade-in">
    <h2>🧾 Operative Audit Log</h2>
    <table class="bestiary-table">
        <tr>
            <th>Callsign</th><th>Rank</th><th>Last Login</th><th>Enlisted</th>
        </tr>
        <?php while ($user = $stmt->fetch()): ?>
            <tr>
                <td><?= htmlspecialchars($user['user_username']) ?></td>
                <td><?= htmlspecialchars($user['user_desig']) ?></td>
                <td><?= $user['user_last_login'] ?? '—' ?></td>
                <td><?= $user['created_at'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <a href="../dashboard.php" class="logout-button">← Return to Dashboard</a>
</div>
</body>
</html>
