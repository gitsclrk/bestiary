<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    $_SESSION['flash'] = "No creature ID specified.";
    header('Location: dashboard.php');
    exit;
}

$id = $_GET['id'];

// Check and delete image if it exists
$stmt = $pdo->prepare("SELECT crtr_image FROM creatures WHERE crtr_id = ?");
$stmt->execute([$id]);
$image = $stmt->fetchColumn();

if ($image && file_exists(__DIR__ . '/../uploads/' . $image)) {
    unlink(__DIR__ . '/../uploads/' . $image);
}

// Delete the record
$stmt = $pdo->prepare("DELETE FROM creatures WHERE crtr_id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount()) {
    $_SESSION['flash'] = "Creature deleted.";
} else {
    $_SESSION['flash'] = "Creature not found or could not be deleted.";
}

header("Location: dashboard.php");
exit;
