<?php
session_start(); // ✅ Needed for flash messages
require '../db.php';

if (!isset($_GET['id'])) {
    header('Location: ../dashboard.php');
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM creatures WHERE crtr_id = ?");
$stmt->execute([$id]);
$creature = $stmt->fetch();

if (!$creature) {
    echo "Creature not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['crtr_name'];
    $size = $_POST['crtr_size'];
    $progeny = $_POST['crtr_progeny'];
    $type = $_POST['crtr_type'];
    $environment = $_POST['crtr_environment'];
    $description = $_POST['crtr_description'];

    $stmt = $pdo->prepare("UPDATE creatures SET
        crtr_name = ?, crtr_size = ?, crtr_progeny = ?, crtr_type = ?, crtr_environment = ?, crtr_description = ?
        WHERE crtr_id = ?");
    $stmt->execute([$name, $size, $progeny, $type, $environment, $description, $id]);

    // ✅ Flash message and redirect
    $_SESSION['flash'] = "Creature updated successfully.";
    header("Location: ../dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Creature</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="terminal-body">
<div class="terminal-frame fade-in">
    <h1 class="header-title">Edit Creature</h1>
    <form method="POST" class="creature-form">
        <input type="text" name="crtr_name" value="<?= htmlspecialchars($creature['crtr_name']) ?>" required />
        <input type="text" name="crtr_size" value="<?= htmlspecialchars($creature['crtr_size']) ?>" required />
        <input type="text" name="crtr_progeny" value="<?= htmlspecialchars($creature['crtr_progeny']) ?>" />
        <input type="text" name="crtr_type" value="<?= htmlspecialchars($creature['crtr_type']) ?>" required />
        <input type="text" name="crtr_environment" value="<?= htmlspecialchars($creature['crtr_environment']) ?>" required />
        <textarea name="crtr_description" rows="4"><?= htmlspecialchars($creature['crtr_description']) ?></textarea>
        <button type="submit">Save Changes</button>
    </form>
    <div style="text-align: center; margin-top: 1rem;">
        <a href="../dashboard.php" class="logout-button">← Cancel</a>
    </div>
</div>
</body>
</html>
