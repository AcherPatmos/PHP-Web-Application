<?php
// setup_staff.php creates the very first staff account.


require_once __DIR__ . '/db.php';

// If anyone at all is on file, this page is closed for good.
$staff_count = (int)$conn->query('SELECT COUNT(*) AS c FROM staff')->fetch_assoc()['c'];

if ($staff_count > 0) {
    header('Location: login.php');
    exit;
}

$errors = [];

// Keeps what was typed so a failed submit does not clear the form.
$values = ['username' => '', 'full_name' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $values['username']  = trim($_POST['username'] ?? '');
    $values['full_name'] = trim($_POST['full_name'] ?? '');

    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($values['username'] === '') {
        $errors[] = 'Choose a username.';
    } elseif (strlen($values['username']) < 3) {
        $errors[] = 'The username needs at least 3 characters.';
    }

    if ($password === '') {
        $errors[] = 'Choose a password.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'The password needs at least 8 characters.';
    }
    // psw pattern matching to make sure the user is sure about their psw
    if ($password !== $confirm) {
        $errors[] = 'The two passwords do not match.';
    }

    if (empty($errors)) {
        // psw hashing for protection
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $full_name = $values['full_name'] !== '' ? $values['full_name'] : null;

        $stmt = $conn->prepare(
            'INSERT INTO staff (username, password, full_name) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('sss', $values['username'], $hash, $full_name);

        if ($stmt->execute()) {
            $stmt->close();
            set_flash('Account created. Sign in with your new details.');
            header('Location: login.php');
            exit;
        }

        $errors[] = 'Could not create the account. Try again.';
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cerebro | First-time setup</title>
<link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="container">

  <section class="login-panel">

    <a class="logo" href="index.php">Cerebro</a>

    <div class="login-header">
      <h4>First-time setup</h4>
      <h1>Create the first account</h1>
      <p>
        No staff accounts exist yet. Set up the first one now.
        This page closes itself the moment that account exists.
      </p>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="setup_staff.php" method="POST" novalidate>

      <label for="username">Username</label>
      <input type="text" id="username" name="username"
             autocomplete="username"
             placeholder="e.g. prof.x"
             value="<?php echo e($values['username']); ?>">
      <small></small>

      <label for="full_name">Full name (optional)</label>
      <input type="text" id="full_name" name="full_name"
             placeholder="e.g. Charles Xavier"
             value="<?php echo e($values['full_name']); ?>">
      <small></small>

      <label for="password">Password</label>
      <input type="password" id="password" name="password"
             autocomplete="new-password"
             placeholder="At least 8 characters">
      <small></small>

      <label for="confirm_password">Confirm password</label>
      <input type="password" id="confirm_password" name="confirm_password"
             autocomplete="new-password"
             placeholder="Type it again">
      <small></small>

      <button class="login-btn" type="submit">Create account</button>

    </form>

    <footer>
      <p>Cerebro &bull; Internal Case File System &bull; 2026</p>
      <p>Delete this file once your accounts are set up if you prefer.</p>
    </footer>

  </section>

  <section class="image-panel">
    <img src="../images/wolverine.jpeg" alt="wolverine">
  </section>

</div>

</body>
</html>