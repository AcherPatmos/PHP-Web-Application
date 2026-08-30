<?php
session_start();
require_once __DIR__ . '/db.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_heroes.php');
    exit;
}

$id = (int)($_POST['hero_id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash'] = 'No hero selected to delete.';
    header('Location: manage_heroes.php');
    exit;
}


$stmt = $conn->prepare("SELECT hero_name FROM heroes WHERE hero_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    $_SESSION['flash'] = 'That hero was already removed.';
    header('Location: manage_heroes.php');
    exit;
}

$delete = $conn->prepare("DELETE FROM heroes WHERE hero_id = ?");
$delete->bind_param('i', $id);

if ($delete->execute()) {
    $_SESSION['flash'] = htmlspecialchars($row['hero_name']) . ' was removed from the roster.';
} else {
    $_SESSION['flash'] = 'Something went wrong deleting that hero. Please try again.';
}
$delete->close();

header('Location: manage_heroes.php');
exit;
