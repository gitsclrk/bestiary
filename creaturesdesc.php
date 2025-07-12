<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($creature['crtr_name']) ?> – Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="terminal-body">
<div class="terminal-frame fade-in">
    <header>
        <h1 class="header-title"><?= htmlspecialchars($creature['crtr_name']) ?></h1>
        <p class="subtitle">
            Classification: <strong><?= htmlspecialchars($creature['crtr_type']) ?></strong>
        </p>
    </header>

    <section>
        <p><strong>Size:</strong> <?= htmlspecialchars($creature['crtr_size']) ?></p>
        <p><strong>Progeny:</strong> <?= $creature['crtr_progeny'] ? htmlspecialchars($creature['crtr_progeny']) : '—' ?></p>
        <p><strong>Habitat:</strong> <?= htmlspecialchars($creature['crtr_environment']) ?></p>
        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($creature['crtr_description'] ?? 'No description available.')) ?></p>
    </section>

    <?php if (!empty($creature['crtr_image'])): ?>
        <section style="margin-top: 2rem; text-align: center;">
            <img src="uploads/<?= htmlspecialchars($creature['crtr_image']) ?>"
                 alt="Creature image"
                 style="max-width: 100%; max-height: 400px; border: 2px solid #5effd6; border-radius: 6px;" />
        </section>
    <?php endif; ?>

    <div style="margin-top: 2rem; text-align: center;">
        <a href="dashboard.php" class="logout-button">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
