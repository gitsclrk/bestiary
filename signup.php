<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fName = trim($_POST['first_name']);
    $lName = trim($_POST['last_name']);
    $desig = trim($_POST['designation']);
    $username = trim($_POST['username']);
    $rawPassword = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($rawPassword !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $password = password_hash($rawPassword, PASSWORD_DEFAULT);
        $check = $pdo->prepare("SELECT user_id FROM users WHERE user_username = ?");
        $check->execute([$username]);

        if ($check->fetch()) {
            $error = "Username already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (user_fName, user_lName, user_username, user_password, user_desig) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$fName, $lName, $username, $password, $desig]);
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Request Access – Camp Valhalla</title>
    <link rel="stylesheet" href="/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="auth-body">
<div class="auth-frame fade-in">
    <h1 class="header-title">CAMP VALHALLA ACCESS</h1>
    <p class="subtitle">Submit your information to create an account.</p>

    <?php if (!empty($error)): ?>
        <p style="color: #ff6b6b;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="signup.php" class="creature-form" onsubmit="return validatePasswords()">
        <input type="text" name="first_name" placeholder="First Name" required />
        <input type="text" name="last_name" placeholder="Last Name" required />
        <input type="text" name="designation" placeholder="Designation" required />
        <input type="text" name="username" placeholder="Username" required />

        <div class="password-wrapper">
            <input type="password" name="password" id="password" placeholder="Password" required oninput="checkStrength(this.value)" />
            <i class="fa-solid fa-eye toggle-eye" onclick="toggleVisibility('password', this)"></i>
        </div>

        <div class="strength-meter" id="strengthMeter"></div>

        <div class="password-wrapper">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required />
            <i class="fa-solid fa-eye toggle-eye" onclick="toggleVisibility('confirm_password', this)"></i>
        </div>

        <button type="submit">Register</button>
    </form>

    <p style="margin-top: 1rem;">Already have an account?
        <a href="index.php" class="logout-button">Return to Login</a>
    </p>
</div>

<script>
    function toggleVisibility(fieldId, icon) {
        const field = document.getElementById(fieldId);
        const type = field.type === "password" ? "text" : "password";
        field.type = type;
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
    }

    function checkStrength(password) {
        const meter = document.getElementById('strengthMeter');
        let strength = 0;

        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;

        const levels = ['Weak', 'Medium', 'Strong', 'Very Strong'];
        const colors = ['#e57373', '#ffb74d', '#81c784', '#64ffda'];

        meter.textContent = password ? levels[strength - 1] || 'Too Short' : '';
        meter.style.color = password ? colors[strength - 1] || '#aaa' : '';
    }

    function validatePasswords() {
        const p1 = document.getElementById('password').value;
        const p2 = document.getElementById('confirm_password').value;
        if (p1 !== p2) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    }
</script>
</body>
</html>
