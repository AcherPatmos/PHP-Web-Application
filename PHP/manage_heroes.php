<?php
session_start();
require_once __DIR__ . '/db.php';


if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}


$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$sql = "SELECT hero_id, hero_name, real_name, short_bio, image_url, team, status
        FROM heroes ORDER BY hero_name ASC";
$result = $conn->query($sql);
$total = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Heroes — Cerebro</title>
<link rel="stylesheet" href="../style.css">
</head>
<body>

<header class="site-header">
  <div class="header-inner">
    <a href="../index.php" class="brand">
      <div class="brand-text"><span class="brand-name">Cerebro</span></div>
    </a>
    <nav class="main-nav">
      <a href="../index.php">Roster</a>
      <a href="manage_heroes.php" class="is-active">Manage Heroes</a>
      <span class="nav-user">Signed in as <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'staff'); ?></strong></span>
      <a href="logout.php" class="btn-nav">Log Out</a>
    </nav>
  </div>
</header>
 
<div class="page-heading">
  <h1>Manage Heroes</h1>
  <p class="subtitle">Add, update, or remove hero records. Changes reflect immediately on the public roster.</p>
</div>
 
<div class="search-bar">
  <?php if ($flash): ?>
    <div class="flash-message"><?php echo htmlspecialchars($flash); ?></div>
  <?php endif; ?>
</div>
 
<div class="toolbar">
  <span class="result-count"><?php echo (int)$total; ?> records</span>
  <a href="add_hero.php" class="btn-primary">+ Add New Hero</a>
</div>
 
<div class="hero-grid">
  <?php if ($result && $total > 0): ?>
    <?php while ($hero = $result->fetch_assoc()): ?>
      <?php
        $initial = strtoupper(substr($hero['hero_name'], 0, 1));
        $status_class = 'stamp-' . strtolower($hero['status']);
        $has_image = !empty($hero['image_url']);
      ?>
      <article class="hero-card">
        <div class="hero-card-media">
          <?php if ($has_image): ?>
            <img src="../<?php echo htmlspecialchars($hero['image_url']); ?>"
                 alt="<?php echo htmlspecialchars($hero['hero_name']); ?>"
                 onerror="this.replaceWith(Object.assign(document.createElement('span'), {className:'hero-initial', textContent:'<?php echo htmlspecialchars($initial); ?>'}));">
          <?php else: ?>
            <span class="hero-initial"><?php echo htmlspecialchars($initial); ?></span>
          <?php endif; ?>
        </div>
        <div class="hero-card-body">
          <h2><?php echo htmlspecialchars($hero['hero_name']); ?></h2>
          <p class="real-name"><?php echo htmlspecialchars($hero['real_name']); ?></p>
          <p class="bio-snippet"><?php echo htmlspecialchars($hero['short_bio']); ?></p>
        </div>
        <div class="hero-card-footer">
          <div class="hero-card-tags">
            <?php if (!empty($hero['team'])): ?>
              <span class="tag"><?php echo htmlspecialchars($hero['team']); ?></span>
            <?php endif; ?>
            <span class="stamp <?php echo htmlspecialchars($status_class); ?>"><?php echo htmlspecialchars($hero['status']); ?></span>
          </div>
          <div class="card-actions">
            <a href="edit_hero.php?id=<?php echo (int)$hero['hero_id']; ?>" class="btn-ghost">Edit</a>
            <form action="delete_hero.php" method="POST" class="inline-form"
                  onsubmit="return confirm('Delete <?php echo htmlspecialchars(addslashes($hero['hero_name'])); ?>? This action is permanent and cannot be undone.');">
              <input type="hidden" name="hero_id" value="<?php echo (int)$hero['hero_id']; ?>">
              <button type="submit" class="btn-danger-card">Delete</button>
            </form>
          </div>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="subtitle">No heroes on file yet. <a href="add_hero.php">Add the first one.</a></p>
  <?php endif; ?>
</div>
 
</body>
</html>
 
