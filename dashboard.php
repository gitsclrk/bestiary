<?php
session_start();
require 'db.php';
// echo "<p style='color:lime;'>Using DB: " . realpath(__DIR__ . '/bestiary.sqlite') . "</p>";

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Camp Valhalla</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="terminal-body">
<div class="terminal-frame fade-in">
    <?php if ($flash): ?>
        <div class="flash-message"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <header>
        <h1 class="header-title">Camp Valhalla Ops Dashboard</h1>
        <p class="subtitle">Welcome, <strong><?= htmlspecialchars($username) ?></strong> | Rank: <span class="rank"><?= htmlspecialchars($rank) ?></span></p>
        <a href="logout.php" class="logout-button" style="position:absolute;top:2rem;right:2rem;">Log Out</a>
    </header>

    <section>
        <h2 class="subtitle">Submit New Creature Report</h2>
        <form method="POST" class="creature-form" enctype="multipart/form-data">
            <input type="text" name="crtr_name" placeholder="Name" required />
            <input type="text" name="crtr_size" placeholder="Size" required />
            <input type="text" name="crtr_progeny" placeholder="Progeny / Ancestry (optional)" />
            <input type="text" name="crtr_type" placeholder="Classification (Type)" required />
            <input type="text" name="crtr_environment" placeholder="Native Environment" required />
            <textarea name="crtr_description" placeholder="Description (optional)" rows="4"></textarea>
            <input type="file" name="crtr_image" accept="image/*" />
            <button type="submit">Log Entry</button>
        </form>
    </section>

    <section>
        <h2 class="subtitle">Encounter Log – Registered Creatures</h2>
   <form method="GET" class="filters" style="margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
    <select name="user_filter">
        <option value="">All Users</option>
        <?php
        $users = $pdo->query("SELECT DISTINCT logged_by FROM creatures ORDER BY logged_by ASC");
        foreach ($users as $u) {
            $val = $u['logged_by'];
            $selected = ($_GET['user_filter'] ?? '') === $val ? 'selected' : '';
            echo "<option value=\"$val\" $selected>" . htmlspecialchars($val) . "</option>";
        }
        ?>
    </select>

    <select name="type_filter">
        <option value="">All Types</option>
        <?php
        $types = $pdo->query("SELECT DISTINCT crtr_type FROM creatures ORDER BY crtr_type ASC");
        foreach ($types as $t) {
            $val = htmlspecialchars($t['crtr_type']);
            $selected = ($_GET['type_filter'] ?? '') === $val ? 'selected' : '';
            echo "<option value=\"$val\" $selected>$val</option>";
        }
        ?>
    </select>

    <select name="env_filter">
        <option value="">All Environments</option>
        <?php
        $envs = $pdo->query("SELECT DISTINCT crtr_environment FROM creatures ORDER BY crtr_environment ASC");
        foreach ($envs as $e) {
            $val = htmlspecialchars($e['crtr_environment']);
            $selected = ($_GET['env_filter'] ?? '') === $val ? 'selected' : '';
            echo "<option value=\"$val\" $selected>$val</option>";
        }
        ?>
    </select>

    <button type="submit">Apply Filters</button>
    <a href="dashboard.php" style="align-self: center; padding: 0.5rem 1rem; background-color: #444; color: white; text-decoration: none; border-radius: 4px;">Clear Filters</a>
    </form>
        <table class="bestiary-table">
            <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Size</th>
                <th>Progeny</th>
                <th>Classification</th>
                <th>Native Environment</th>
                <th>Logged By</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $where = [];
            $params = [];

            if (!empty($_GET['user_filter'])) {
                $where[] = 'logged_by = ?';
                $params[] = $_GET['user_filter'];
            }
            if (!empty($_GET['type_filter'])) {
                $where[] = 'crtr_type = ?';
                $params[] = $_GET['type_filter'];
            }
            if (!empty($_GET['env_filter'])) {
                $where[] = 'crtr_environment = ?';
                $params[] = $_GET['env_filter'];
            }
                $sql = "SELECT * FROM creatures";
                if ($where) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }
                $sql .= " ORDER BY id DESC"; // ← this line is changed


            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
           $index = 1;

            while ($creature = $stmt->fetch()):
                    $typeSlug = strtolower(preg_replace('/[^a-z0-9]/', '', $creature['crtr_type']));
                    $classClass = !empty($typeSlug) ? "classification-$typeSlug" : "classification-default";
                ?>
                    <tr>
                        <td><?= str_pad($index++, 4, '0', STR_PAD_LEFT) ?></td> <!-- Displayed No. -->
                        <td><a href="creaturesdesc.php?id=<?= urlencode($creature['crtr_id']) ?>">
                            <?= htmlspecialchars($creature['crtr_name']) ?></a></td>
                        <td><?= htmlspecialchars($creature['crtr_size']) ?></td>
                        <td><?= htmlspecialchars($creature['crtr_progeny']) ?: '—' ?></td>
                        <td><span class="classification <?= $classClass ?>">
                            <?= strtoupper(htmlspecialchars($creature['crtr_type'])) ?></span></td>
                        <td><?= htmlspecialchars($creature['crtr_environment']) ?></td>
                        <td><?= htmlspecialchars($creature['logged_by']) ?></td>
                        <td>
                            <a href="edit_creature.php?id=<?= urlencode($creature['crtr_id']) ?>" title="Edit">Edit</a> |
                            <a href="delete_creature.php?id=<?= urlencode($creature['crtr_id']) ?>"
                               onclick="return confirm('Confirm deletion of this record?')" title="Delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>


            </tbody>
        </table>
    </section>

    <footer class="terminal-footer">
        <p>Valhalla Tactical Systems — Access Level: Secure</p>
    </footer>
</div>
<script>
    window.addEventListener("DOMContentLoaded", () => {
        const frame = document.querySelector(".terminal-frame");
        frame.style.opacity = 0;
        frame.style.transform = "translateY(10px)";
        setTimeout(() => {
            frame.style.opacity = 1;
            frame.style.transform = "translateY(0)";
        }, 100);
    });
</script>
</body>
</html>
