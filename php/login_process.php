<?php

$email = $_POST['email'];
$password = $_POST['password'];

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Login Details</h2>";

echo "Email: " . htmlspecialchars($email) . "<br><br>";

echo "Original Password: " . htmlspecialchars($password) . "<br><br>";

echo "Hashed Password:<br>";
echo $hashedPassword;

?>