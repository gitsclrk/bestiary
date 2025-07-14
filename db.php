<?php
try {
    // Connect to the SQLite database in the current directory
    $pdo = new PDO('sqlite:' . __DIR__ . '/bestiary.sqbpro');

    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set default fetch mode to associative arrays
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Disable emulated prepared statements (for better security)
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
