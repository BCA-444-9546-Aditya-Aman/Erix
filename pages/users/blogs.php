<?php
$pageTitle = "Our Blogs - Erix Construction";
$pathPrefix = "../../";
$currentPage = "blogs";
$navClass = "scrolled";
$navLocked = true;
include '../includes/header.php';

require_once '../admin/db.php';
try {
    $stmt = $pdo->query("SELECT * FROM blogs ORDER BY date_published DESC");
    $blogs = $stmt->fetchAll();
} catch (\PDOException $e) {
    $blogs = [];
}
?>

<!-- ── MAIN PAGE CONTENT ── -->
<main class="blogs-page">

  <!-- Warm Sand Gradient Hero Section -->
  <section class="blogs-hero">
    <div class="blogs-hero-content">
      <div class="blogs-hero-eyebrow">
        <div class="blogs-hero-eyebrow-line"></div>
        <span class="blogs-hero-eyebrow-text">Insights & Perspectives</span>
      </div>
      <h1 class="blogs-hero-title">OUR <span>BLOGS</span></h1>
    </div>
  </section>

  <!-- Blogs List Container (Centered Padding) -->
  <section class="blogs-list-container">

    <?php if (count($blogs) > 0): ?>
      <?php foreach ($blogs as $post): ?>
        <?php 
          $dateFormatted = date('F d, Y', strtotime($post['date_published']));
          $imageSrc = $post['image_url'];
          if ($imageSrc && strpos($imageSrc, 'http') !== 0) {
              $imageSrc = $pathPrefix . $imageSrc;
          }
          if (!$imageSrc) {
              $imageSrc = 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80';
          }
        ?>
        <a href="blog-details.php?id=<?php echo $post['id']; ?>" class="blog-row-card">
          <div class="blog-card-img-wrapper">
            <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy"/>
          </div>
          <div class="blog-card-content">
            <div class="blog-card-meta">
              <span><?php echo htmlspecialchars($dateFormatted); ?></span>
              <span>·</span>
              <span>By <?php echo htmlspecialchars($post['author']); ?></span>
            </div>
            <h3 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
            <p class="blog-card-desc"><?php echo htmlspecialchars($post['summary']); ?></p>
          </div>
          <div class="blog-card-action">
            <span class="btn-read-more">
              Read More
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </span>
          </div>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="text-align: center; padding: 40px; color: var(--cream); font-family: 'DM Sans', sans-serif; grid-column: 1/-1;">
        No blog posts published at this time.
      </div>
    <?php endif; ?>

  </section>

</main>

<?php include '../includes/footer.php'; ?>
