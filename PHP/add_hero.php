<?php
// add_hero.php contains the form for putting a new hero on the roster.
// Same file handles both jobs: a GET shows an empty form, a POST tries to save.

require_once __DIR__ . '/db.php';
require_login();

$errors = [];

// The default form state. Every key here matches a form field name below,
// which is what lets the foreach further down fill them all in one line.
$values = [
    'hero_name' => '',
    'real_name' => '',
    'short_bio' => '',
    'long_bio'  => '',
    'powers'    => '',
    'image_url' => '',
    'team'      => '',
    'gender'    => '',
    'status'    => 'Active'
];

// Kept in a variable because the check and the dropdown both need the same list.
$valid_statuses = ['Active', 'Inactive', 'Deceased', 'Unknown'];
$valid_genders  = ['Male', 'Female', 'Non-binary', 'Unknown'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($values as $key => $default) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    if ($values['hero_name'] === '') $errors[] = 'Hero name is required.';
    if ($values['real_name'] === '') $errors[] = 'Real name is required.';
    if ($values['short_bio'] === '') $errors[] = 'Short biography is required.';
    if ($values['long_bio']  === '') $errors[] = 'Full biography is required.';
    if ($values['powers']    === '') $errors[] = 'Powers is required.';

    // in_array with true as the third argument compares type as well as value,
    // so the number 0 cannot slip through as a match for a string.
    if (!in_array($values['gender'], $valid_genders, true)) {
        $errors[] = 'Choose a gender.';
    }

    if (!in_array($values['status'], $valid_statuses, true)) {
        $errors[] = 'Choose a valid status.';
    }

    if (empty($errors)) {

        $stmt = $conn->prepare(
            'INSERT INTO heroes
                (hero_name, real_name, short_bio, long_bio, powers, image_url, team, gender, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        // The two optional columns are NULL in the database when left blank,
        // rather than an empty string. 
        $image_url = $values['image_url'] !== '' ? $values['image_url'] : null;
        $team      = $values['team']      !== '' ? $values['team']      : null;

        // Nine s characters for nine string placeholders, in the same order.
        $stmt->bind_param(
            'sssssssss',
            $values['hero_name'], $values['real_name'], $values['short_bio'],
            $values['long_bio'],  $values['powers'],    $image_url,
            $team,                $values['gender'],    $values['status']
        );

        if ($stmt->execute()) {
            $stmt->close();

            set_flash($values['hero_name'] . ' was added to the roster.');

            header('Location: manage_heroes.php');
            exit;
        }

        $errors[] = 'Something went wrong saving this hero. Try again.';
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add hero — Cerebro</title>

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

  <a href="manage_heroes.php" class="back-link">&larr; Back to manage heroes</a>

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
    <p class="eyebrow">Case file intake</p>
    <h1>Add new hero</h1>

    <form action="add_hero.php" method="POST">

      <div class="form-row">
        <div class="form-group">
          <label for="hero_name">Hero name *</label>
          <input type="text" id="hero_name" name="hero_name"
                 value="<?php echo e($values['hero_name']); ?>" required>
        </div>
        <div class="form-group">
          <label for="real_name">Real name *</label>
          <input type="text" id="real_name" name="real_name"
                 value="<?php echo e($values['real_name']); ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label for="short_bio">Short biography *</label>
        <textarea id="short_bio" name="short_bio" rows="2" required><?php echo e($values['short_bio']); ?></textarea>
        <small class="field-hint">Shown on the roster cards</small>
      </div>

      <div class="form-group">
        <label for="long_bio">Full biography *</label>
        <textarea id="long_bio" name="long_bio" rows="5" required><?php echo e($values['long_bio']); ?></textarea>
        <small class="field-hint">Shown on the hero's own page</small>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="powers">Powers *</label>
          <input type="text" id="powers" name="powers"
                 value="<?php echo e($values['powers']); ?>" required>
        </div>
        <div class="form-group">
          <label for="image_url">Photo path</label>
          <input type="text" id="image_url" name="image_url"
                 value="<?php echo e($values['image_url']); ?>"
                 placeholder="images/heroes/storm.jpg">
          <small class="field-hint">Path from the project root. Leave blank for a letter tile.</small>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="team">Team</label>
          <input type="text" id="team" name="team" value="<?php echo e($values['team']); ?>">
        </div>
        <div class="form-group">
          <label for="gender">Gender *</label>
          <select id="gender" name="gender" required>
            <option value="">Select&hellip;</option>
            <?php foreach ($valid_genders as $g): ?>
              <!-- selected keeps the choice after a failed submit -->
              <option value="<?php echo e($g); ?>" <?php echo $values['gender'] === $g ? 'selected' : ''; ?>>
                <?php echo e($g); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group form-group-narrow">
        <label for="status">Status *</label>
        <select id="status" name="status" required>
          <?php foreach ($valid_statuses as $s): ?>
            <option value="<?php echo e($s); ?>" <?php echo $values['status'] === $s ? 'selected' : ''; ?>>
              <?php echo e($s); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">Save hero</button>
        <a href="manage_heroes.php" class="btn btn-ghost">Cancel</a>
      </div>

    </form>
  </div>

</main>

<footer class="site-footer">
  <span>Cerebro &middot; Mutant Roster Manager &middot; Staff area</span>
</footer>

</body>
</html>