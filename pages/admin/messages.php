<?php
$adminTitle = "Inquiry Messages";
$activeTab = "messages";
include 'layout_top.php';
?>
<style>
  /* Filter Bar Styles */
  .filter-bar {
    display: flex;
    gap: 20px;
    align-items: center;
    margin-bottom: 25px;
    background: var(--card-bg);
    border: 1px solid var(--border);
    padding: 15px 25px;
    border-radius: 8px;
    flex-wrap: wrap;
  }
  
  .filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .filter-group label {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 13px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: rgba(26,26,26,0.7);
    margin-bottom: 0 !important;
  }
  
  .filter-select {
    background: #ffffff;
    border: 1px solid rgba(13,13,13,0.12);
    border-radius: 4px;
    padding: 8px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: #1a1a1a;
    outline: none;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .filter-select:focus {
    border-color: var(--gold);
    box-shadow: 0 0 8px rgba(212,160,23,0.15);
  }
  
  /* Unread Badge / Row styling */
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
  
  tr.unread-row td.col-email,
  tr.unread-row td.col-phone {
    font-weight: 400 !important; /* Keep secondary columns normal weight */
  }
  
  .action-links-mobile {
    display: none;
  }
  
  .action-links-desktop {
    display: flex;
    gap: 12px;
  }
  
  .btn-clear-filters {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid rgba(13,13,13,0.12);
    border-radius: 4px;
    padding: 8px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: rgba(26,26,26,0.8);
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s;
  }
  
  .btn-clear-filters:hover {
    color: var(--danger);
    border-color: var(--danger);
    background: rgba(220,53,69,0.02);
  }
  
  @media (max-width: 576px) {
    .col-email, .col-phone, .col-service {
      display: none !important;
    }
    
    .action-links-desktop {
      display: none !important;
    }
    
    .action-links-mobile {
      display: flex !important;
      gap: 16px;
      justify-content: flex-start;
      align-items: center;
    }
    
    .action-links-mobile a {
      color: rgba(26,26,26,0.6);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: color 0.3s;
    }
    
    .action-links-mobile a:hover {
      color: var(--gold);
    }
    
    .table-responsive {
      overflow-x: visible !important;
    }
    
    table {
      width: 100% !important;
    }
    
    td, th {
      padding: 12px 10px !important;
    }
    
    .filter-bar {
      padding: 12px 15px;
      gap: 12px;
    }
    
    .filter-group {
      width: 100%;
      justify-content: space-between;
    }
    
    .filter-select {
      width: 60%;
    }
    
    .btn-clear-filters {
      width: auto;
      margin-left: 0;
      padding: 8px 12px;
      margin-top: 4px;
      align-self: flex-start;
    }
  }
</style>
<?php

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$msg = '';
$msgType = '';

// Handle Delete Action
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Message deleted successfully.";
        $msgType = "success";
        $action = 'list';
    } catch (\PDOException $e) {
        $msg = "Error deleting message: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Fetch single message details if requested
$messageDetails = null;
if ($id > 0 && $action !== 'delete') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmt->execute([$id]);
        $messageDetails = $stmt->fetch();
        if (!$messageDetails) {
            $msg = "Message not found.";
            $msgType = "danger";
        } else {
            $action = 'view';
            // Mark message as read when viewed
            if ($messageDetails['is_read'] == 0) {
                $updateStmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
                $updateStmt->execute([$id]);
                $messageDetails['is_read'] = 1; // Update local variable
            }
        }
    } catch (\PDOException $e) {
        $msg = "Database error: " . $e->getMessage();
        $msgType = "danger";
    }
}
?>

<div class="content-header">
  <div class="content-title">
    <h1>Customer <span>Inquiries</span></h1>
    <?php if ($action === 'view'): ?><p>Detailed view of inquiry #<?php echo $id; ?></p>
    <?php else: ?><p>Browse and review local logs of customer inquiries from the contact form.</p><?php endif; ?>
  </div>
  <div>
    <?php if ($action === 'view'): ?>
      <a href="messages.php" class="btn-outline">Back to List</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert-<?php echo $msgType; ?>">
    <?php echo htmlspecialchars($msg); ?>
  </div>
