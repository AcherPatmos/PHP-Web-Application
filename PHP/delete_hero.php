<?php
// delete_hero.php removes one hero. Prints nothing; always redirects.

require_once __DIR__ . '/db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_heroes.php');
    exit;
}

$id = (int)($_POST['hero_id'] ?? 0);

if ($id <= 0) {
    set_flash('No hero was selected to delete.');
    header('Location: manage_heroes.php');
    exit;
}

// Read the name before deleting, so the confirmation message can say who went.
// After the DELETE runs the row is gone and there is nothing left to read.
$stmt = $conn->prepare('SELECT hero_name FROM heroes WHERE hero_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Already deleted, probably in another tab.
if (!$row) {
    set_flash('That hero had already been removed.');
    header('Location: manage_heroes.php');
    exit;
}

$delete = $conn->prepare('DELETE FROM heroes WHERE hero_id = ?');
$delete->bind_param('i', $id);

if ($delete->execute()) {
    
    set_flash($row['hero_name'] . ' was removed from the roster.');
} else {
    set_flash('Something went wrong deleting that hero. Try again.');
}

$delete->close();

header('Location: manage_heroes.php');
exit;