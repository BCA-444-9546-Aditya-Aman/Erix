<?php
$pageTitle = "Erix Construction - Engineering Excellence";
$pathPrefix = "./";
$currentPage = "home";
include 'pages/includes/header.php';

// Load projects from database for the showcase carousel
require_once 'pages/admin/db.php';
try {
    $featuredProjects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 6")->fetchAll();
} catch (\PDOException $e) {
    $featuredProjects = [];
}

// Load featured services
try {
    $featuredServices = $pdo->query("SELECT * FROM services WHERE is_featured = 1 ORDER BY created_at ASC LIMIT 4")->fetchAll();
} catch (\PDOException $e) {
    $featuredServices = [];
}
?>


<!-- ── HERO ── -->
<section class="hero" id="home">

  <video class="hero-video" autoplay muted loop playsinline>
    <source src="./assets/videos/hero_bg.mp4" type="video/mp4"/>
  </video>

  <div class="hero-overlay"></div>

  <div class="hero-content">
    <div class="hero-eyebrow">
      <div class="eyebrow-line"></div>
      <span class="eyebrow-text">Est. 2010 &nbsp;·&nbsp; Premium Construction</span>
    </div>

    <h1 class="hero-title">
      Engineering Excellence,
      <span>Constructing the Future</span>
    </h1>

    <p class="hero-tagline">
      Transforming ideas into structures that last.
    </p>

    <div class="hero-actions">
      <a href="./pages/users/projects.php" class="btn-primary">View Our Work</a>
      <a href="#contact"  class="btn-ghost">Get a Quote</a>
    </div>
  </div>

  <!-- Stats Bar -->
  <div class="hero-stats">
    <div class="stat-item">
      <span class="stat-number">15+</span>
      <span class="stat-label">Years of Experience</span>
    </div>
    <div class="stat-item">
      <span class="stat-number">320+</span>
      <span class="stat-label">Projects Completed</span>
    </div>
    <div class="stat-item">
      <span class="stat-number">180+</span>
      <span class="stat-label">Happy Clients</span>
    </div>
    <div class="stat-item">
      <span class="stat-number">12</span>
      <span class="stat-label">Cities Covered</span>
    </div>
  </div>

  <!-- Scroll Hint -->
  <div class="scroll-hint">
    <span>Scroll</span>
    <div class="scroll-line"></div>
  </div>

</section>


<!-- ── SERVICES SECTION ── -->
<section class="services" id="services">

  <div class="services-header">
    <div class="services-header-left">
      <div class="section-eyebrow">
        <div class="eyebrow-line"></div>
        <span class="eyebrow-text">What We Do</span>
      </div>
      <h2 class="section-title">Our Core<br/><span>Services</span></h2>
      <p class="services-subtitle">From the ground up — we deliver end-to-end construction solutions built around your needs, your timeline, and your vision.</p>
    </div>
    <a href="#contact" class="services-cta-link">
      Start a Project
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>

  <div class="services-carousel-wrapper">
    <div class="services-grid">

    <?php if (count($featuredServices) > 0): ?>
      <?php foreach ($featuredServices as $index => $srv): ?>
        <?php 
          $num = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
          $imgSrc = $srv['image_url'];
          if ($imgSrc && strpos($imgSrc, 'http') !== 0) {
              $imgSrc = $pathPrefix . $imgSrc;
          }
          if (!$imgSrc) $imgSrc = 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80'; // Fallback
        ?>
        <a href="./pages/users/service-details.php?id=<?php echo $srv['id']; ?>" class="service-card">
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
      <p style="color: rgba(245,245,240,0.6); font-size: 16px;">Services are currently being updated.</p>
    <?php endif; ?>
  </div>
  <div class="carousel-dots services-dots" id="servicesDots"></div>
  <div class="services-bottom">
    <a href="./pages/users/services.php" class="btn-outline-gold">
      View More
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>
</section>


<!-- ── PROJECTS SECTION ── -->
<section class="projects" id="projects">

  <div class="projects-header">
    <div>
      <div class="section-eyebrow">
        <div class="eyebrow-line"></div>
        <span class="eyebrow-text" style="color:var(--cream);">Featured Work</span>
      </div>
      <h2 class="section-title">Projects That<br/><span>Define Us</span></h2>
    </div>
    <span class="projects-count">320+</span>
  </div>

  <div class="projects-carousel-wrapper">
    <div class="projects-grid">

    <?php if (count($featuredProjects) > 0): ?>
      <?php foreach ($featuredProjects as $proj): ?>
        <?php
          $catLower = strtolower($proj['category']);
          $imageSrc = $proj['image_url'];
          if (strpos($imageSrc, 'http') !== 0) {
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
        <a href="./pages/users/project-details.php?id=<?php echo $proj['id']; ?>" class="project-card <?php echo htmlspecialchars($catLower); ?>" data-category="<?php echo htmlspecialchars($catLower); ?>">
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
      <?php endforeach; ?>
    <?php else: ?>
      <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--cream);font-family:'DM Sans',sans-serif;">
        No projects available at this time.
      </div>
    <?php endif; ?>
  </div>
  <div class="carousel-dots projects-dots" id="projectsDots"></div>

  <div class="projects-bottom">
    <a href="./pages/users/projects.php" class="btn-outline-gold">
      View More
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>

