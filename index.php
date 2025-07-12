<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Camp Valhalla – Login</title>
    <link rel="stylesheet" href="style.css" />
    <!-- Font Awesome for eye icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="auth-body">
<div class="auth-frame fade-in">
    <h1 class="header-title">CAMP VALHALLA ACCESS</h1>
    <p class="subtitle">Please log in to continue.</p>

    <?php if ($error): ?>
        <p style="color: #ff6b6b;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php" class="creature-form">
        <input type="text" name="username" placeholder="Username" required />

        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Enter your password" required />
            <i class="fa-solid fa-eye toggle-eye" id="toggleEye" onclick="togglePassword()"></i>
        </div>

        <button type="submit">Login</button>
    </form>

    <p style="margin-top: 1rem;">
        Don't have an account?
        <a href="signup.php" class="logout-button">Sign up</a>
    </p>
</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const eye = document.getElementById('toggleEye');

        if (password.type === 'password') {
            password.type = 'text';
            eye.classList.remove('fa-eye');
            eye.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            eye.classList.remove('fa-eye-slash');
            eye.classList.add('fa-eye');
        }
    }
</script>
</body>
</html>
