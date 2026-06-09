<?php
$adminTitle = "Manage Blogs";
$activeTab = "blogs";
include 'layout_top.php';
?>
<style>
  .action-links-mobile {
    display: none;
  }
  
  .action-links-desktop {
    display: flex;
    gap: 12px;
  }
  
  @media (max-width: 576px) {
    .col-author, .col-date, .col-summary {
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
        $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Blog post deleted successfully.";
        $msgType = "success";
        $action = 'list'; // Go back to list
    } catch (\PDOException $e) {
        $msg = "Error deleting blog post: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add' || $action === 'edit')) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $date_published = trim($_POST['date_published']);
    $summary = trim($_POST['summary']);
    $content = trim($_POST['content']);
    $image_url = trim($_POST['image_url']);
    
    // File Upload handling
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileSize = $_FILES['image_file']['size'];
        $fileType = $_FILES['image_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadFileDir = '../../assets/images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Use relative path for database storage (so it works from index.php and pages/users/)
                $image_url = 'assets/images/' . $newFileName;
            }
        }
    }

    if (!empty($title) && !empty($author) && !empty($date_published) && !empty($summary) && !empty($content)) {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO blogs (title, author, date_published, summary, content, image_url) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $author, $date_published, $summary, $content, $image_url]);
                $msg = "Blog post published successfully.";
                $msgType = "success";
                $action = 'list';
            } else {
                $stmt = $pdo->prepare("UPDATE blogs SET title = ?, author = ?, date_published = ?, summary = ?, content = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$title, $author, $date_published, $summary, $content, $image_url, $id]);
                $msg = "Blog post updated successfully.";
                $msgType = "success";
                $action = 'list';
            }
        } catch (\PDOException $e) {
            $msg = "Database error: " . $e->getMessage();
            $msgType = "danger";
        }
    } else {
        $msg = "Please fill in all required fields.";
        $msgType = "danger";
    }
}

// Fetch blog post for viewing or editing
$blog = null;
if (($action === 'edit' || $action === 'view') && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        $blog = $stmt->fetch();
        if (!$blog) {
            $msg = "Blog post not found.";
            $msgType = "danger";
            $action = 'list';
        }
    } catch (\PDOException $e) {
        $msg = "Database error: " . $e->getMessage();
        $msgType = "danger";
    }
}
?>

<div class="content-header">
  <div class="content-title">
    <h1>Manage Blog <span>Posts</span></h1>
    <?php if ($action === 'add'): ?><p>Write and publish a new article on Erix Construction updates.</p>
    <?php elseif ($action === 'edit'): ?><p>Edit the blog article.</p>
    <?php elseif ($action === 'view'): ?><p>Detailed view of blog post #<?php echo $id; ?></p>
    <?php else: ?><p>View, modify, or delete published blog articles.</p><?php endif; ?>
  </div>
  <div>
    <?php if ($action === 'list'): ?>
      <a href="blogs.php?action=add" class="btn-gold">Write New Post</a>
    <?php else: ?>
      <a href="blogs.php" class="btn-outline">Back to List</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert-<?php echo $msgType; ?>">
    <?php echo htmlspecialchars($msg); ?>
  </div>
<?php endif; ?>

