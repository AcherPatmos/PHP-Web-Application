<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($username === '' || $password === '') {
        header("Location: login.php?error=empty");
        exit();
    }

    $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header("Location: manage_heroes.php");
        exit();
    } else {
        header("Location: login.php?error=invalid");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}