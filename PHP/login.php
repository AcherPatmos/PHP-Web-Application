<?php
// login.php for the staff sign-in screen.

require_once __DIR__ . '/db.php';

// Already signed in? No reason to look at this page.
if (is_logged_in()) {
    header('Location: manage_heroes.php');
    exit;
}

$error_code = $_GET['error'] ?? '';
$error_message = '';

if ($error_code === 'empty') {
    $error_message = 'Enter both your username and your password.';
} elseif ($error_code === 'invalid') {
    $error_message = 'That username and password do not match an account.';
}

// After logging out we say so, rather than showing a bare login box.
$notice = isset($_GET['out']) ? 'You have been signed out.' : '';

// A one-time message handed over by another page, e.g. "Account created".
$flash = take_flash();
if ($flash !== null) {
    $notice = $flash;
}

// Count the staff accounts. If there are none, the very first thing anyone
// needs is the setup page, so we point at it instead of a dead login form.
$staff_count = (int)$conn->query('SELECT COUNT(*) AS c FROM staff')->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cerebro | Staff Login</title>

<!-- This page uses its own split-screen stylesheet, not the roster one -->
<link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="container">

  <!-- LEFT PANEL -->
  <section class="login-panel">

    <a class="logo" href="../index.php">Cerebro</a>

    <div class="login-header">
      <h4>Restricted access</h4>
      <h1>Staff login</h1>
      <p>Sign in to create, edit, or remove case files.</p>
    </div>

    <?php if ($staff_count === 0): ?>
      <!-- Nobody has an account yet, so there is nothing to log in to -->
      <div class="alert alert-notice">
        No staff accounts exist yet.
        <a href="setup_staff.php">Create the first account</a> to get started.
      </div>
    <?php endif; ?>

    <?php if ($error_message !== ''): ?>
      <div class="alert alert-error"><?php echo e($error_message); ?></div>
    <?php endif; ?>

    <?php if ($notice !== ''): ?>
      <div class="alert alert-notice"><?php echo e($notice); ?></div>
    <?php endif; ?>

    <!-- novalidate turns off the browser's own bubbles so validation.js
         can show messages that match the rest of the page -->
    <form id="loginForm" action="login_process.php" method="POST" novalidate>

      <label for="username">Username</label>

      <!-- type="text", not type="email". These are usernames like "prof.x",
           and type="email" would refuse anything without an @ sign. -->
      <input type="text"
             id="username"
             name="username"
             autocomplete="username"
             placeholder="Enter your username"
             value="">

      <small id="usernameError"></small>

      <label for="password">Password</label>

      <input type="password"
             id="password"
             name="password"
             autocomplete="current-password"
             placeholder="Enter your password">

      <small id="passwordError"></small>

      <button class="login-btn" type="submit">Log in</button>

    </form>

    <p class="back-to-site"><a href="index.php">&larr; Back to the public roster</a></p>

    <footer>
      <p>Cerebro &bull; Internal Case File System &bull; 2026</p>
      <p>Authorized personnel only may modify records.</p>
    </footer>

  </section>

  <!-- RIGHT PANEL -->
  <section class="image-panel">
    <img src="../images/wolverine.jpeg" alt="wolverine image">
  </section>

</div>

<script src="../js/validation.js"></script>

</body>
</html>