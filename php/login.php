<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASAegis Registry | Staff Login</title>

    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container">

    <!-- LEFT PANEL -->
    <section class="login-panel">

        <div class="logo">
            ASAegis Registry
        </div>

        <div class="login-header">

            <h4>Restricted Access</h4>

            <h1>Staff Login</h1>

            <p>
                Sign in to create, edit, or remove case files.
            </p>

        </div>

        <form id="loginForm" action="login_process.php" method="POST" novalidate>

            <label for="email">Username</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your username">

            <small id="emailError"></small>

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password">

            <small id="passwordError"></small>

            <button class="login-btn" type="submit">
                Log In
            </button>

        </form>

        <footer>

            
                <p>ASAegis Registry • Internal Case File System • 2026</p>

                <p>Authorized personnel only may modify records.</p>

        </footer>

    </section>

    <!-- RIGHT PANEL -->

    <section class="image-panel">

        <img src="../images/wolverine.jpg.jpeg" alt="Wolverine">

    </section>

</div>

<script src="../js/validation.js"></script>

</body>
</html>