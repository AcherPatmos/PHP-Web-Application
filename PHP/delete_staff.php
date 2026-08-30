<?php
// delete_staff.php removes a staff account. Prints nothing; always redirects.

require_once __DIR__ . '/db.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_staff.php');
    exit;
}

$id = (int)($_POST['staff_id'] ?? 0);
$me = (int)($_SESSION['staff_id'] ?? 0);

if ($id <= 0) {
    set_flash('No account was selected.');
    header('Location: manage_staff.php');
    exit;
}

// you cannot delete the account you are signed in with
if ($id === $me) {
    set_flash('You cannot remove the account you are signed in with.');
    header('Location: manage_staff.php');
    exit;
}

// the last account cannot go. With zero staff rows, nobody can
// sign in and nobody can create an account except through direct access to the database
$count = (int)$conn->query('SELECT COUNT(*) AS c FROM staff')->fetch_assoc()['c'];

if ($count <= 1) {
    set_flash('This is the only staff account. Create another one before removing it.');
    header('Location: manage_staff.php');
    exit;
}

// Read the username first so the confirmation can name the account.
$stmt = $conn->prepare('SELECT username FROM staff WHERE staff_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    set_flash('That account had already been removed.');
    header('Location: manage_staff.php');
    exit;
}

$delete = $conn->prepare('DELETE FROM staff WHERE staff_id = ?');
$delete->bind_param('i', $id);

if ($delete->execute()) {
    set_flash($row['username'] . ' can no longer sign in.');
} else {
    set_flash('Something went wrong removing that account. Try again.');
}

$delete->close();

header('Location: manage_staff.php');
exit;