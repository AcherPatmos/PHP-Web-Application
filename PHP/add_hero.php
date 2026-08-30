<?php
session_start();
require_once __DIR__ . '/db.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$errors = [];
$values = [
    'hero_name' => '', 'real_name' => '', 'short_bio' => '', 'long_bio' => '',
    'powers' => '', 'image_url' => '', 'team' => '', 'gender' => '', 'status' => 'Active'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $default) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    
    if ($values['hero_name'] === '') $errors[] = 'Hero Name is required.';
    if ($values['real_name'] === '') $errors[] = 'Real Name is required.';
    if ($values['short_bio'] === '') $errors[] = 'Short Biography is required.';
    if ($values['long_bio'] === '') $errors[] = 'Full Biography is required.';
    if ($values['powers'] === '') $errors[] = 'Powers is required.';
    if ($values['gender'] === '') $errors[] = 'Gender is required.';
    $valid_statuses = ['Active', 'Inactive', 'Deceased', 'Unknown'];
    if (!in_array($values['status'], $valid_statuses, true)) $errors[] = 'Invalid status selected.';

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO heroes (hero_name, real_name, short_bio, long_bio, powers, image_url, team, gender, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $image_url = $values['image_url'] !== '' ? $values['image_url'] : null;
        $team = $values['team'] !== '' ? $values['team'] : null;

        $stmt->bind_param(
            'sssssssss',
            $values['hero_name'], $values['real_name'], $values['short_bio'], $values['long_bio'],
            $values['powers'], $image_url, $team, $values['gender'], $values['status']
        );

        if ($stmt->execute()) {
            $_SESSION['flash'] = htmlspecialchars($values['hero_name']) . ' was added to the roster.';
            header('Location: manage_heroes.php');
            exit;
        } else {
            $errors[] = 'Something went wrong saving this hero. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Hero — Cerebro</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>

<header class="site-header">
  <div class="header-inner">
    <a href="../index.php" class="brand">
      <div class="brand-mark">C</div>
      <div class="brand-text">
        <span class="brand-name">Cerebro</span>
      </div>
    </a>
    <nav class="main-nav">
      <a href="../index.php">Roster</a>
      <a href="manage_heroes.php">Manage Heroes</a>
      <span class="nav-user">Signed in as <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'staff'); ?></strong></span>
      <a href="logout.php" class="btn-nav">Log Out</a>
    </nav>
  </div>
</header>

<main>
  <a href="manage_heroes.php" class="back-link">&larr; Back to Manage Heroes</a>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="form-card">
    <p class="eyebrow">Case File Intake</p>
    <h1>Add New Hero</h1>
    <form action="add_hero.php" method="POST">
      <div class="form-row">
        <div class="form-group"><label>Hero Name *</label><input type="text" name="hero_name" value="<?php echo htmlspecialchars($values['hero_name']); ?>" required></div>
        <div class="form-group"><label>Real Name *</label><input type="text" name="real_name" value="<?php echo htmlspecialchars($values['real_name']); ?>" required></div>
      </div>
      <div class="form-group">
        <label>Short Biography *</label>
        <textarea name="short_bio" rows="2" required><?php echo htmlspecialchars($values['short_bio']); ?></textarea>
        <small class="field-hint">Shown on the roster grid</small>
      </div>
      <div class="form-group">
        <label>Full Biography *</label>
        <textarea name="long_bio" rows="5" required><?php echo htmlspecialchars($values['long_bio']); ?></textarea>
        <small class="field-hint">Shown on the hero's detail page</small>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Powers *</label><input type="text" name="powers" value="<?php echo htmlspecialchars($values['powers']); ?>" required></div>
        <div class="form-group"><label>Photo URL</label><input type="text" name="image_url" value="<?php echo htmlspecialchars($values['image_url']); ?>" placeholder="assets/images/hero.jpg"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Team</label><input type="text" name="team" value="<?php echo htmlspecialchars($values['team']); ?>"></div>
        <div class="form-group">
          <label>Gender *</label>
          <select name="gender" required>
            <option value="">Select…</option>
            <?php foreach (['Male', 'Female', 'Non-binary', 'Unknown'] as $g): ?>
              <option value="<?php echo $g; ?>" <?php echo $values['gender'] === $g ? 'selected' : ''; ?>><?php echo $g; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group" style="max-width:240px;">
        <label>Status *</label>
        <select name="status" required>
          <?php foreach (['Active', 'Inactive', 'Deceased', 'Unknown'] as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $values['status'] === $s ? 'selected' : ''; ?>><?php echo $s; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save Hero</button>
        <a href="manage_heroes.php" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>
</main>

<footer><span>Cerebro &middot; Mutant Roster Manager &middot; Staff Area</span></footer>

</body>
</html>