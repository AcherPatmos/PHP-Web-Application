<?php
// index.php - the public home page. Anyone can browse the roster from here.
require_once __DIR__ . '/db.php';

// Tells the nav which link to mark as the current page.
$currentPage = 'index.php';

// Whatever the visitor typed in the search box.
// Empty string when they have not searched for anything.
$search = trim($_GET['q'] ?? '');

// The columns the roster grid needs, written once so both queries stay in sync.
$columns = 'hero_id, hero_name, real_name, short_bio, image_url, team, gender, status';

if ($search !== '') {
    // The % wildcards go into the PHP variable, not into the SQL text.
    // That keeps this a real prepared statement and prevents sql injection
    $like = '%' . $search . '%';

    $stmt = $conn->prepare(
        "SELECT $columns
         FROM heroes
         WHERE hero_name LIKE ? OR real_name LIKE ?
         ORDER BY hero_name ASC"
    );

    // 'ss' means both placeholders are strings.
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // No search term, so just list everyone.
    $result = $conn->query("SELECT $columns FROM heroes ORDER BY hero_name ASC");
}

$total = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cerebro — Mutant Roster</title>

<!-- Google Fonts -->
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
      <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'is-active' : ''; ?>">Roster</a>

      <?php if (is_logged_in()): ?>
        <!-- Signed in: show the staff tools -->
        <a href="PHP/manage_heroes.php">Manage Heroes</a>
        <a href="PHP/manage_staff.php">Staff</a>
        <span class="nav-user">Signed in as <strong><?php echo e(current_staff_name()); ?></strong></span>
        <a href="PHP/logout.php" class="btn-nav">Log Out</a>
      <?php else: ?>
        <!-- Signed out: only offer the way in -->
        <a href="PHP/login.php" class="btn-nav">Staff Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>

  <div class="page-heading">
    <p class="eyebrow">Professor Xavier's Registry</p>
    <h1>Heroes on File</h1>
    <p class="subtitle">
      Browse every recorded operative.
      <?php echo is_logged_in()
        ? 'You are signed in and may create, edit, or remove records.'
        : 'Sign in to create, edit, or remove records.'; ?>
    </p>
  </div>

  <!-- Search box. method="get" so the search term stays in the URL and
       the page can be bookmarked or refreshed. -->
  <form class="search-bar" method="get" action="index.php" role="search">
    <label class="visually-hidden" for="q">Search heroes</label>
    <input type="text" id="q" name="q"
           placeholder="Search by hero or real name"
           value="<?php echo e($search); ?>">
    <button type="submit" class="btn-primary">Search</button>
    <?php if ($search !== ''): ?>
      <!-- Clear only appears when there is something to clear -->
      <a href="index.php" class="btn-ghost">Clear</a>
    <?php endif; ?>
  </form>

  <p class="result-count">
    <?php if ($search !== ''): ?>
      <?php echo (int)$total; ?> match<?php echo $total === 1 ? '' : 'es'; ?>
      for &ldquo;<?php echo e($search); ?>&rdquo;
    <?php else: ?>
      <?php echo (int)$total; ?> records on file
    <?php endif; ?>
  </p>

  <div class="hero-grid">
    <?php if ($total > 0): ?>
      <?php while ($hero = $result->fetch_assoc()): ?>
        <?php
          // Worked out once per card so the markup below stays readable.
          $initial      = hero_initial($hero['hero_name']);
          $status_class = status_class($hero['status']);
          $has_image    = !empty($hero['image_url']);

          // '' base because this page sits in the project root.
          $img_src = hero_image_src($hero['image_url'], '');
        ?>
        <article class="hero-card">

          <!-- The whole top of the card is one link to the detail page -->
          <a href="hero.php?id=<?php echo (int)$hero['hero_id']; ?>" class="hero-card-link">

            <div class="hero-card-media">
              <?php if ($has_image): ?>
                <!-- onerror swaps a missing photo for the letter tile,
                     so a broken path never shows a torn-image icon -->
                <img src="<?php echo e($img_src); ?>"
                     alt="<?php echo e($hero['hero_name']); ?>"
                     class="hero-photo"
                     loading="lazy"
                     onerror="this.replaceWith(Object.assign(document.createElement('span'), {className:'hero-initial', textContent:'<?php echo e($initial); ?>'}));">
              <?php else: ?>
                <span class="hero-initial"><?php echo e($initial); ?></span>
              <?php endif; ?>
            </div>

            <div class="hero-card-body">
              <h2><?php echo e($hero['hero_name']); ?></h2>
              <p class="real-name"><?php echo e($hero['real_name']); ?></p>
              <p class="bio-snippet"><?php echo e($hero['short_bio']); ?></p>
            </div>

          </a>

          <div class="hero-card-footer">
            <div class="hero-card-tags">
              <?php if (!empty($hero['team'])): ?>
                <span class="tag"><?php echo e($hero['team']); ?></span>
              <?php endif; ?>
              <span class="stamp <?php echo e($status_class); ?>"><?php echo e($hero['status']); ?></span>
            </div>

            <a href="hero.php?id=<?php echo (int)$hero['hero_id']; ?>" class="view-link">View &rarr;</a>
          </div>

        </article>
      <?php endwhile; ?>

    <?php elseif ($search !== ''): ?>
      <!-- Searched, found nothing -->
      <p class="empty-state">
        No hero matches &ldquo;<?php echo e($search); ?>&rdquo;.
        <a href="index.php">Show the full roster.</a>
      </p>

    <?php else: ?>
      <!-- if Database is genuinely empty -->
      <p class="empty-state">
        No heroes on file yet.
        <?php if (is_logged_in()): ?>
          <a href="PHP/add_hero.php">Add the first one.</a>
        <?php else: ?>
          <a href="PHP/login.php">Sign in to add the first one.</a>
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </div>

</main>

<footer class="site-footer">
  <span>Cerebro &middot; Mutant Roster Manager &middot; 2026</span>
  <span>Only authenticated staff may create, update, or delete records.</span>
</footer>

</body>
</html>