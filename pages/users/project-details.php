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
        <span class="meta-label">Status</span>
        <span class="meta-value"><?php echo htmlspecialchars($project && isset($project['status']) ? $project['status'] : 'Completed'); ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Project Type</span>
        <span class="meta-value"><?php echo htmlspecialchars($project ? $project['category'] : 'N/A'); ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Timeline (Year)</span>
        <span class="meta-value"><?php echo htmlspecialchars($project ? $project['year'] : 'N/A'); ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Built Area</span>
        <span class="meta-value"><?php echo htmlspecialchars($project && $project['sq_ft'] ? $project['sq_ft'] : 'N/A'); ?></span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Total Floor Count</span>
        <span class="meta-value"><?php echo htmlspecialchars($project && $project['floors'] ? $project['floors'] : 'N/A'); ?></span>
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
    <?php 
      $diffs = $project ? (is_string($project['difficulties']) ? json_decode($project['difficulties'], true) : []) : [];
      $sols = $project ? (is_string($project['our_solution']) ? json_decode($project['our_solution'], true) : []) : [];
      if (!is_array($diffs)) $diffs = [];
      if (!is_array($sols)) $sols = [];
      $pairCount = max(count($diffs), count($sols));
    ?>
    <?php if ($pairCount > 0): ?>
    <div class="challenges-section">
      <h2 class="challenges-section-title">Difficulties & <span>How We Overcame Them</span></h2>
      <div class="challenges-grid">
        
        <?php for ($i = 0; $i < $pairCount; $i++): ?>
          
          <?php if (!empty(trim($diffs[$i] ?? ''))): ?>
          <div class="challenge-card">
            <span class="card-badge badge-difficulty">Difficulty <?php echo $i+1; ?></span>
            <h3 class="card-title">Project Challenge</h3>
            <div class="card-text">
              <?php echo nl2br(htmlspecialchars($diffs[$i])); ?>
            </div>
          </div>
          <?php endif; ?>
          
          <?php if (!empty(trim($sols[$i] ?? ''))): ?>
          <div class="challenge-card">
            <span class="card-badge badge-solution">Our Solution</span>
            <h3 class="card-title">Strategic Execution</h3>
            <div class="card-text">
              <?php echo nl2br(htmlspecialchars($sols[$i])); ?>
            </div>
          </div>
          <?php endif; ?>

        <?php endfor; ?>

      </div>
    </div>
    <?php endif; ?>

  </section>

</main>

<?php include '../includes/footer.php'; ?>
