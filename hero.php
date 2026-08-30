<?php
session_start();
require_once __DIR__ . '/PHP/db.php';

$logged_in = is_logged_in();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM heroes WHERE hero_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$hero = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$hero) {
    header('Location: index.php');
    exit;
}

$initial = strtoupper(substr($hero['hero_name'], 0, 1));
$status_class = 'stamp-' . strtolower($hero['status']);
$has_image = !empty($hero['image_url']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($hero['hero_name']); ?> — Cerebro</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
  <div class="header-inner">
    <a href="index.php" class="brand">
      <div class="brand-mark">C</div>
      <div class="brand-text">
        <span class="brand-name">Cerebro</span>
      </div>
    </a>
    <nav class="main-nav">
      <a href="index.php">Roster</a>
      <?php if ($logged_in): ?>
        <a href="PHP/add_hero.php">Add Hero</a>
        <span class="nav-user">Signed in as <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'staff'); ?></strong></span>
        <a href="PHP/logout.php" class="btn-nav">Log Out</a>
      <?php else: ?>
        <a href="PHP/login.php" class="btn-nav">Staff Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<div class="page-heading">
  <a href="index.php" class="back-link">&larr; Back to Roster</a>
</div>

<div class="hero-card" style="max-width: 720px; margin: 0 20px 40px;">
  <div class="hero-card-media" style="height: 220px;">
    <?php if ($has_image): ?>
      <img src="<?php echo htmlspecialchars($hero['image_url']); ?>"
           alt="<?php echo htmlspecialchars($hero['hero_name']); ?>"
           onerror="this.replaceWith(Object.assign(document.createElement('span'), {className:'hero-initial', textContent:'<?php echo htmlspecialchars($initial); ?>'}));">
    <?php else: ?>
      <span class="hero-initial" style="font-size: 4rem;"><?php echo htmlspecialchars($initial); ?></span>
    <?php endif; ?>
  </div>

  <div class="hero-card-body">
    <p class="eyebrow">Case File</p>
    <h2><?php echo htmlspecialchars($hero['hero_name']); ?></h2>
    <p class="real-name">Real name: <?php echo htmlspecialchars($hero['real_name']); ?></p>

    <div class="hero-card-tags" style="margin-bottom: 16px;">
      <?php if (!empty($hero['team'])): ?>
        <span class="tag"><?php echo htmlspecialchars($hero['team']); ?></span>
      <?php endif; ?>
      <?php if (!empty($hero['gender'])): ?>
        <span class="tag"><?php echo htmlspecialchars($hero['gender']); ?></span>
      <?php endif; ?>
      <span class="stamp <?php echo htmlspecialchars($status_class); ?>"><?php echo htmlspecialchars($hero['status']); ?></span>
    </div>

    <p style="color: var(--text-muted); margin-bottom: 18px;">
      <strong style="color: var(--text-light);">Powers:</strong>
      <?php echo htmlspecialchars($hero['powers']); ?>
    </p>

    <div style="margin-bottom: 18px;">
      <h3 style="font-size: 0.85rem; color: var(--text-muted); border-bottom: 1px dashed var(--yellow); padding-bottom: 6px; margin-bottom: 8px;">
        Short Biography
      </h3>
      <p style="font-family: var(--font-body);"><?php echo nl2br(htmlspecialchars($hero['short_bio'])); ?></p>
    </div>

    <div style="margin-bottom: 18px;">
      <h3 style="font-size: 0.85rem; color: var(--text-muted); border-bottom: 1px dashed var(--yellow); padding-bottom: 6px; margin-bottom: 8px;">
        Full Biography
      </h3>
      <p style="font-family: var(--font-body);"><?php echo nl2br(htmlspecialchars($hero['long_bio'])); ?></p>
    </div>

    <p style="font-size: 0.78rem; color: var(--text-muted); font-style: italic;">
      On file since <?php echo date('F j, Y', strtotime($hero['created_at'])); ?>
    </p>

    <?php if ($logged_in): ?>
      <div class="form-actions" style="margin-top: 18px;">
        <a href="PHP/edit_hero.php?id=<?php echo (int)$hero['hero_id']; ?>" class="btn-primary">Edit This File</a>
      </div>
    <?php endif; ?>
  </div>
</div>

<footer class="site-footer">
  <span>Cerebro &middot; Mutant Roster Manager &middot; 2026</span>
  <span>Only authenticated staff may create, update, or delete records.</span>
</footer>

</body>
</html>