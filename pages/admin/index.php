<?php
$adminTitle = "Dashboard Overview";
$activeTab = "dashboard";
include 'layout_top.php';

// Fetch counts
try {
    // Projects Count
    $stmt = $pdo->query("SELECT COUNT(*) FROM projects");
    $projectCount = $stmt->fetchColumn();

    // Blogs Count
    $stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
    $blogCount = $stmt->fetchColumn();

    // Messages Count
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages");
    $messageCount = $stmt->fetchColumn();

    // Unread Messages Count
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
    $unreadMessageCount = $stmt->fetchColumn();
    
    // Fetch latest 5 messages (Unread on top, then newest first)
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY is_read ASC, submitted_at DESC LIMIT 5");
    $recentMessages = $stmt->fetchAll();
    
} catch (\PDOException $e) {
    echo "<div class='alert alert-danger'>Error loading dashboard metrics: " . $e->getMessage() . "</div>";
    $projectCount = 0;
    $blogCount = 0;
    $messageCount = 0;
    $recentMessages = [];
}
?>

<div class="content-header">
  <div class="content-title">
    <h1>Dashboard <span>Overview</span></h1>
    <p>Welcome back, admin. Here's what's happening at Erix Construction.</p>
  </div>
  <div>
    <a href="../../index.php" class="btn-outline view-live-btn" target="_blank">View Live Site</a>
  </div>
</div>

<!-- Stats Grid -->
<style>
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
  }
  
  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s, border-color 0.3s;
  }
  
  .stat-card:hover {
    transform: translateY(-2px);
    border-color: var(--gold);
  }
  
  .stat-info h3 {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(26,26,26,0.6);
  }
  
  .stat-info .stat-number {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 48px;
    color: #1a1a1a;
    line-height: 1.1;
    margin-top: 10px;
  }
  
  .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(212, 160, 23, 0.06);
    color: var(--gold);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .stat-card:hover .stat-icon {
    background: rgba(212, 160, 23, 0.12);
  }
  
  /* Unread Dot & Row Styles */
  .unread-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: var(--gold);
    border-radius: 50%;
    margin-right: 8px;
    box-shadow: 0 0 6px var(--gold);
    vertical-align: middle;
  }
  
  tr.unread-row td {
    font-weight: 600 !important;
  }
  
  .recent-inquiries-section tbody tr {
    cursor: pointer;
  }
  
  .quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 45px;
  }
  
  .action-panel {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 30px;
  }
  
  .action-panel h2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 24px;
    letter-spacing: 1px;
    color: var(--white);
    margin-bottom: 15px;
    border-bottom: 1px solid rgba(212, 160, 23, 0.08);
    padding-bottom: 10px;
  }
  
  .action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 20px;
  }
  
  .carousel-dots {
    display: none;
  }
  
  @media (max-width: 992px) {
    .quick-actions {
      grid-template-columns: 1fr;
      gap: 20px;
    }
  }
  
  @media (max-width: 576px) {
    .recent-inquiries-section {
      display: none !important;
    }
    
    .stats-grid {
      display: flex !important;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      scroll-behavior: smooth;
      gap: 20px;
      padding: 10px 20px 20px 20px;
      margin-left: -20px !important;
      margin-right: -20px !important;
      -webkit-overflow-scrolling: touch;
      margin-bottom: 5px;
      scrollbar-width: none;
      scroll-padding: 20px;
    }
    
    .stats-grid::-webkit-scrollbar {
      display: none;
    }
    
    .stat-card {
      width: calc(100vw - 40px) !important;
      min-width: calc(100vw - 40px) !important;
      scroll-snap-align: start;
      flex-shrink: 0;
    }
    
    .carousel-dots {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-bottom: 25px;
      margin-top: -10px;
    }
    
    .carousel-dots .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: rgba(13, 13, 13, 0.12);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .carousel-dots .dot.active {
      background-color: var(--gold);
      transform: scale(1.25);
    }
    
    .quick-actions {
      grid-template-columns: 1fr;
      gap: 15px;
    }
    
    .content-title h1 {
      font-size: 28px;
    }
    
    .content-title h2 {
      font-size: 22px;
    }
    
    .action-panel h2 {
      font-size: 20px;
    }
    
    .btn-gold, .btn-outline {
      padding: 8px 14px;
      font-size: 12px;
      letter-spacing: 1px;
    }
    
    .view-live-btn {
      display: none !important;
    }
  }
</style>

