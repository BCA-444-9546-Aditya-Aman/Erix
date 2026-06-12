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

$pageTitle = htmlspecialchars($service['title']) . " | Erix Construction Services";
$metaDescription = $service ? substr(strip_tags($service['short_desc']), 0, 160) : "Explore our construction services at Erix Construction.";
$metaKeywords = "Erix Construction services, " . ($service ? htmlspecialchars($service['tags']) : "construction services");
$ogImage = $service && $service['image_url'] ? (strpos($service['image_url'], 'http') === 0 ? $service['image_url'] : $pathPrefix . $service['image_url']) : "";

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
              $titleFirst = explode(' ', $service['title'])[0];
              $stmt = $pdo->prepare("SELECT * FROM projects WHERE category LIKE ? ORDER BY created_at DESC LIMIT 3");
              $stmt->execute([$titleFirst . '%']);
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
                <div class="project-stat">
                  <span class="project-stat-val"><?php echo htmlspecialchars($proj['year'] ?: 'N/A'); ?></span>
                  <span class="project-stat-key">Timeline</span>
                </div>
                
                <div class="project-stat">
                  <span class="project-stat-val"><?php echo htmlspecialchars($proj['sq_ft'] ?: 'N/A'); ?></span>
                  <span class="project-stat-key">Area</span>
                </div>

                <div class="project-stat">
                  <span class="project-stat-val"><?php echo htmlspecialchars($proj['floors'] ?: 'N/A'); ?></span>
                  <span class="project-stat-key">Total Floor Count</span>
                </div>
                
                <span class="project-arrow-link">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </span>
              </div>
            </div>
          </a>
          <?php 
              endforeach; 
          else:
          ?>
          <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--cream); font-family: 'DM Sans', sans-serif;">
            No related projects found.
          </div>
          <?php endif; ?>
          
        </div>
        
        <?php if (count($relatedProjects) > 0): ?>
        <div style="text-align: center; margin-top: 40px;">
          <a href="projects.php" class="btn-primary">View More</a>
        </div>
        <?php endif; ?>

      </div>
      <div class="carousel-dots projects-dots" id="projectsDots"></div>
    </div>

  </section>

</main>

<?php include '../includes/footer.php'; ?>
