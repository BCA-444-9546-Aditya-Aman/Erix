<?php
$pageTitle = "Showcase Projects & Portfolio | Erix Construction";
$metaDescription = "View the Erix Construction portfolio. Discover our monumental commercial skyscrapers, luxury villas, and bespoke architectural projects.";
$metaKeywords = "construction portfolio, showcase projects, completed buildings, Erix projects, commercial skyscrapers, luxury villas";
$pathPrefix = "../../";
$currentPage = "projects";
$navClass = "scrolled";
$navLocked = true;
include '../includes/header.php';

require_once '../admin/db.php';
try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
} catch (\PDOException $e) {
    $projects = [];
}
?>

<!-- ── PROJECTS HERO ── -->
<section class="projects-hero">
  <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=1600&q=80" alt="Projects Hero Background" class="projects-hero-bg"/>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-eyebrow">
      <div class="eyebrow-line"></div>
      <span class="eyebrow-text">OUR PORTFOLIO</span>
    </div>
    <h1 class="projects-hero-title">FEATURED <span>WORK</span></h1>
    <p class="hero-tagline">Explore how we convert blueprints into modern, luxury architectural landmarks with absolute precision.</p>
  </div>
</section>

<!-- ── FILTER BAR ── -->
<section class="filter-section">
  <div class="filter-container">
    <span class="filter-label">Filters:</span>
    <div class="filter-controls">
      <div class="custom-select-wrapper">
        <select id="projectCategoryFilter" class="filter-dropdown">
          <option value="all">All Categories</option>
          <option value="residential">Residential</option>
          <option value="commercial">Commercial</option>
          <option value="renovation">Renovation</option>
          <option value="interior">Interior</option>
        </select>
        <!-- Custom Down Arrow -->
        <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <div class="custom-select-wrapper">
        <select id="projectStatusFilter" class="filter-dropdown">
          <option value="all">All Statuses</option>
          <option value="upcoming">Upcoming</option>
          <option value="ongoing">Ongoing</option>
          <option value="completed">Completed</option>
        </select>
        <!-- Custom Down Arrow -->
        <svg class="select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
      </div>
      <button id="clearFilterBtn" class="clear-filter-link">
        Clear Filters
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"></polyline></svg>
      </button>
    </div>
  </div>
</section>

<!-- ── PROJECTS PAGE GRID ── -->
<section class="projects-page-content">
  <div class="projects-carousel-wrapper">
    <div class="projects-grid">

      <?php if (count($projects) > 0): ?>
        <?php foreach ($projects as $proj): ?>
          <?php 
            $catLower = strtolower($proj['category']);
            $statLower = strtolower($proj['status'] ?? 'completed');
            // Determine image URL
            $imageSrc = $proj['image_url'];
            if (strpos($imageSrc, 'http') !== 0) {
                $imageSrc = $pathPrefix . $imageSrc;
            }
          ?>
          <a href="project-details.php?id=<?php echo $proj['id']; ?>" class="project-card <?php echo $catLower; ?>" data-category="<?php echo $catLower; ?>" data-status="<?php echo $statLower; ?>">
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
              <p class="project-desc">
                <?php 
                  $excerpt = $proj['description'];
                  if (strlen($excerpt) > 120) {
                      $excerpt = substr($excerpt, 0, 117) . '...';
                  }
                  echo htmlspecialchars($excerpt);
                ?>
              </p>
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
        <?php endforeach; ?>
      <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--cream); font-family: 'DM Sans', sans-serif;">
          No projects available at this time.
        </div>
      <?php endif; ?>

    </div>
  </div>
  <div class="carousel-dots projects-dots" id="projectsDots"></div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const catFilter = document.getElementById('projectCategoryFilter');
    const statFilter = document.getElementById('projectStatusFilter');
    const clearBtn = document.getElementById('clearFilterBtn');
    const cards = document.querySelectorAll('.project-card');

    function filterProjects() {
      const catVal = catFilter.value;
      const statVal = statFilter.value;

      cards.forEach(card => {
        const matchCat = (catVal === 'all' || card.dataset.category === catVal);
        const matchStat = (statVal === 'all' || card.dataset.status === statVal);
        
        if (matchCat && matchStat) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }

    if (catFilter) catFilter.addEventListener('change', filterProjects);
    if (statFilter) statFilter.addEventListener('change', filterProjects);

    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        if(catFilter) catFilter.value = 'all';
        if(statFilter) statFilter.value = 'all';
        filterProjects();
      });
    }
  });
</script>

<?php include '../includes/footer.php'; ?>
