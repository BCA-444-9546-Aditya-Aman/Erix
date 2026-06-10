<?php
$pageTitle = "Our Construction Services | Residential & Commercial | Erix Construction";
$metaDescription = "Explore our comprehensive construction services including turnkey residential development, commercial spaces, interior fit-outs, and heritage renovations.";
$metaKeywords = "construction services, turnkey residential development, commercial construction, interior fit-outs, heritage renovations, Erix Construction services";
$pathPrefix = "../../";
$currentPage = "services";
$navClass = "scrolled";
$navLocked = true;
require_once '../admin/db.php';
include '../includes/header.php';

// Fetch all services
try {
    $stmt = $pdo->query("SELECT * FROM services ORDER BY created_at ASC");
    $servicesList = $stmt->fetchAll();
} catch (\PDOException $e) {
    $servicesList = [];
}
?>

<!-- ── MAIN PAGE CONTENT ── -->
<main class="services-page">

  <!-- Solid-Color Theme Hero Section -->
  <section class="service-hero">
    <div class="service-hero-content">
      <div class="service-hero-eyebrow">
        <div class="service-hero-eyebrow-line"></div>
        <span class="service-hero-eyebrow-text">Our Building Expertise</span>
      </div>
      <h1 class="service-hero-title">Core <span>Services</span></h1>
    </div>
  </section>

  <!-- Services Grid Section -->
  <section class="services" id="services" style="padding: 80px 5%;">
    <div class="services-carousel-wrapper">
      <div class="services-grid">

        <?php if (count($servicesList) > 0): ?>
          <?php foreach ($servicesList as $index => $srv): ?>
            <?php 
              $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
              $imgSrc = $srv['image_url'];
              if ($imgSrc && strpos($imgSrc, 'http') !== 0) {
                  $imgSrc = $pathPrefix . $imgSrc;
              }
              if (!$imgSrc) $imgSrc = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80'; // Fallback
            ?>
            <a href="service-details.php?id=<?php echo $srv['id']; ?>" class="service-card">
              <div class="service-bg">
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($srv['title']); ?>" loading="lazy"/>
                <div class="service-bg-overlay"></div>
              </div>
              <div class="service-arrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </div>
              <div class="service-card-inner">
                <span class="service-number"><?php echo $num; ?></span>
                <div class="service-icon">
                  <?php if (!empty($srv['icon_svg'])): ?>
                    <?php echo $srv['icon_svg']; ?>
                  <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  <?php endif; ?>
                </div>
                <h3 class="service-name"><?php echo str_replace(' ', '<br/>', htmlspecialchars($srv['title'])); ?></h3>
                <p class="service-desc"><?php echo htmlspecialchars($srv['short_desc']); ?></p>
              </div>
              <span class="service-tag"><?php echo htmlspecialchars($srv['tags']); ?></span>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color: rgba(245,245,240,0.6); font-size: 16px;">Services are currently being updated. Please check back later.</p>
        <?php endif; ?>

      </div>
    </div>
    <div class="carousel-dots services-dots" id="servicesDots"></div>
  </section>

</main>

<?php include '../includes/footer.php'; ?>
