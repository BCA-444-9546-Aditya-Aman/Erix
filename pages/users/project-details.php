<?php
require_once '../admin/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project = null;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();
    } catch (\PDOException $e) {
        // Fallback
    }
}

// Redirect or show default if not found
if (!$project) {
    try {
        $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 1");
        $project = $stmt->fetch();
    } catch (\PDOException $e) {
        // Ignore
    }
}

$gallery_images = [];
if ($project) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY created_at ASC");
        $stmt->execute([$project['id']]);
        $gallery_images = $stmt->fetchAll();
    } catch (\PDOException $e) {
        // Fallback
    }
}

$pageTitle = ($project ? $project['name'] : "Project Details") . " | Erix Construction Portfolio";
$metaDescription = $project ? substr(strip_tags($project['description']), 0, 160) . "..." : "View our project details at Erix Construction.";
$metaKeywords = "Erix Construction project, " . ($project ? $project['category'] . " construction, " . $project['location'] . " builders" : "construction portfolio");
$ogImage = $project && $project['image_url'] ? (strpos($project['image_url'], 'http') === 0 ? $project['image_url'] : $pathPrefix . $project['image_url']) : "";

$pathPrefix = "../../";
$currentPage = "projects";
$navClass = "scrolled";
$navLocked = true;
include '../includes/header.php';
?>

