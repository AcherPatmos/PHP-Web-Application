<?php
// hero.php: the full case file for one hero, reached from the roster cards.
// Public: anyone can read it. Staff also get an "Edit this file" button.

require_once __DIR__ . '/db.php';

$currentPage = 'hero.php';

// exits to homepage if someone wants to visit a hero with an invalid id
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Prepared statement, so the id is data and never part of the SQL text.
$stmt = $conn->prepare('SELECT * FROM heroes WHERE hero_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$hero = $stmt->get_result()->fetch_assoc();
$stmt->close();

// A valid-looking id that matches no row: back to the roster.
if (!$hero) {
    header('Location: index.php');
    exit;
}

$initial      = hero_initial($hero['hero_name']);
$status_class = status_class($hero['status']);
$has_image    = !empty($hero['image_url']);

// '' base because this page sits in the project root.
$img_src = hero_image_src($hero['image_url'], '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($hero['hero_name']); ?> — Cerebro</title>

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
        <span class="brand-sub">Mutant Roster</span>
      </div>
    </a>

    <nav class="main-nav" aria-label="Primary">
      <a href="index.php">Roster</a>

      <?php if (is_logged_in()): ?>
        <a href="PHP/manage_heroes.php">Manage Heroes</a>
        <a href="PHP/manage_staff.php">Staff</a>
        <span class="nav-user">Signed in as <strong><?php echo e(current_staff_name()); ?></strong></span>
        <a href="PHP/logout.php" class="btn-nav">Log Out</a>
      <?php else: ?>
        <a href="PHP/login.php" class="btn-nav">Staff Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>

  <div class="page-heading">
    <a href="index.php" class="back-link">&larr; Back to roster</a>
  </div>

  <article class="case-file">

    <div class="case-file-media">
      <?php if ($has_image): ?>
        <img src="<?php echo e($img_src); ?>"
             alt="<?php echo e($hero['hero_name']); ?>"
             onerror="this.replaceWith(Object.assign(document.createElement('span'), {className:'hero-initial', textContent:'<?php echo e($initial); ?>'}));">
      <?php else: ?>
        <span class="hero-initial"><?php echo e($initial); ?></span>
      <?php endif; ?>
    </div>

    <div class="case-file-body">
      <p class="eyebrow">Case File</p>
      <h1><?php echo e($hero['hero_name']); ?></h1>
      <p class="real-name">Real name: <?php echo e($hero['real_name']); ?></p>

      <div class="hero-card-tags case-file-tags">
        <?php if (!empty($hero['team'])): ?>
          <span class="tag"><?php echo e($hero['team']); ?></span>
        <?php endif; ?>
        <?php if (!empty($hero['gender'])): ?>
          <span class="tag"><?php echo e($hero['gender']); ?></span>
        <?php endif; ?>
        <span class="stamp <?php echo e($status_class); ?>"><?php echo e($hero['status']); ?></span>
      </div>

      <p class="case-powers">
        <strong>Powers:</strong> <?php echo e($hero['powers']); ?>
      </p>

      <section class="case-section">
        <h3>Short biography</h3>
        <!-- nl2br turns the line breaks the editor typed into real <br> tags.
             We escape FIRST and add the breaks after, never the other way round. -->
        <p><?php echo nl2br(e($hero['short_bio'])); ?></p>
      </section>

      <section class="case-section">
        <h3>Full biography</h3>
        <p><?php echo nl2br(e($hero['long_bio'])); ?></p>
      </section>

      <p class="case-meta">
        On file since <?php echo date('F j, Y', strtotime($hero['created_at'])); ?>
      </p>

      <?php if (is_logged_in()): ?>
        <div class="form-actions">
          <a href="PHP/edit_hero.php?id=<?php echo (int)$hero['hero_id']; ?>" class="btn-primary">Edit this file</a>
        </div>
      <?php endif; ?>
    </div>

  </article>

</main>

<footer class="site-footer">
  <span>Cerebro &middot; Mutant Roster Manager &middot; 2026</span>
  <span>Only authenticated staff may create, update, or delete records.</span>
</footer>

</body>
</html>