<?php endif; ?>

<!-- ── DETAIL VIEW ── -->
<?php if ($action === 'view' && $messageDetails): ?>
  <style>
    .message-detail-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 35px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .meta-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
      border-bottom: 1px solid rgba(212, 160, 23, 0.08);
      padding-bottom: 20px;
    }
    
    .meta-item {
      font-size: 14px;
    }
    
    .meta-label {
      font-family: 'Barlow Condensed', sans-serif;
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: 1px;
      color: rgba(26,26,26,0.6);
      margin-bottom: 5px;
    }
    
    .meta-value {
      font-weight: 500;
      color: #1a1a1a;
    }
    
    .message-body {
      background: rgba(212, 160, 23, 0.04);
      border: 1px solid rgba(212, 160, 23, 0.12);
      padding: 25px;
      border-radius: 6px;
      line-height: 1.6;
      white-space: pre-wrap;
      font-size: 15px;
      color: #1a1a1a;
      margin-bottom: 30px;
    }
  </style>

  <div class="message-detail-card">
    <div class="meta-row">
      <div class="meta-item">
        <div class="meta-label">Submitted On</div>
        <div class="meta-value"><?php echo date('M d, Y H:i', strtotime($messageDetails['submitted_at'])); ?></div>
      </div>
      <div class="meta-item">
        <div class="meta-label">Full Name</div>
        <div class="meta-value"><?php echo htmlspecialchars($messageDetails['full_name']); ?></div>
      </div>
      <div class="meta-item">
        <div class="meta-label">Service Required</div>
        <div class="meta-value"><span class="badge badge-info"><?php echo htmlspecialchars($messageDetails['service']); ?></span></div>
      </div>
    </div>
    
    <div class="meta-row" style="margin-top: -10px; border-bottom: none; padding-bottom: 0;">
      <div class="meta-item">
        <div class="meta-label">Email Address</div>
        <div class="meta-value">
          <a href="mailto:<?php echo htmlspecialchars($messageDetails['email']); ?>" style="color: var(--gold); text-decoration: none;">
            <?php echo htmlspecialchars($messageDetails['email']); ?> ✉
          </a>
        </div>
      </div>
      <div class="meta-item">
        <div class="meta-label">Phone Number</div>
        <div class="meta-value">
          <a href="tel:<?php echo htmlspecialchars($messageDetails['phone']); ?>" style="color: var(--gold); text-decoration: none;">
            <?php echo htmlspecialchars($messageDetails['phone']); ?> 📞
          </a>
        </div>
      </div>
      <div class="meta-item">
        <!-- Spacer -->
      </div>
    </div>

    <div style="margin-top: 30px;">
      <div class="meta-label" style="margin-bottom: 10px;">Message Text</div>
      <div class="message-body"><?php echo htmlspecialchars($messageDetails['message']); ?></div>
    </div>
    
    <div style="display: flex; gap: 15px;">
      <a href="mailto:<?php echo htmlspecialchars($messageDetails['email']); ?>?subject=Inquiry Reply - Erix Construction" class="btn-gold">Reply by Email</a>
      <a href="messages.php?action=delete&id=<?php echo $messageDetails['id']; ?>" class="btn-outline" style="color: #ff858d; border-color: rgba(220,53,69,0.3);" onclick="return confirm('Are you sure you want to delete this inquiry?');">Delete Inquiry</a>
      <a href="messages.php" class="btn-outline">Back to Inbox</a>
    </div>
  </div>

