<?php
require_once '../admin/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch();
    } catch (\PDOException $e) {
        // Fallback
    }
}

// Fallback to latest blog if not found
if (!$post) {
    try {
        $stmt = $pdo->query("SELECT * FROM blogs ORDER BY date_published DESC LIMIT 1");
        $post = $stmt->fetch();
    } catch (\PDOException $e) {
        // Ignore
    }
}

// Fetch 5 random blogs for the sidebar
$recentBlogs = [];
try {
    if ($post) {
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id != ? ORDER BY RAND() LIMIT 5");
        $stmt->execute([$post['id']]);
    } else {
        $stmt = $pdo->query("SELECT * FROM blogs ORDER BY RAND() LIMIT 5");
    }
    $recentBlogs = $stmt->fetchAll();
} catch (\PDOException $e) {
    // Ignore
}

if (!$post) {
    header("Location: blogs.php");
    exit;
}

$pathPrefix = "../../";
$pageTitle = ($post ? $post['title'] : "Blog Details") . " | Erix Construction Insights";
$metaDescription = $post ? substr(strip_tags($post['summary']), 0, 160) : "Read construction insights from Erix Construction.";
$metaKeywords = "construction blog, " . ($post ? "Erix Construction, " . strtolower(str_replace(' ', ', ', $post['title'])) : "");
$ogImage = $post && $post['image_url'] ? (strpos($post['image_url'], 'http') === 0 ? $post['image_url'] : $pathPrefix . $post['image_url']) : "";

$currentPage = "blogs";
$navClass = "scrolled";
$navLocked = true;
include '../includes/header.php';
?>

<!-- ── MAIN PAGE CONTENT ── -->
<main class="blog-details-page">

  <!-- Large Centered Image Section (Padded) -->
  <section class="blog-details-hero-img-container">
    <div class="blog-details-img-wrapper">
      <?php 
        $imageSrc = $post ? $post['image_url'] : '';
        if ($imageSrc && strpos($imageSrc, 'http') !== 0) {
            $imageSrc = $pathPrefix . $imageSrc;
        }
        if (!$imageSrc) {
            $imageSrc = 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=1200&q=80';
        }
      ?>
      <img src="<?php echo htmlspecialchars($imageSrc); ?>" alt="<?php echo htmlspecialchars($post ? $post['title'] : 'Blog'); ?>" />
      <div class="blog-details-img-overlay"></div>
    </div>
  </section>

  <!-- Narrative details -->
  <section class="blog-details-content-container">
    
    <!-- Title & Eyebrow -->
    <div class="blog-details-header">
      <div class="blog-details-eyebrow">
        <div class="blog-details-eyebrow-line"></div>
        <span class="blog-details-eyebrow-text">Industry Insights</span>
      </div>
      <h1 class="blog-details-title">
        <?php 
          if ($post) {
              $titleParts = explode(" ", $post['title']);
              if (count($titleParts) > 2) {
                  $spanWords = array_slice($titleParts, -2);
                  $mainWords = array_slice($titleParts, 0, -2);
                  echo htmlspecialchars(implode(" ", $mainWords)) . " <span>" . htmlspecialchars(implode(" ", $spanWords)) . "</span>";
              } else {
                  echo htmlspecialchars($post['title']);
              }
          } else {
              echo "Blog <span>Details</span>";
          }
        ?>
      </h1>
      
      <!-- Author details -->
      <div class="blog-details-author-card">
        <div class="blog-details-author-avatar" style="display: flex; align-items: center; justify-content: center; background-color: var(--gold); color: #fff; font-size: 24px; font-weight: bold; font-family: 'Bebas Neue', sans-serif; border-radius: 50%; width: 50px; height: 50px;">
          <?php 
            $authorName = $post ? $post['author'] : 'Admin';
            echo htmlspecialchars(strtoupper(substr($authorName, 0, 1)));
          ?>
        </div>
        <div class="blog-details-author-info">
          <div class="blog-details-author-name"><?php echo htmlspecialchars($authorName); ?></div>
          <div class="blog-details-author-meta">Erix Contributor · <?php echo $post ? date('M d, Y', strtotime($post['date_published'])) : ''; ?> · 5 min read</div>
        </div>
      </div>
    </div>

    <div class="blog-details-layout">
      <style>
        .blog-content-card {
            background: #ffffff;
            border: 1px solid rgba(13, 13, 13, 0.08);
            border-radius: 4px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        @media (max-width: 768px) {
            .blog-content-card {
                padding: 20px;
            }
        }
      </style>
      
      <!-- Blog Post Content -->
      <article class="blog-details-body blog-content-card">
        <?php 
          if ($post) {
              $content = $post['content'];
              $paragraphs = explode("\n", $content);
              $validParas = [];
              foreach ($paragraphs as $para) {
                  if (trim($para)) {
                      $validParas[] = trim($para);
                  }
              }
              
              $count = count($validParas);
              foreach ($validParas as $index => $para) {
                  $p = nl2br(htmlspecialchars($para));
                  if ($index === 0) {
                      $p = '<span style="color: var(--gold); font-size: 1.5em; font-weight: bold; font-family: serif;">&ldquo;</span> ' . $p;
                  }
                  if ($index === $count - 1) {
                      $p = $p . ' <span style="color: var(--gold); font-size: 1.5em; font-weight: bold; font-family: serif;">&rdquo;</span>';
                  }
                  echo "<p>" . $p . "</p>";
              }
          }
        ?>
      </article>

      <!-- Recent Blogs Sidebar (Hidden on Mobile) -->
      <aside class="blog-details-sidebar">
        <h3>Recent Blogs</h3>
        <div class="recent-blogs-list">
          <?php if (count($recentBlogs) > 0): ?>
            <?php foreach ($recentBlogs as $rec): ?>
              <?php 
                $recDate = date('M d, Y', strtotime($rec['date_published']));
                $recImage = $rec['image_url'];
                if ($recImage && strpos($recImage, 'http') !== 0) {
                    $recImage = $pathPrefix . $recImage;
                }
                if (!$recImage) {
                    $recImage = 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=150&q=80';
                }
              ?>
              <a href="blog-details.php?id=<?php echo $rec['id']; ?>" class="recent-blog-card">
                <div class="recent-blog-img">
                  <img src="<?php echo htmlspecialchars($recImage); ?>" alt="<?php echo htmlspecialchars($rec['title']); ?>" />
                </div>
                <div class="recent-blog-info">
                  <span class="recent-blog-date"><?php echo htmlspecialchars($recDate); ?></span>
                  <h4><?php echo htmlspecialchars($rec['title']); ?></h4>
                </div>
              </a>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="color: rgba(26,26,26,0.55); font-size: 13px;">No other articles available.</p>
          <?php endif; ?>
        </div>
      </aside>
    </div>

  </section>

</main>

<?php include '../includes/footer.php'; ?>
