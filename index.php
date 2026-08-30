<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/PHP/db.php';

$logged_in = is_logged_in();

$sql = "SELECT hero_id, hero_name, real_name, short_bio, image_url, team, gender, status
        FROM heroes
        ORDER BY hero_name ASC";
$result = $conn->query($sql);

$total = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cerebro — Mutant Roster Manager</title>
<link rel="stylesheet" href="../style.css">
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
    <a href="index.php" class="active">Roster</a>
    <?php if ($logged_in): ?>
      <a href="add_hero.php">Add Hero</a>
      <span class="nav-user">Signed in as <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'staff'); ?></strong></span>
      <a href="logout.php" class="btn-login">Log Out</a>
    <?php else: ?>
      <a href="login.php" class="btn-login">Staff Login</a>
    <?php endif; ?>
  </nav>
</header>

<main>
  <div class="page-heading">
    <p class="eyebrow">Professor Xavier's Registry</p>
    <h1>Mutants on File</h1>
    <p class="lede">Browse every recorded hero. Sign in to add, update, or remove records.</p>
  </div>

  <p class="result-count"><?php echo (int)$total; ?> records on file</p>

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
                   class="hero-photo"
                   onerror="this.replaceWith(Object.assign(document.createElement('span'), {className:'hero-initial', textContent:'<?php echo htmlspecialchars($initial); ?>'}));">
            <?php else: ?>
              <span class="hero-initial"><?php echo htmlspecialchars($initial); ?></span>
            <?php endif; ?>
          </div>
          <div class="hero-card-body">
            <h3><?php echo htmlspecialchars($hero['hero_name']); ?></h3>
            <p class="real-name"><?php echo htmlspecialchars($hero['real_name']); ?></p>
            <p class="bio-snippet"><?php echo htmlspecialchars($hero['short_bio']); ?></p>
          </div>
          <div class="hero-card-footer">
            <?php if (!empty($hero['team'])): ?>
              <span class="tag"><?php echo htmlspecialchars($hero['team']); ?></span>
            <?php endif; ?>
            <span class="stamp <?php echo htmlspecialchars($status_class); ?>">
              <?php echo htmlspecialchars($hero['status']); ?>
            </span>
            <a href="hero.php?id=<?php echo (int)$hero['hero_id']; ?>" class="view-link">View &rarr;</a>
          </div>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="lede">No heroes on file yet.</p>
    <?php endif; ?>
  </div>
</main>

<footer>
  <span>Cerebro &middot; Mutant Roster Manager &middot; 2026</span>
  <span>Only authenticated staff may create, update, or delete records.</span>
</footer>

</body>
</html>