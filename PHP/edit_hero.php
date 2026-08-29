<?php
session_start();
require_once __DIR__ . '/PHP/db.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$id = (int)($_GET['id'] ?? $_POST['hero_id'] ?? 0);
if ($id <= 0) {
    $_SESSION['flash'] = 'No hero selected to edit.';
    header('Location: manage_heroes.php');
    exit;
}

$errors = [];

// just to load the hero data for the form ... will fetch it from the database
$stmt = $conn->prepare("SELECT * FROM heroes WHERE hero_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$hero_result = $stmt->get_result();
$hero = $hero_result->fetch_assoc();
$stmt->close();

if (!$hero) {
    $_SESSION['flash'] = 'That hero no longer exists.';
    header('Location: manage_heroes.php');
    exit;
}

$values = $hero;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['hero_name','real_name','short_bio','long_bio','powers','image_url','team','gender','status'] as $key) {
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
        $update = $conn->prepare(
            "UPDATE heroes
             SET hero_name = ?, real_name = ?, short_bio = ?, long_bio = ?, powers = ?,
                 image_url = ?, team = ?, gender = ?, status = ?
             WHERE hero_id = ?"
        );
        $image_url = $values['image_url'] !== '' ? $values['image_url'] : null;
        $team = $values['team'] !== '' ? $values['team'] : null;

        $update->bind_param(
            'sssssssssi',
            $values['hero_name'], $values['real_name'], $values['short_bio'], $values['long_bio'],
            $values['powers'], $image_url, $team, $values['gender'], $values['status'], $id
        );

        if ($update->execute()) {
            $_SESSION['flash'] = htmlspecialchars($values['hero_name']) . ' was updated.';
            header('Location: manage_heroes.php');
            exit;
        } else {
            $errors[] = 'Something went wrong saving these changes. Please try again.';
        }
        $update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit <?php echo htmlspecialchars($values['hero_name']); ?> — Cerebro</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="brand">
    <div class="brand-mark">C</div>
    <div><div class="brand-name">Cerebro</div><div class="brand-tag">Mutant Roster Manager</div></div>
  </div>
  <nav>
    <a href="index.php">Roster</a>
    <a href="manage_heroes.php" class="active">Manage Heroes</a>
    <span class="nav-user">Signed in as <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'staff'); ?></strong></span>
    <a href="logout.php" class="btn-login">Log Out</a>
  </nav>
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
    <h1>Edit <?php echo htmlspecialchars($values['hero_name']); ?></h1>
    <form action="edit_hero.php?id=<?php echo (int)$id; ?>" method="POST">
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
        <div class="form-group"><label>Photo URL</label><input type="text" name="image_url" value="<?php echo htmlspecialchars($values['image_url'] ?? ''); ?>" placeholder="assets/images/hero.jpg"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Team</label><input type="text" name="team" value="<?php echo htmlspecialchars($values['team'] ?? ''); ?>"></div>
        <div class="form-group">
          <label>Gender *</label>
          <select name="gender" required>
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
        <button class="btn btn-primary" type="submit">Save Changes</button>
        <a href="manage_heroes.php" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>

  <div class="danger-zone">
    <div>
      <h4>Remove this file</h4>
      <p>This permanently deletes <?php echo htmlspecialchars($values['hero_name']); ?> from the roster. This cannot be undone.</p>
    </div>
    <form action="delete_hero.php" method="POST"
          onsubmit="return confirm('Delete <?php echo htmlspecialchars(addslashes($values['hero_name'])); ?>? This action is permanent and cannot be undone.');">
      <input type="hidden" name="hero_id" value="<?php echo (int)$id; ?>">
      <button type="submit" class="btn btn-danger">Delete This Hero</button>
    </form>
  </div>
</main>

<footer><span>Cerebro &middot; Mutant Roster Manager &middot; Staff Area</span></footer>

</body>
</html>