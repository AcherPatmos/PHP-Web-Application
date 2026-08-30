<?php
// manage_heroes.php is the staff dashboard. Same card grid as the public
// roster, but every card carries Edit and Delete buttons.

require_once __DIR__ . '/db.php';

// First line of every staff-only page. Not signed in means straight to login.
require_login();

// Pick up and clear any "hero was added / updated / deleted" message.
$flash = take_flash();

// The staff list can be searched too, same pattern as the public roster.
$search  = trim($_GET['q'] ?? '');
$columns = 'hero_id, hero_name, real_name, short_bio, image_url, team, status';

if ($search !== '') {
    $like = '%' . $search . '%';

    $stmt = $conn->prepare(
        "SELECT $columns
         FROM heroes
         WHERE hero_name LIKE ? OR real_name LIKE ?
         ORDER BY hero_name ASC"
    );
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT $columns FROM heroes ORDER BY hero_name ASC");
}

$total = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Heroes — Cerebro</title>

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
      <a href="manage_heroes.php" class="is-active">Manage Heroes</a>
      <a href="manage_staff.php">Staff</a>
      <span class="nav-user">Signed in as <strong><?php echo e(current_staff_name()); ?></strong></span>
      <a href="logout.php" class="btn-nav">Log Out</a>
    </nav>
  </div>
</header>

<main>

  <div class="page-heading">
    <p class="eyebrow">Staff area</p>
    <h1>Manage heroes</h1>
    <p class="subtitle">Add, update, or remove records. Changes show on the public roster immediately.</p>
  </div>

  <?php if ($flash): ?>
    <!-- Escaped here at print time. The message is stored raw in the session. -->
    <div class="flash-message"><?php echo e($flash); ?></div>
  <?php endif; ?>

  <form class="search-bar" method="get" action="manage_heroes.php" role="search">
    <label class="visually-hidden" for="q">Search heroes</label>
    <input type="text" id="q" name="q"
           placeholder="Search by hero or real name"
           value="<?php echo e($search); ?>">
    <button type="submit" class="btn-primary">Search</button>
    <?php if ($search !== ''): ?>
      <a href="manage_heroes.php" class="btn-ghost">Clear</a>
    <?php endif; ?>
  </form>

  <div class="toolbar">
    <span class="result-count"><?php echo (int)$total; ?> record<?php echo $total === 1 ? '' : 's'; ?></span>
    <a href="add_hero.php" class="btn-primary">+ Add new hero</a>
  </div>

  <div class="hero-grid">
    <?php if ($total > 0): ?>
      <?php while ($hero = $result->fetch_assoc()): ?>
        <?php
          $initial      = hero_initial($hero['hero_name']);
          $status_class = status_class($hero['status']);
          $has_image    = !empty($hero['image_url']);

          // '../' base because this page is one folder deep.
          $img_src = hero_image_src($hero['image_url'], '../');
        ?>
        <article class="hero-card">

          <div class="hero-card-media">
            <?php if ($has_image): ?>
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

          <div class="hero-card-footer">
            <div class="hero-card-tags">
              <?php if (!empty($hero['team'])): ?>
                <span class="tag"><?php echo e($hero['team']); ?></span>
              <?php endif; ?>
              <span class="stamp <?php echo e($status_class); ?>"><?php echo e($hero['status']); ?></span>
            </div>

            <div class="card-actions">
              <a href="edit_hero.php?id=<?php echo (int)$hero['hero_id']; ?>" class="btn-ghost">Edit</a>

              <!-- Delete is a POST form, not a link. A link would let a search
                   engine or a browser prefetch wipe a record just by visiting it. -->
              <form action="delete_hero.php" method="POST" class="inline-form"
                    onsubmit="return confirm('Delete <?php echo e($hero['hero_name']); ?>? This cannot be undone.');">
                <input type="hidden" name="hero_id" value="<?php echo (int)$hero['hero_id']; ?>">
                <button type="submit" class="btn-danger-card">Delete</button>
              </form>
            </div>
          </div>

        </article>
      <?php endwhile; ?>

    <?php elseif ($search !== ''): ?>
      <p class="empty-state">
        No hero matches &ldquo;<?php echo e($search); ?>&rdquo;.
        <a href="manage_heroes.php">Show all records.</a>
      </p>

    <?php else: ?>
      <p class="empty-state">No heroes on file yet. <a href="add_hero.php">Add the first one.</a></p>
    <?php endif; ?>
  </div>

</main>

<footer class="site-footer">
  <span>Cerebro &middot; Mutant Roster Manager &middot; Staff area</span>
</footer>

</body>
</html>