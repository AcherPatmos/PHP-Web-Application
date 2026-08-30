<?php
// login_process.php checks a submitted username and password.
// This page never prints anything. It either sets up the session and sends
// you to Manage Heroes, or sends you back to the login form with an error.

require_once __DIR__ . '/db.php';

// Only POST gets here. Typing this URL in the address bar is a GET,
// so a user just bounces back to the form.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Username and psw inputs
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// if one of them is wrong, an error is flagged
if ($username === '' || $password === '') {
    header('Location: login.php?error=empty');
    exit;
}

// Look the account up by username only
$stmt = $conn->prepare('SELECT staff_id, username, password FROM staff WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($staff && password_verify($password, $staff['password'])) {

    // Give the session a brand-new id now that the privilege level changed.


    session_regenerate_id(true);

    $_SESSION['staff_id'] = $staff['staff_id'];
    $_SESSION['username'] = $staff['username'];

    header('Location: manage_heroes.php');
    exit;
}

header('Location: login.php?error=invalid');
exit;