<!-- ── LIST VIEW ── -->
<?php else: ?>
  <?php
  // Build query based on filters
  $filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
  $filterSort = isset($_GET['sort']) ? $_GET['sort'] : '';

  // Base Query
  $query = "SELECT * FROM contact_messages";
  $params = [];

  // Apply status filter
  if ($filterStatus === 'unread') {
      $query .= " WHERE is_read = 0";
  } elseif ($filterStatus === 'read') {
      $query .= " WHERE is_read = 1";
  }

  // Apply sorting
  if ($filterSort === 'newest') {
      $query .= " ORDER BY submitted_at DESC";
  } elseif ($filterSort === 'oldest') {
      $query .= " ORDER BY submitted_at ASC";
  } else {
      // Default: Unread on top, then newest first
      $query .= " ORDER BY is_read ASC, submitted_at DESC";
  }

  try {
      $stmt = $pdo->prepare($query);
      $stmt->execute($params);
      $messages = $stmt->fetchAll();
  } catch (\PDOException $e) {
      echo "<div class='alert alert-danger'>Error loading inquiries: " . $e->getMessage() . "</div>";
      $messages = [];
  }
  ?>

  <!-- Filter Bar -->
  <form method="GET" action="messages.php" class="filter-bar">
    <div class="filter-group">
      <label for="status-filter">Status:</label>
      <select name="status" id="status-filter" class="filter-select" onchange="this.form.submit()">
        <option value="all" <?php echo ($filterStatus === 'all') ? 'selected' : ''; ?>>All Messages</option>
        <option value="unread" <?php echo ($filterStatus === 'unread') ? 'selected' : ''; ?>>Unread</option>
        <option value="read" <?php echo ($filterStatus === 'read') ? 'selected' : ''; ?>>Read</option>
      </select>
    </div>
    
    <div class="filter-group">
      <label for="sort-filter">Sort By:</label>
      <select name="sort" id="sort-filter" class="filter-select" onchange="this.form.submit()">
        <option value="" <?php echo ($filterSort === '') ? 'selected' : ''; ?>>Sort By...</option>
        <option value="newest" <?php echo ($filterSort === 'newest') ? 'selected' : ''; ?>>Date: Newest First</option>
        <option value="oldest" <?php echo ($filterSort === 'oldest') ? 'selected' : ''; ?>>Date: Oldest First</option>
      </select>
    </div>
    
    <?php if ($filterStatus !== 'all' || $filterSort !== ''): ?>
      <a href="messages.php" class="btn-clear-filters" title="Clear all filters">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="vertical-align: middle;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        <span>Clear</span>
      </a>
    <?php endif; ?>
  </form>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th class="col-date">Date/Time</th>
          <th class="col-name">Full Name</th>
          <th class="col-email">Email Address</th>
          <th class="col-phone">Phone</th>
          <th class="col-service">Service Required</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($messages) > 0): ?>
          <?php foreach ($messages as $msg): ?>
            <tr class="<?php echo ($msg['is_read'] == 0) ? 'unread-row' : ''; ?>" style="cursor: pointer;" onclick="window.location='messages.php?id=<?php echo $msg['id']; ?>'">
              <td class="col-date" style="white-space: nowrap; color: rgba(26,26,26,0.65);">
                <?php if ($msg['is_read'] == 0): ?>
                  <span class="unread-dot" title="Unread Message"></span>
                <?php endif; ?>
                <?php echo date('M d, Y H:i', strtotime($msg['submitted_at'])); ?>
              </td>
              <td class="col-name" style="font-weight: 500;"><?php echo htmlspecialchars($msg['full_name']); ?></td>
              <td class="col-email"><?php echo htmlspecialchars($msg['email']); ?></td>
              <td class="col-phone" style="white-space: nowrap;"><?php echo htmlspecialchars($msg['phone']); ?></td>
              <td class="col-service"><span class="badge badge-info"><?php echo htmlspecialchars($msg['service']); ?></span></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; color: rgba(26,26,26,0.45); padding: 40px 0;">
              No inquiries found matching your filters.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
include 'layout_bottom.php';
?>
