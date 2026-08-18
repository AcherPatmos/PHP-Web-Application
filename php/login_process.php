<?php

$email = $_POST['email'];
$password = $_POST['password'];

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Verify the password
$isCorrect = password_verify($password, $hashedPassword);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Result</title>
</head>
<body>

<h2>Login Test</h2>

<p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>

<p><strong>Password Hash:</strong></p>

<p><?php echo $hashedPassword; ?></p>

<?php if ($isCorrect): ?>

    <h3 style="color:green;">
        Password verified successfully!
    </h3>

<?php else: ?>

    <h3 style="color:red;">
        Password verification failed.
    </h3>

<?php endif; ?>

</body>
</html>