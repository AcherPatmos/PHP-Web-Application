<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

     <link rel="stylesheet" href="../css/style.css">
</head>
<body>

     <div class="login-container">
    <h1>Welcome Back</h1>

    <form id="loginForm" action="login_process.php" method="POST">

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Email Address"
            required>
        <small id="emailError"></small>
    

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Password"
            required>
        <small id="passwordError"></small>

        <button type="submit">Login</button>

    </form>
</div>

<script src="../js/validation.js"></script>
</body>
</html>