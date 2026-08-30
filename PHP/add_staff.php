<?php
// add_staff.php creates another account that can sign in and manage heroes. 
// only reachable by an admin

require_once __DIR__ . '/db.php';
require_login();

$errors = [];
$values = ['username' => '', 'full_name' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $values['username']  = trim($_POST['username'] ?? '');
    $values['full_name'] = trim($_POST['full_name'] ?? '');

    
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($values['username'] === '') {
        $errors[] = 'A username is required.';
    } elseif (strlen($values['username']) < 3) {
        $errors[] = 'The username needs at least 3 characters.';
    } elseif (strlen($values['username']) > 50) {
        // username character validation
        $errors[] = 'The username can be at most 50 characters.';
    }

    if ($password === '') {
        $errors[] = 'A password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'The password needs at least 8 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'The two passwords do not match.';
    }

    // Checks if the name is free before trying to insert although the UNIQUE index on the
    // column is the real guarantee
    if (empty($errors)) {
        $check = $conn->prepare('SELECT staff_id FROM staff WHERE username = ?');
        $check->bind_param('s', $values['username']);
        $check->execute();

        if ($check->get_result()->fetch_assoc()) {
            $errors[] = 'That username is already taken.';
        }

        $check->close();
    }

    if (empty($errors)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $full_name = $values['full_name'] !== '' ? $values['full_name'] : null;

        $stmt = $conn->prepare(
            'INSERT INTO staff (username, password, full_name) VALUES (?, ?, ?)'
        );
        $stmt->bind_param('sss', $values['username'], $hash, $full_name);

        if ($stmt->execute()) {
            $stmt->close();
            set_flash($values['username'] . ' can now sign in.');
            header('Location: manage_staff.php');
            exit;
        }

        $errors[] = 'Could not create that account. Try again.';
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add staff account — Cerebro</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@400;700;800&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&family=Special+Elite&display=swap">

<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header class="site-header">
  <div class="header-inner">
    <a href="index.php" class="brand">
      <div class="brand-mark">C</div>
      <div class="brand-text">
        <span class="brand-name">Cerebro</span>
        <span class="brand-sub">Staff area</span>
      </div>
    </a>

    <nav class="main-nav" aria-label="Primary">
      <a href="index.php">Roster</a>
      <a href="manage_heroes.php">Manage Heroes</a>
      <a href="manage_staff.php">Staff</a>
      <span class="nav-user">Signed in as <strong><?php echo e(current_staff_name()); ?></strong></span>
      <a href="logout.php" class="btn-nav">Log Out</a>
    </nav>
  </div>
</header>

<main>

  <a href="manage_staff.php" class="back-link">&larr; Back to staff accounts</a>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="form-card">
    <p class="eyebrow">Access control</p>
    <h1>Add staff account</h1>
    <p class="field-hint form-intro">
      The person you create this for will be able to add, edit, and delete hero
      records straight away.
    </p>

    <form action="add_staff.php" method="POST">

      <div class="form-row">
        <div class="form-group">
          <label for="username">Username *</label>

          <input type="text" id="username" name="username"
                 autocomplete="off"
                 value="<?php echo e($values['username']); ?>"
                 placeholder="e.g. ororo.munroe" required>
        </div>
        <div class="form-group">
          <label for="full_name">Full name</label>
          <input type="text" id="full_name" name="full_name"
                 value="<?php echo e($values['full_name']); ?>"
                 placeholder="e.g. Ororo Munroe">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="password">Password *</label>
          <input type="password" id="password" name="password"
                 autocomplete="new-password" required>
          <small class="field-hint">At least 8 characters</small>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm password *</label>
          <input type="password" id="confirm_password" name="confirm_password"
                 autocomplete="new-password" required>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Create account</button>
        <a href="manage_staff.php" class="btn btn-ghost">Cancel</a>
      </div>

    </form>
  </div>

</main>

<footer class="site-footer">
  <span>Cerebro &middot; Mutant Roster Manager &middot; Staff area</span>
</footer>

</body>
</html>