</section>

<!-- ── VIDEO STORY SECTION ── -->
<section class="video-story">
  <video id="storyVideo" class="video-story-bg" autoplay muted loop playsinline>
    <source src="./assets/videos/story.mp4" type="video/mp4"/>
    Your browser does not support the video tag.
  </video>
  <button id="fullscreenToggle" class="fullscreen-toggle-btn" aria-label="Full View">
    <svg class="fullscreen-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
    </svg>
  </button>
  <button id="soundToggle" class="sound-toggle-btn" aria-label="Toggle Sound">
    <!-- Icon Off (Muted by default) -->
    <svg class="sound-icon sound-icon-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
      <line x1="23" y1="9" x2="17" y2="15"></line>
      <line x1="17" y1="9" x2="23" y2="15"></line>
    </svg>
    <!-- Icon On (Hidden by default) -->
    <svg class="sound-icon sound-icon-on" style="display: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
      <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
    </svg>
  </button>
</section>

<section class="testimonials" id="testimonials">
  <!-- Trust Badge (Top Left Corner) -->
  <div class="trust-badge">
    <div class="avatar-stack">
      <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Client 1" class="trust-avatar"/>
      <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Client 2" class="trust-avatar"/>
      <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="Client 3" class="trust-avatar"/>
      <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=100&q=80" alt="Client 4" class="trust-avatar"/>
      <div class="trust-avatar-more">+180</div>
    </div>
    <div class="trust-text">
      <span class="trust-label">Trusted by</span>
      <span class="trust-val">world leading companies</span>
    </div>
  </div>

  <div class="testimonials-inner">
    <div class="section-eyebrow">
      <div class="eyebrow-line"></div>
      <span class="eyebrow-text" style="color:var(--cream);">Client Words</span>
      <div class="eyebrow-line"></div>
    </div>
    <h2 class="section-title">What Our<br/><span>Clients Say</span></h2>

    <div class="testi-slider" id="testiSlider">

      <div class="testi-slide active">
        <div class="testi-stars">
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <p class="testi-quote">Erix transformed our vision into a landmark. The attention to detail, the quality of finishes, and above all the communication throughout — it was truly a world-class experience. Our Skyline tower stands as proof of what they can deliver.</p>
        <div class="testi-author">
          <div class="testi-avatar">RK</div>
          <span class="testi-name">Rajiv Kapoor</span>
          <span class="testi-role">Director, Kapoor Developers — Mumbai</span>
        </div>
      </div>

      <div class="testi-slide">
        <div class="testi-stars">
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <p class="testi-quote">We handed Erix a complex renovation brief on our 100-year-old heritage hotel and they delivered beyond expectations. They preserved the soul of the building while bringing it to modern luxury standards. On budget, ahead of schedule.</p>
        <div class="testi-author">
          <div class="testi-avatar">SM</div>
          <span class="testi-name">Sunita Mehta</span>
          <span class="testi-role">General Manager, Heritage Grand — Delhi</span>
        </div>
      </div>

      <div class="testi-slide">
        <div class="testi-stars">
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <p class="testi-quote">The Nexus Corporate Park is now the crown jewel of our real estate portfolio. Erix brought structure, transparency, and craft to every phase. Their project management team is simply the best we have ever worked with.</p>
        <div class="testi-author">
          <div class="testi-avatar">AT</div>
          <span class="testi-name">Arjun Tiwari</span>
          <span class="testi-role">CEO, Tiwari Estates — Pune</span>
        </div>
      </div>

      <div class="testi-slide">
        <div class="testi-stars">
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        </div>
        <p class="testi-quote">Our penthouse interior fit-out was handled with extraordinary care. Every custom piece was crafted to perfection and the team respected our home throughout the process. We could not be happier with the result.</p>
        <div class="testi-author">
          <div class="testi-avatar">PR</div>
          <span class="testi-name">Priya Reddy</span>
          <span class="testi-role">Homeowner — Bangalore</span>
        </div>
      </div>

    </div>

    <div class="testi-controls">
      <button class="testi-btn" id="testiPrev" aria-label="Previous">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      </button>
      <div class="testi-dots" id="testiDots">
        <button class="testi-dot active" data-i="0"></button>
        <button class="testi-dot" data-i="1"></button>
        <button class="testi-dot" data-i="2"></button>
        <button class="testi-dot" data-i="3"></button>
      </div>
      <button class="testi-btn" id="testiNext" aria-label="Next">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>
</section>