<!-- ── MAIN PAGE CONTENT ── -->
<main class="project-details-page">

  <!-- Centered Image Carousel -->
  <section class="details-carousel-container">
    <div class="details-carousel" id="detailsCarousel">
      <div class="details-slides" id="detailsSlides">
        <?php 
          $imageSrc = $project ? $project['image_url'] : '';
          if ($imageSrc && strpos($imageSrc, 'http') !== 0) {
              $imageSrc = $pathPrefix . $imageSrc;
          }
          if (!$imageSrc) {
              $imageSrc = 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1200&q=80';
          }
        ?>
        <!-- Slide 1 (Cover Image) -->
        <div class="details-slide">
          <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo htmlspecialchars($project ? $project['name'] : 'Project'); ?> Cover" />
          <div class="details-slide-overlay"></div>
        </div>
        
        <!-- Gallery Images -->
        <?php foreach ($gallery_images as $index => $img): ?>
          <?php 
            $galSrc = $img['image_url'];
            if ($galSrc && strpos($galSrc, 'http') !== 0) {
                $galSrc = $pathPrefix . $galSrc;
            }
          ?>
          <div class="details-slide">
            <img src="<?php echo htmlspecialchars($galSrc); ?>" alt="Gallery Image <?php echo $index + 1; ?>" />
            <div class="details-slide-overlay"></div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <!-- Arrows -->
      <button class="details-arrow details-arrow-left" id="prevSlideBtn" aria-label="Previous Slide">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
      </button>
      <button class="details-arrow details-arrow-right" id="nextSlideBtn" aria-label="Next Slide">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </button>
    </div>
    
    <!-- Dots -->
    <div class="details-dots" id="detailsDots"></div>
  </section>

  <!-- Narrative & Specifications -->
  <section class="details-content-container">
    
    <!-- Header Title Section -->
    <div class="details-header">
      <div class="details-title-row">
        <div class="details-title-section">
          <div class="details-eyebrow">
            <div class="details-eyebrow-line"></div>
            <span class="details-eyebrow-text">Featured <?php echo htmlspecialchars($project ? $project['category'] : 'Construction'); ?> Landmark</span>
          </div>
          <h1 class="details-main-title">
            <?php 
              if ($project) {
                  $nameParts = explode(" ", $project['name']);
                  if (count($nameParts) > 1) {
                      $lastWord = array_pop($nameParts);
                      echo htmlspecialchars(implode(" ", $nameParts)) . " <span>" . htmlspecialchars($lastWord) . "</span>";
                  } else {
                      echo htmlspecialchars($project['name']);
                  }
              } else {
                  echo "Project <span>Details</span>";
              }
            ?>
          </h1>
        </div>
      </div>
    </div>

    <!-- Metadata Grid -->
    <div class="details-meta-grid">
      <div class="meta-item">
        <span class="meta-label">Location</span>
        <span class="meta-value"><?php echo htmlspecialchars($project ? $project['location'] : 'N/A'); ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Client</span>
        <span class="meta-value">Erix Construction Client</span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Project Type</span>
        <span class="meta-value"><?php echo htmlspecialchars($project ? $project['category'] : 'N/A'); ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Built Area</span>
        <span class="meta-value"><?php echo htmlspecialchars($project && $project['sq_ft'] ? $project['sq_ft'] : 'N/A'); ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Status</span>
        <span class="meta-value"><?php echo htmlspecialchars($project && isset($project['status']) ? $project['status'] : 'Completed'); ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Year Completed</span>
        <span class="meta-value"><?php echo htmlspecialchars($project ? $project['year'] : 'N/A'); ?></span>
      </div>
    </div>

    <!-- Narrative Description -->
    <div class="details-narrative">
      <?php 
        if ($project) {
            $desc = $project['description'];
            $paragraphs = explode("\n", $desc);
            foreach ($paragraphs as $para) {
                if (trim($para)) {
                    echo "<p>" . nl2br(htmlspecialchars(trim($para))) . "</p>";
                }
            }
        }
      ?>
    </div>

    <!-- Difficulties & Solutions Section -->
    <div class="challenges-section">
      <h2 class="challenges-section-title">Difficulties & <span>How We Overcame Them</span></h2>
      <div class="challenges-grid">
        
        <!-- Challenge 1 -->
        <div class="challenge-card">
          <span class="card-badge badge-difficulty">Difficulty</span>
          <h3 class="card-title">Congested Urban Site & Logistics Constraints</h3>
          <p class="card-text">
            The site was located in a highly dense, high-traffic commercial corridor with strict municipal constraints regarding noise levels, working hours, and heavy equipment access. Space was extremely limited, leaving virtually zero area for local material staging or storage.
          </p>
        </div>
        
        <!-- Solution 1 -->
        <div class="challenge-card">
          <span class="card-badge badge-solution">Our Solution</span>
          <h3 class="card-title">Just-in-Time Material Logistics & Night Ops</h3>
          <p class="card-text">
            We implemented a custom "Just-in-Time" (JIT) material delivery plan driven by real-time tracking software. Heavy steel trusses and structural precast components were brought in and lifted directly from trailers into place during pre-coordinated night windows, eliminating on-site storage issues completely.
          </p>
        </div>

        <!-- Challenge 2 -->
        <div class="challenge-card">
          <span class="card-badge badge-difficulty">Difficulty</span>
          <h3 class="card-title">High Water Table & Loose Subsurface Soil</h3>
          <p class="card-text">
            Excavation for the triple-level underground parking basement faced heavy groundwater infiltration due to the close proximity to coastal channels. The loose alluvial soil conditions made traditional soil-retaining methods highly unsafe.
          </p>
        </div>
        
        <!-- Solution 2 -->
        <div class="challenge-card">
          <span class="card-badge badge-solution">Our Solution</span>
          <h3 class="card-title">Secant Pile Wall & Deep Dewatering Wells</h3>
          <p class="card-text">
            Our geotechnical engineers constructed a continuous interlocking Secant Pile Wall around the site boundary to act as a watertight barrier. We then deployed a computerized deep-well dewatering system coupled with high-flow pumps to temporarily lower the water table within the excavation boundary during basement foundation concreting.
          </p>
        </div>

        <!-- Challenge 3 -->
        <div class="challenge-card">
          <span class="card-badge badge-difficulty">Difficulty</span>
          <h3 class="card-title">Heavy Monsoon Weather Disruptions</h3>
          <p class="card-text">
            Peak concrete structural pours for the upper tower columns coincided with the heavy Mumbai monsoon season, threatening structural curing quality and creating unsafe wet conditions for high-altitude exterior glazing installation.
          </p>
        </div>
        
        <!-- Solution 3 -->
        <div class="challenge-card">
          <span class="card-badge badge-solution">Our Solution</span>
          <h3 class="card-title">Monsoon Tarpaulin Cocoons & Special Concrete Mixes</h3>
          <p class="card-text">
            We built structural tarpaulin cocoons around the active casting decks to shield the pouring sites. We adjusted the concrete mix designs by using rapid-hardening admixtures and silica-fume additives to guarantee structural curing strength under constant high humidity, keeping the schedule entirely on track.
          </p>
        </div>

      </div>
    </div>

  </section>

</main>

<?php include '../includes/footer.php'; ?>
