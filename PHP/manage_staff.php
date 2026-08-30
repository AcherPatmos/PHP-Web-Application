<?php
// manage_staff.php lists everyone who can sign in, and links to add more

require_once __DIR__ . '/db.php';
require_login();

$flash = take_flash();

$result = $conn->query(
    'SELECT staff_id, username, full_name, created_at
     FROM staff
     ORDER BY username ASC'
);

$total = $result ? $result->num_rows : 0;

// Used below to stop you deleting the account you are currently using.
$me = (int)($_SESSION['staff_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff accounts — Cerebro</title>

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
      <a href="manage_staff.php" class="is-active">Staff</a>
      <span class="nav-user">Signed in as <strong><?php echo e(current_staff_name()); ?></strong></span>
      <a href="logout.php" class="btn-nav">Log Out</a>
    </nav>
  </div>
</header>

<main>

  <div class="page-heading">
    <p class="eyebrow">Staff area</p>
    <h1>Staff accounts</h1>
    <p class="subtitle">Everyone listed here can add, edit, and delete hero records.</p>
  </div>

  <?php if ($flash): ?>
    <div class="flash-message"><?php echo e($flash); ?></div>
  <?php endif; ?>

  <div class="toolbar">
    <span class="result-count"><?php echo (int)$total; ?> account<?php echo $total === 1 ? '' : 's'; ?></span>
    <a href="add_staff.php" class="btn-primary">+ Add staff account</a>
  </div>

  <div class="table-wrap">
    <table class="staff-table">
      <thead>
        <tr>
          <th scope="col">Username</th>
          <th scope="col">Full name</th>
          <th scope="col">Added</th>
          <th scope="col"><span class="visually-hidden">Actions</span></th>
        </tr>
      </thead>
      <tbody>
        <?php while ($member = $result->fetch_assoc()): ?>
          <?php
            // Is this row the person who is currently signed in?
            $is_me = ((int)$member['staff_id'] === $me);
          ?>
          <tr>
            <td>
              <strong><?php echo e($member['username']); ?></strong>
              <?php if ($is_me): ?>
                <span class="tag">You</span>
              <?php endif; ?>
            </td>

            <!-- full_name is optional in the database, so it may be NULL -->
            <td><?php echo $member['full_name'] !== null ? e($member['full_name']) : '&mdash;'; ?></td>

            <td><?php echo date('j M Y', strtotime($member['created_at'])); ?></td>

            <td class="row-actions">
              <?php if ($is_me): ?>

                <span class="muted-note">Current session</span>
              <?php else: ?>
                <form action="delete_staff.php" method="POST" class="inline-form"
                      onsubmit="return confirm('Remove the account <?php echo e($member['username']); ?>?');">
                  <input type="hidden" name="staff_id" value="<?php echo (int)$member['staff_id']; ?>">
                  <button type="submit" class="btn-danger-card">Remove</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <p class="field-hint page-note">
    Passwords are stored as bcrypt hashes and cannot be read back, not even here.
    If someone forgets theirs, remove the account and create a new one.
  </p>

</main>

<footer class="site-footer">
  <span>Cerebro &middot; Mutant Roster Manager &middot; Staff area</span>
</footer>

</body>
</html>