<!-- ── ABOUT SECTION ── -->
<section class="about" id="about">

  <div class="about-inner">

    <!-- LEFT — Image stack -->
    <div class="about-visual">
      <div class="about-img-main">
        <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80" alt="Construction site" loading="lazy"/>
        <div class="about-img-badge">
          <span class="badge-num">15+</span>
          <span class="badge-label">Years Building Excellence</span>
        </div>
      </div>
      <div class="about-img-secondary">
        <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=400&q=80" alt="Team at work" loading="lazy"/>
      </div>
      <div class="about-corner-accent"></div>
    </div>

    <!-- RIGHT — Content -->
    <div class="about-content">

      <div class="section-eyebrow">
        <div class="eyebrow-line"></div>
        <span class="eyebrow-text">Who We Are</span>
      </div>

      <h2 class="section-title">Built on Trust.<br/><span>Driven by Vision.</span></h2>

      <p class="about-body">
        Erix Construction was founded with a singular belief — that every structure we raise should stand as a testament to precision, integrity, and craftsmanship. From residential landmarks to large-scale commercial developments, we bring the same unwavering commitment to every project we undertake.
      </p>

      <p class="about-body">
        Our team of seasoned engineers, architects, and project managers work in close collaboration with clients to turn ambitious blueprints into enduring realities — on time, within budget, and beyond expectations.
      </p>

      <!-- Mission & Values -->
      <div class="values-carousel-wrapper">
        <div class="values-grid">
          <div class="value-card">
            <div class="value-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h4>Integrity First</h4>
            <p>Transparent processes, honest timelines, zero compromise on quality.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h4>Precision Driven</h4>
            <p>Every detail engineered to exact specification, from foundation to finish.</p>
          </div>
          <div class="value-card">
            <div class="value-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h4>Client-Centered</h4>
            <p>Your vision shapes every decision we make throughout the project lifecycle.</p>
          </div>
        </div>
      </div>
      <div class="carousel-dots values-dots" id="valuesDots"></div>

      <!-- Milestones -->
      <div class="milestones">
        <div class="milestone">
          <span class="milestone-year">2010</span>
          <span class="milestone-event">Founded in Mumbai with a team of 8</span>
        </div>
        <div class="milestone">
          <span class="milestone-year">2015</span>
          <span class="milestone-event">Expanded to commercial & industrial projects</span>
        </div>
        <div class="milestone">
          <span class="milestone-year">2019</span>
          <span class="milestone-event">ISO 9001:2015 Certified · 100+ projects milestone</span>
        </div>
        <div class="milestone">
          <span class="milestone-year">2024</span>
          <span class="milestone-event">Best Construction Firm — National Build Awards</span>
        </div>
      </div>

      <!-- Awards row -->
      <div class="awards-row">
        <div class="award-chip">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
          ISO 9001:2015
        </div>
        <div class="award-chip">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
          National Build Award 2024
        </div>
        <div class="award-chip">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          CREDAI Member
        </div>
      </div>

    </div>
  </div>
</section>


<!-- ── CONTACT SECTION ── -->
<section class="contact" id="contact">
  <div class="contact-inner">

    <div class="contact-left">
      <div class="section-eyebrow">
        <div class="eyebrow-line"></div>
        <span class="eyebrow-text" style="color:var(--cream);">Get In Touch</span>
      </div>
      <h2 class="section-title">Start Your<br/><span>Project Today</span></h2>
      <p class="contact-desc">Have a project in mind? Fill in the form and our team will get back to you within 24 hours to discuss your vision, timeline, and budget.</p>

      <div class="contact-info-list">
        <div class="contact-info-item">
          <div class="contact-info-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
          </div>
          <div>
            <span class="contact-info-label">Phone</span>
            <span class="contact-info-val">+91 98765 43210</span>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="contact-info-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <div>
            <span class="contact-info-label">Email</span>
            <span class="contact-info-val">info@erixconstruction.com</span>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="contact-info-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          </div>
          <div>
            <span class="contact-info-label">Office</span>
            <span class="contact-info-val">Level 12, Apex Tower, BKC, Mumbai — 400051</span>
          </div>
        </div>
      </div>
    </div>

    <div class="contact-form-wrap">
      <form id="contactForm" novalidate>
        <div class="form-row">
          <div class="form-group">
            <label for="fname">Full Name</label>
            <input type="text" id="fname" name="from_name" placeholder="John Doe" required/>
          </div>
          <div class="form-group">
            <label for="fphone">Phone Number</label>
            <input type="tel" id="fphone" name="phone" placeholder="+91 00000 00000" required/>
          </div>
        </div>
        <div class="form-group">
          <label for="femail">Email Address</label>
          <input type="email" id="femail" name="from_email" placeholder="john@example.com" required/>
        </div>
        <div class="form-group">
          <label for="fservice">Service Required</label>
          <select id="fservice" name="service" required>
            <option value="" disabled selected>Select a service…</option>
            <option>Residential Construction</option>
            <option>Commercial Construction</option>
            <option>Renovation & Remodeling</option>
            <option>Interior Fit-Out</option>
            <option>Other / Not Sure Yet</option>
          </select>
        </div>
        <div class="form-group">
          <label for="fmessage">Your Message</label>
          <textarea id="fmessage" name="message" placeholder="Tell us about your project — location, size, timeline…" required></textarea>
        </div>
        <button type="submit" class="form-submit" id="formSubmit">
          Send Message
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
        </button>
        <p class="form-status" id="formStatus"></p>
      </form>
    </div>
  </div>
</section>

<?php include 'pages/includes/footer.php'; ?>