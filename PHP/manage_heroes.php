<?php
session_start();
require_once __DIR__ . '/PHP/db.php';


if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}


$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$sql = "SELECT hero_id, hero_name, real_name, team, status FROM heroes ORDER BY hero_name ASC";
$result = $conn->query($sql);
$total = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Heroes — Cerebro</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="site-header">
  <div class="brand">
    <div class="brand-mark">C</div>
    <div>
      <div class="brand-name">Cerebro</div>
      <div class="brand-tag">Mutant Roster Manager</div>
    </div>
  </div>
  <nav>
    <a href="index.php">Roster</a>
    <a href="manage_heroes.php" class="active">Manage Heroes</a>
    <span class="nav-user">Signed in as <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'staff'); ?></strong></span>
    <a href="logout.php" class="btn-login">Log Out</a>
  </nav>
</header>

<main>
  <h1>Manage Heroes</h1>
  <p class="lede">Add, update, or remove hero records. Changes reflect immediately on the public roster.</p>

  <?php if ($flash): ?>
    <div class="flash-message"><?php echo htmlspecialchars($flash); ?></div>
  <?php endif; ?>

  <div class="toolbar">
    <span class="result-count"><?php echo (int)$total; ?> records</span>
    <a href="add_hero.php" class="btn btn-primary">+ Add New Hero</a>
  </div>

  <table class="manage-table">
    <thead>
      <tr><th>Hero</th><th>Real Name</th><th>Team</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($result && $total > 0): ?>
        <?php while ($hero = $result->fetch_assoc()): ?>
          <?php $status_class = 'stamp-' . strtolower($hero['status']); ?>
          <tr>
            <td><?php echo htmlspecialchars($hero['hero_name']); ?></td>
            <td><?php echo htmlspecialchars($hero['real_name']); ?></td>
            <td><?php echo htmlspecialchars($hero['team'] ?? '—'); ?></td>
            <td><span class="stamp <?php echo htmlspecialchars($status_class); ?>"><?php echo htmlspecialchars($hero['status']); ?></span></td>
            <td class="row-actions">
              <a href="edit_hero.php?id=<?php echo (int)$hero['hero_id']; ?>" class="btn btn-edit">Edit</a>

              <form action="delete_hero.php" method="POST" class="inline-form"
                    onsubmit="return confirm('Delete <?php echo htmlspecialchars(addslashes($hero['hero_name'])); ?>? This action is permanent and cannot be undone.');">
                <input type="hidden" name="hero_id" value="<?php echo (int)$hero['hero_id']; ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5">No heroes on file yet. <a href="add_hero.php">Add the first one.</a></td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</main>

<footer>
  <span>Cerebro &middot; Mutant Roster Manager &middot; Staff Area</span>
</footer>

</body>
</html>