<!-- ── DETAIL VIEW ── -->
<?php if ($action === 'view' && $blog): ?>
  <style>
    .split-detail-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 40px;
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 350px 1fr;
      gap: 50px;
    }
    
    @media (max-width: 900px) {
      .split-detail-card {
        grid-template-columns: 1fr;
        padding: 25px;
      }
    }

    .detail-left-col {
      display: flex;
      flex-direction: column;
    }

    .detail-right-col {
      display: flex;
      flex-direction: column;
    }

    .meta-row {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      margin-bottom: 25px;
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
    
    .meta-value { font-weight: 500; color: #1a1a1a; font-size: 15px; }
    
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

    .project-cover-view {
      margin-bottom: 25px;
      text-align: center;
    }
    
    .project-cover-view img {
      width: 100%;
      height: 240px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid var(--border);
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
  </style>

  <div class="split-detail-card">
    
    <!-- LEFT COLUMN -->
    <div class="detail-left-col">
      <?php if ($blog['image_url']): ?>
        <div class="project-cover-view">
          <?php $src = (strpos($blog['image_url'], 'http') === 0) ? $blog['image_url'] : '../../' . $blog['image_url']; ?>
          <img src="<?php echo htmlspecialchars($src); ?>" alt="Blog Image" />
        </div>
      <?php endif; ?>

      <div class="meta-row" style="border-bottom: none; padding-bottom: 0;">
        <div class="meta-item" style="width: 100%;">
          <div class="meta-label">Title</div>
          <div class="meta-value" style="font-size: 20px; color: var(--gold);"><?php echo htmlspecialchars($blog['title']); ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Author</div>
          <div class="meta-value"><?php echo htmlspecialchars($blog['author']); ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Date Published</div>
          <div class="meta-value"><?php echo htmlspecialchars($blog['date_published']); ?></div>
        </div>
      </div>

      <div class="meta-label" style="margin-top: 25px; margin-bottom: 10px; font-size: 15px;">Summary</div>
      <div class="message-body" style="font-size: 14px; margin-bottom: 0; border-left: 4px solid var(--gold);"><?php echo htmlspecialchars($blog['summary']); ?></div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="detail-right-col">
      <div class="meta-label" style="margin-bottom: 15px; font-size: 16px;">Full Content</div>
      <div class="message-body" style="font-size: 16px; min-height: 300px;"><?php echo htmlspecialchars($blog['content']); ?></div>

      <div style="margin-top: auto; padding-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="blogs.php?action=edit&id=<?php echo $blog['id']; ?>" class="btn-gold">Edit Blog</a>
        <a href="blogs.php?action=delete&id=<?php echo $blog['id']; ?>" class="btn-outline" style="color: #ff858d; border-color: rgba(220,53,69,0.3);" onclick="return confirm('Are you sure you want to delete this blog post?');">Delete</a>
      </div>
    </div>
    
  </div>

<!-- ── LIST VIEW ── -->
<?php elseif ($action === 'list'): ?>
  <?php
  try {
      $stmt = $pdo->query("SELECT * FROM blogs ORDER BY date_published DESC");
      $blogs = $stmt->fetchAll();
  } catch (\PDOException $e) {
      echo "<div class='alert alert-danger'>Error loading blogs: " . $e->getMessage() . "</div>";
      $blogs = [];
  }
  ?>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th class="col-image">Image</th>
          <th class="col-title">Title</th>
          <th class="col-author">Author</th>
          <th class="col-date">Date Published</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($blogs) > 0): ?>
          <?php foreach ($blogs as $post): ?>
            <tr style="cursor: pointer;" onclick="window.location='blogs.php?action=view&id=<?php echo $post['id']; ?>'">
              <td class="col-image">
                <?php if ($post['image_url']): ?>
                  <?php 
                    $src = (strpos($post['image_url'], 'http') === 0) ? $post['image_url'] : '../../' . $post['image_url'];
                  ?>
                  <img src="<?php echo htmlspecialchars($src); ?>" alt="Blog thumbnail" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);" />
                <?php else: ?>
                  <span style="font-size: 11px; color: rgba(26,26,26,0.4);">No Image</span>
                <?php endif; ?>
              </td>
              <td class="col-title" style="font-weight: 500; font-size: 15px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($post['title']); ?></td>
              <td class="col-author"><?php echo htmlspecialchars($post['author']); ?></td>
              <td class="col-date" style="white-space: nowrap;"><?php echo date('M d, Y', strtotime($post['date_published'])); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; color: rgba(245,245,240,0.45); padding: 40px 0;">
              No blog posts found. Tap "Write New Post" to publish your first article.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<!-- ── ADD / EDIT FORM VIEW ── -->
<?php elseif ($action === 'add' || $action === 'edit'): ?>
  <form action="blogs.php?action=<?php echo $action; ?><?php echo ($action === 'edit') ? '&id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="form-group full-width">
      <label for="title">Blog Title <span style="color:var(--danger)">*</span></label>
      <input type="text" id="title" name="title" value="<?php echo ($blog) ? htmlspecialchars($blog['title']) : ''; ?>" placeholder="e.g. Revolutionizing Mumbai's Skyline" required>
    </div>
    
    <div class="form-group">
      <label for="author">Author <span style="color:var(--danger)">*</span></label>
      <input type="text" id="author" name="author" value="<?php echo ($blog) ? htmlspecialchars($blog['author']) : 'Admin'; ?>" placeholder="e.g. Aditya Aman" required>
    </div>
    
    <div class="form-group">
      <label for="date_published">Publish Date <span style="color:var(--danger)">*</span></label>
      <input type="date" id="date_published" name="date_published" value="<?php echo ($blog) ? htmlspecialchars($blog['date_published']) : date('Y-m-d'); ?>" required>
    </div>
    
    <div class="form-group full-width">
      <label for="summary">Article Summary / Excerpt <span style="color:var(--danger)">*</span></label>
      <textarea id="summary" name="summary" rows="3" placeholder="Write a short summary that appears on the blog list page..." required><?php echo ($blog) ? htmlspecialchars($blog['summary']) : ''; ?></textarea>
    </div>
    
    <div class="form-group full-width">
      <label for="content">Article Body Content <span style="color:var(--danger)">*</span></label>
      <textarea id="content" name="content" rows="12" placeholder="Write the complete blog article details here..." required><?php echo ($blog) ? htmlspecialchars($blog['content']) : ''; ?></textarea>
    </div>
    
    <div class="form-group full-width" style="border-top: 1px solid rgba(245,245,240,0.1); padding-top: 20px;">
      <label>Blog Cover Image</label>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 5px;">
        <div>
          <label for="image_file" style="color: rgba(245,245,240,0.6); font-size:12px;">Upload File (PNG, JPG, WEBP):</label>
          <input type="file" id="image_file" name="image_file" accept="image/*" style="display:block; margin-top:8px;">
        </div>
        <div>
          <label for="image_url" style="color: rgba(245,245,240,0.6); font-size:12px;">Or Paste URL:</label>
          <input type="url" id="image_url" name="image_url" value="<?php echo ($blog) ? htmlspecialchars($blog['image_url']) : ''; ?>" placeholder="e.g. https://images.unsplash.com/photo-..." style="margin-top:8px;">
        </div>
      </div>
      
      <?php if ($blog && $blog['image_url']): ?>
        <div style="margin-top: 15px;">
          <p style="font-size:12px; color: rgba(245,245,240,0.6); margin-bottom: 8px;">Current Image Preview:</p>
          <?php 
            $current_src = (strpos($blog['image_url'], 'http') === 0) ? $blog['image_url'] : '../../' . $blog['image_url'];
          ?>
          <img src="<?php echo htmlspecialchars($current_src); ?>" alt="Preview" style="max-width: 150px; max-height: 100px; border-radius: 4px; border: 1px solid var(--border);" />
        </div>
      <?php endif; ?>
    </div>
    
    <div class="full-width form-actions">
      <a href="blogs.php" class="btn-outline">Cancel</a>
      <button type="submit" class="btn-gold">Publish Post</button>
    </div>
  </form>
<?php endif; ?>

<?php
include 'layout_bottom.php';
?>