<div class="stats-grid" id="stats-grid-carousel">
  <!-- Card 1 -->
  <a href="projects.php" class="stat-card">
    <div class="stat-info">
      <h3>Active Projects</h3>
      <div class="stat-number"><?php echo $projectCount; ?></div>
    </div>
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
    </div>
  </a>
  
  <!-- Card 2 -->
  <a href="blogs.php" class="stat-card">
    <div class="stat-info">
      <h3>Published Blogs</h3>
      <div class="stat-number"><?php echo $blogCount; ?></div>
    </div>
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    </div>
  </a>
  
  <!-- Card 3 -->
  <a href="messages.php" class="stat-card">
    <div class="stat-info">
      <h3>Contact Inquiries</h3>
      <div class="stat-number">
        <?php echo $messageCount; ?>
        <?php if ($unreadMessageCount > 0): ?>
          <span style="font-size: 16px; color: var(--gold); font-family: 'DM Sans', sans-serif; font-weight: 500; vertical-align: middle; margin-left: 8px;">(<?php echo $unreadMessageCount; ?> New)</span>
        <?php endif; ?>
      </div>
    </div>
    <div class="stat-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="28" height="28"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    </div>
  </a>
</div>

<!-- Carousel Indicators (Dots) for mobile -->
<div class="carousel-dots" id="stats-carousel-dots">
  <span class="dot active" onclick="goToSlide(0)"></span>
  <span class="dot" onclick="goToSlide(1)"></span>
  <span class="dot" onclick="goToSlide(2)"></span>
</div>

<div class="quick-actions">
  <div class="action-panel">
    <h2>Quick Content <span>Creation</span></h2>
    <p>Easily expand your portfolio and showcase new completed works.</p>
    <div class="action-buttons">
      <a href="projects.php?action=add" class="btn-gold">Add New Project</a>
    </div>
  </div>
  
  <div class="action-panel">
    <h2>Blog <span>Posts</span></h2>
    <p>Publish updates, construction news, or general blog articles here.</p>
    <div class="action-buttons">
      <a href="blogs.php?action=add" class="btn-gold">Write New Post</a>
      <a href="blogs.php" class="btn-outline">Manage Blog Posts</a>
    </div>
  </div>
</div>

<!-- Recent Messages Section -->
<div class="recent-inquiries-section">
  <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    <div class="content-title" style="margin-bottom: 0;">
      <h2>Recent Contact <span>Inquiries</span></h2>
      <p>Latest customer quotes and requests logged locally.</p>
    </div>
    <a href="messages.php" class="btn-outline" style="font-size: 13px; padding: 8px 16px;">View All Inquiries</a>
  </div>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Submitted At</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Service</th>
          <th>Message Excerpt</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($recentMessages) > 0): ?>
          <?php foreach ($recentMessages as $msg): ?>
            <tr class="<?php echo ($msg['is_read'] == 0) ? 'unread-row' : ''; ?>" onclick="window.location='messages.php?id=<?php echo $msg['id']; ?>';">
              <td style="white-space: nowrap; color: rgba(26,26,26,0.65);">
                <?php if ($msg['is_read'] == 0): ?>
                  <span class="unread-dot" title="Unread Message"></span>
                <?php endif; ?>
                <?php echo date('M d, Y H:i', strtotime($msg['submitted_at'])); ?>
              </td>
              <td style="font-weight: 500;"><?php echo htmlspecialchars($msg['full_name']); ?></td>
              <td><?php echo htmlspecialchars($msg['email']); ?></td>
              <td><span class="badge badge-info"><?php echo htmlspecialchars($msg['service']); ?></span></td>
              <td style="color: rgba(26,26,26,0.7); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <?php echo htmlspecialchars($msg['message']); ?>
              </td>
              <td>
                <a href="messages.php?id=<?php echo $msg['id']; ?>" class="action-edit" onclick="event.stopPropagation();">View Details</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; color: rgba(26,26,26,0.45); padding: 40px 0;">
              No contact inquiries found.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  function goToSlide(index) {
    const track = document.getElementById('stats-grid-carousel');
    if (track) {
      const cards = track.querySelectorAll('.stat-card');
      const card = cards[index];
      if (card) {
        const trackRect = track.getBoundingClientRect();
        const cardRect = card.getBoundingClientRect();
        // Calculate the card's offset relative to the track's scroll contents
        const cardOffset = cardRect.left - trackRect.left + track.scrollLeft;
        // Target scroll position centers or starts the card with the target scroll-padding (20px)
        const targetScrollLeft = cardOffset - 20; 
        track.scrollTo({
          left: targetScrollLeft,
          behavior: 'smooth'
        });
      }
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('stats-grid-carousel');
    const dots = document.querySelectorAll('#stats-carousel-dots .dot');
    
    if (track && dots.length > 0) {
      track.addEventListener('scroll', function() {
        const cards = track.querySelectorAll('.stat-card');
        const trackRect = track.getBoundingClientRect();
        const targetX = trackRect.left + 20; // active target point (20px from left)
        
        let activeIndex = 0;
        let minDistance = Infinity;
        
        cards.forEach((card, idx) => {
          const cardRect = card.getBoundingClientRect();
          const distance = Math.abs(cardRect.left - targetX);
          if (distance < minDistance) {
            minDistance = distance;
            activeIndex = idx;
          }
        });
        
        dots.forEach((dot, idx) => {
          if (idx === activeIndex) {
            dot.classList.add('active');
          } else {
            dot.classList.remove('active');
          }
        });
      }, { passive: true });
    }
  });
</script>

<?php
include 'layout_bottom.php';
?>
