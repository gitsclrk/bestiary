<?php
session_start();
require 'db.php';
echo "<p style='color:lime;'>Using DB: " . realpath(__DIR__ . '/bestiary.sqlite') . "</p>";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$username = $_SESSION['username'] ?? 'User';
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT user_desig FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$rank = $user['user_desig'] ?? 'Unassigned';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = uniqid('CRTR_');
    $name = $_POST['crtr_name'];
    $size = $_POST['crtr_size'];
    $progeny = $_POST['crtr_progeny'];
    $type = $_POST['crtr_type'];
    $environment = $_POST['crtr_environment'];
    $description = $_POST['crtr_description'] ?? null;
    $imageFileName = null;

    do {
        $id = 'CRTR_' . bin2hex(random_bytes(8));
        $check = $pdo->prepare("SELECT 1 FROM creatures WHERE crtr_id = ?");
        $check->execute([$id]);
    } while ($check->fetch());

    if (isset($_FILES['crtr_image']) && $_FILES['crtr_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = basename($_FILES['crtr_image']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $imageFileName = uniqid('img_') . '.' . $extension;

        $targetPath = $uploadDir . $imageFileName;
        move_uploaded_file($_FILES['crtr_image']['tmp_name'], $targetPath);
    }

    $stmt = $pdo->prepare("INSERT INTO creatures 
        (crtr_id, crtr_name, crtr_size, crtr_progeny, crtr_type, crtr_environment, crtr_description, crtr_image, logged_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id, $name, $size, $progeny, $type, $environment, $description, $imageFileName, $username]);

    $_SESSION['flash'] = "New creature entry logged.";
    header("Location: dashboard.php");
    exit;
}
?>

