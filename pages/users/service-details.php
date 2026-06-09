<?php
$pathPrefix = "../../";
require_once '../admin/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$service = null;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch();
    } catch (\PDOException $e) {
        $service = null;
    }
}

if (!$service) {
    header("Location: services.php");
    exit;
}

$pageTitle = htmlspecialchars($service['title']) . " - Erix Construction";
$currentPage = "services";
$navClass = "scrolled";
$navLocked = true;
include '../includes/header.php';

// Split title for styling
$titleParts = explode(' ', $service['title'], 2);
$titleFirst = $titleParts[0];
$titleRest = isset($titleParts[1]) ? $titleParts[1] : '';
?>

<!-- ── MAIN PAGE CONTENT ── -->
<main class="service-details-page">

  <!-- Solid-Color Theme Hero Section -->
  <section class="service-hero">
    <div class="service-hero-content">
      <div class="service-hero-eyebrow">
        <div class="service-hero-eyebrow-line"></div>
        <span class="service-hero-eyebrow-text">Core Building Expertise</span>
      </div>
      <h1 class="service-hero-title"><?php echo htmlspecialchars($titleFirst); ?> <?php if($titleRest): ?><span><?php echo htmlspecialchars($titleRest); ?></span><?php endif; ?></h1>
    </div>
  </section>

  <!-- Narrative & Emotional Storyline -->
  <section class="service-details-content-container">
    <div class="service-story-split">
      
      <!-- Narrative Details (Left) -->
      <div class="service-narrative">
        <?php 
          $paragraphs = explode("\n", trim($service['full_desc']));
          foreach ($paragraphs as $p) {
              if (trim($p) !== '') {
                  echo "<p>" . htmlspecialchars(trim($p)) . "</p>";
              }
          }
        ?>
      </div>

      <!-- Emotional Storyline Box (Right) -->
      <?php if (!empty($service['philosophy'])): ?>
      <div class="service-story-box">
        <h3 class="service-story-title">Our Philosophy</h3>
        <p class="service-story-text">
          "<?php echo htmlspecialchars(trim($service['philosophy'])); ?>"
        </p>
      </div>
      <?php endif; ?>

    </div>

    <!-- Featured Projects Grid (Below) -->
    <div class="service-projects-section">
      <h2 class="service-projects-title">Featured <span>Related Projects</span></h2>
      
      <div class="projects-carousel-wrapper">
        <div class="projects-grid">
          
          <?php
          try {
              $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 3");
              $relatedProjects = $stmt->fetchAll();
          } catch (\PDOException $e) {
              $relatedProjects = [];
          }
          
          if (count($relatedProjects) > 0):
              foreach ($relatedProjects as $proj):
                  $catLower = strtolower($proj['category']);
                  $imageSrc = $proj['image_url'];
                  if ($imageSrc && strpos($imageSrc, 'http') !== 0) {
                      $imageSrc = $pathPrefix . $imageSrc;
                  }
                  $s1val = $proj['floors']; $s1key = 'Floors';
                  if ($s1val && preg_match('/^([\d\.★\w\+]+)\s+(.+)$/', $s1val, $m)) { $s1val = $m[1]; $s1key = $m[2]; }
                  $s3val = $proj['sq_ft']; $s3key = 'Sq. Ft.';
                  if ($s3val && preg_match('/^([\d\.★\w\+]+)\s+(.+)$/', $s3val, $m)) { $s3val = $m[1]; $s3key = $m[2]; }
                  $s2val = $proj['units']; $s2key = 'Units';
                  if ($s2val && preg_match('/^([\d\.★\w\+]+)\s+(.+)$/', $s2val, $m)) { $s2val = $m[1]; $s2key = $m[2]; }
                  $excerpt = $proj['description'];
                  if (strlen($excerpt) > 120) $excerpt = substr($excerpt, 0, 117) . '...';
          ?>
          <a href="project-details.php?id=<?php echo $proj['id']; ?>" class="project-card <?php echo htmlspecialchars($catLower); ?>" data-category="<?php echo htmlspecialchars($catLower); ?>">
            <div class="project-border-glow"></div>
            <div class="project-img">
              <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo htmlspecialchars($proj['name']); ?>" loading="lazy"/>
              <div class="project-img-overlay">
                <span class="project-view-btn">View Project</span>
              </div>
              <span class="project-category-tag"><?php echo htmlspecialchars($proj['category']); ?></span>
            </div>
            <div class="project-body">
              <div class="project-meta">
                <span class="project-location"><?php echo htmlspecialchars($proj['location']); ?></span>
                <span class="project-year"><?php echo htmlspecialchars($proj['year']); ?></span>
              </div>
              <h3 class="project-name"><?php echo htmlspecialchars($proj['name']); ?></h3>
              <p class="project-desc"><?php echo htmlspecialchars($excerpt); ?></p>
              <div class="project-footer">
                <?php if ($s1val): ?>
                <div class="project-stat">
                  <span class="project-stat-val"><?php echo htmlspecialchars($s1val); ?></span>
                  <span class="project-stat-key"><?php echo htmlspecialchars($s1key); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($s3val): ?>
                <div class="project-stat">
                  <span class="project-stat-val"><?php echo htmlspecialchars($s3val); ?></span>
                  <span class="project-stat-key"><?php echo htmlspecialchars($s3key); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($s2val): ?>
                <div class="project-stat">
                  <span class="project-stat-val"><?php echo htmlspecialchars($s2val); ?></span>
                  <span class="project-stat-key"><?php echo htmlspecialchars($s2key); ?></span>
                </div>
                <?php endif; ?>
                <span class="project-arrow-link">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </span>
              </div>
            </div>
          </a>
          <?php 
              endforeach; 
          endif; 
          ?>
          
        </div>
      </div>
      <div class="carousel-dots projects-dots" id="projectsDots"></div>
    </div>

  </section>

</main>

<?php include '../includes/footer.php'; ?>
