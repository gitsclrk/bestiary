<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['user_password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['user_username'];

        // Optional: store designation/rank
        $_SESSION['user_desig'] = $user['user_desig'];

        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: index.php?error=Invalid credentials");
        exit;
    }
}

?>