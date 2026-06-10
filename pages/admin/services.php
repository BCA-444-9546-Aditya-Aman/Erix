<?php
$adminTitle = "Manage Services";
$activeTab = "services";
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
    .col-tags, .col-featured {
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
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Service deleted successfully.";
        $msgType = "success";
        $action = 'list';
    } catch (\PDOException $e) {
        $msg = "Error deleting service: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add' || $action === 'edit')) {
    $title = trim($_POST['title']);
    $short_desc = trim($_POST['short_desc']);
    $tags = trim($_POST['tags']);
    $icon_svg = trim($_POST['icon_svg']);
    $image_url = trim($_POST['image_url']);
    $full_desc = trim($_POST['full_desc']);
    $philosophy = trim($_POST['philosophy']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Check featured limit (max 4)
    $proceed = true;
    if ($is_featured) {
        try {
            if ($action === 'add') {
                $checkStmt = $pdo->query("SELECT COUNT(*) FROM services WHERE is_featured = 1");
            } else {
                $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE is_featured = 1 AND id != ?");
                $checkStmt->execute([$id]);
            }
            $count = $checkStmt->fetchColumn();
            if ($count >= 4) {
                $msg = "You can only select up to 4 services to show on the landing page.";
                $msgType = "danger";
                $proceed = false;
            }
        } catch (\PDOException $e) {
            $msg = "Database error: " . $e->getMessage();
            $msgType = "danger";
            $proceed = false;
        }
    }

    if ($proceed) {
        // File Upload handling for service image
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fileTmpPath);
        finfo_close($finfo);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        
        if (in_array($fileExtension, $allowedExtensions) && in_array($mime, $allowedMimes)) {
            $uploadFileDir = '../../assets/images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_url = 'assets/images/' . $newFileName;
            }
        } else {
            $_SESSION['error'] = "Invalid image format. Only JPG, PNG, and WEBP are allowed.";
            header("Location: services.php");
            exit;
        }
    }

        if (!empty($title) && !empty($short_desc) && !empty($full_desc)) {
            try {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("INSERT INTO services (title, short_desc, tags, icon_svg, image_url, full_desc, philosophy, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $short_desc, $tags, $icon_svg, $image_url, $full_desc, $philosophy, $is_featured]);
                    $msg = "Service added successfully.";
                    $msgType = "success";
                    $action = 'list';
                } else {
                    $stmt = $pdo->prepare("UPDATE services SET title = ?, short_desc = ?, tags = ?, icon_svg = ?, image_url = ?, full_desc = ?, philosophy = ?, is_featured = ? WHERE id = ?");
                    $stmt->execute([$title, $short_desc, $tags, $icon_svg, $image_url, $full_desc, $philosophy, $is_featured, $id]);
                    $msg = "Service updated successfully.";
                    $msgType = "success";
                    $action = 'list';
                }
            } catch (\PDOException $e) {
                $msg = "Database error: " . $e->getMessage();
                $msgType = "danger";
            }
        } else {
            $msg = "Please fill in all required fields (Title, Short Description, Full Description).";
            $msgType = "danger";
        }
    }
}

// Fetch service for viewing or editing
$serviceItem = null;
if (($action === 'edit' || $action === 'view') && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $serviceItem = $stmt->fetch();
        if (!$serviceItem) {
            $msg = "Service not found.";
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
    <h1>Manage <span>Services</span></h1>
    <?php if ($action === 'add'): ?><p>Add a new service to your offerings.</p>
    <?php elseif ($action === 'edit'): ?><p>Edit service details.</p>
    <?php elseif ($action === 'view'): ?><p>Detailed view of service #<?php echo $id; ?></p>
    <?php else: ?><p>View, modify, or delete your construction services.</p><?php endif; ?>
  </div>
  <div>
    <?php if ($action === 'list'): ?>
      <a href="services.php?action=add" class="btn-gold">Add New Service</a>
    <?php else: ?>
      <a href="services.php" class="btn-outline">Back to List</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert-<?php echo $msgType; ?>">
    <?php echo htmlspecialchars($msg); ?>
  </div>
<?php endif; ?>

<!-- ── DETAIL VIEW ── -->
<?php if ($action === 'view' && $serviceItem): ?>
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
    
    .detail-left {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    
    .detail-right {
      display: flex;
      flex-direction: column;
    }
    
    .detail-cover-img {
      width: 100%;
      height: auto;
      border-radius: 6px;
      border: 1px solid rgba(245,245,240,0.1);
      display: block;
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
      font-size: 16px;
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
      margin-bottom: 20px;
    }
  </style>

  <div class="split-detail-card">
    <!-- LEFT COLUMN -->
    <div class="detail-left">
      <?php 
        $imgSrc = $serviceItem['image_url'];
        if ($imgSrc && strpos($imgSrc, 'http') !== 0) {
            $imgSrc = '../' . $imgSrc;
        }
      ?>
      <?php if ($imgSrc): ?>
        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Cover" class="detail-cover-img">
      <?php else: ?>
        <div style="width:100%; padding:40px 20px; background:rgba(0,0,0,0.1); text-align:center; border-radius:6px; color:rgba(26,26,26,0.5);">No Cover Image</div>
      <?php endif; ?>
      
      <div style="margin-top: 10px;">
        <div class="meta-label">Title</div>
        <div class="meta-value" style="font-size: 20px;"><?php echo htmlspecialchars($serviceItem['title']); ?></div>
      </div>
      
      <div>
        <div class="meta-label">Tags</div>
        <div class="meta-value"><?php echo htmlspecialchars($serviceItem['tags']); ?></div>
      </div>

      <div>
        <div class="meta-label">Featured on Landing Page?</div>
        <div class="meta-value">
            <?php if ($serviceItem['is_featured']): ?>
                <span class="badge badge-success">Yes</span>
            <?php else: ?>
                <span class="badge badge-warning">No</span>
            <?php endif; ?>
        </div>
      </div>
      
      <div>
        <div class="meta-label" style="margin-bottom: 10px;">Short Description</div>
        <div class="message-body" style="font-size: 14px; padding: 15px; min-height: unset;"><?php echo htmlspecialchars($serviceItem['short_desc']); ?></div>
      </div>
    </div>
    
    <!-- RIGHT COLUMN -->
    <div class="detail-right">
      <div class="meta-label" style="margin-bottom: 15px; font-size: 16px;">Full Description</div>
      <div class="message-body" style="font-size: 16px; min-height: 200px;"><?php echo htmlspecialchars($serviceItem['full_desc']); ?></div>

      <?php if (!empty($serviceItem['philosophy'])): ?>
      <div class="meta-label" style="margin-bottom: 15px; font-size: 16px;">Philosophy / Quote</div>
      <div class="message-body" style="font-size: 16px; min-height: 100px; font-style: italic;"><?php echo htmlspecialchars($serviceItem['philosophy']); ?></div>
      <?php endif; ?>

      <div style="margin-top: auto; padding-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="services.php?action=edit&id=<?php echo $serviceItem['id']; ?>" class="btn-gold">Edit Service</a>
        <a href="services.php?action=delete&id=<?php echo $serviceItem['id']; ?>" class="btn-outline" style="color: #ff858d; border-color: rgba(220,53,69,0.3);" onclick="return confirm('Are you sure you want to delete this service?');">Delete</a>
      </div>
    </div>
  </div>

<!-- ── ADD / EDIT FORM ── -->
<?php elseif ($action === 'add' || $action === 'edit'): ?>
  <form action="services.php?action=<?php echo $action; ?><?php echo ($id > 0) ? '&id='.$id : ''; ?>" method="POST" enctype="multipart/form-data" class="admin-form" style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    
    <div class="form-group full-width">
      <label for="title">Service Title (Required)</label>
      <input type="text" id="title" name="title" required value="<?php echo ($serviceItem) ? htmlspecialchars($serviceItem['title']) : ''; ?>" placeholder="e.g. Residential Construction">
    </div>
    
    <div class="form-group">
      <label for="tags">Tags / Sub-services</label>
      <input type="text" id="tags" name="tags" value="<?php echo ($serviceItem) ? htmlspecialchars($serviceItem['tags']) : ''; ?>" placeholder="e.g. Homes · Villas · Apartments">
    </div>

    <div class="form-group" style="display: flex; align-items: center; justify-content: flex-start;">
      <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0; padding-top: 25px;">
        <input type="checkbox" name="is_featured" value="1" <?php echo ($serviceItem && $serviceItem['is_featured']) ? 'checked' : ''; ?> style="width: auto; margin: 0;">
        <span style="font-weight: 500; font-size: 15px;">Show on Landing Page (Featured)</span>
      </label>
    </div>
    
    <div class="form-group full-width">
      <label for="short_desc">Short Description (Required - appears on cards)</label>
      <textarea id="short_desc" name="short_desc" required rows="3" placeholder="Brief summary of the service..."><?php echo ($serviceItem) ? htmlspecialchars($serviceItem['short_desc']) : ''; ?></textarea>
    </div>

    <div class="form-group full-width">
      <label for="icon_svg">Icon SVG Code</label>
      <textarea id="icon_svg" name="icon_svg" rows="3" placeholder="Paste the raw <svg>...</svg> code here..."><?php echo ($serviceItem) ? htmlspecialchars($serviceItem['icon_svg']) : ''; ?></textarea>
      <div style="font-size: 11px; color: rgba(26,26,26,0.5); margin-top: 5px;">This will render directly in the UI. Make sure it's valid SVG HTML.</div>
    </div>
    
    <div class="form-group full-width" style="border-top: 1px solid rgba(245,245,240,0.1); padding-top: 20px;">
      <label>Cover Image</label>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 5px;">
        <div>
          <label for="image_file" style="color: rgba(26,26,26,0.6); font-size:12px;">Upload Image:</label>
          <input type="file" id="image_file" name="image_file" accept="image/*" style="display:block; margin-top:8px;">
        </div>
        <div>
          <label for="image_url" style="color: rgba(26,26,26,0.6); font-size:12px;">Or Paste URL:</label>
          <input type="url" id="image_url" name="image_url" value="<?php echo ($serviceItem) ? htmlspecialchars($serviceItem['image_url']) : ''; ?>" placeholder="e.g. https://images.unsplash.com/photo-..." style="margin-top:8px;">
        </div>
      </div>

      <?php if ($serviceItem && $serviceItem['image_url']): ?>
        <div style="margin-top: 15px;">
          <p style="font-size: 12px; color: rgba(26,26,26,0.5); margin-bottom: 5px;">Current Image:</p>
          <?php 
            $imgSrc = $serviceItem['image_url'];
            if (strpos($imgSrc, 'http') !== 0) { $imgSrc = '../' . $imgSrc; }
          ?>
          <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Current Image" style="max-height: 100px; border-radius: 4px;">
        </div>
      <?php endif; ?>
    </div>
    
    <div class="form-group full-width" style="margin-top: 10px;">
      <label for="full_desc">Full Description (Required - for Details Page)</label>
      <textarea id="full_desc" name="full_desc" required rows="10" placeholder="Write the complete service details here..."><?php echo ($serviceItem) ? htmlspecialchars($serviceItem['full_desc']) : ''; ?></textarea>
    </div>

    <div class="form-group full-width" style="margin-top: 10px;">
      <label for="philosophy">Philosophy / Quote (For Details Page)</label>
      <textarea id="philosophy" name="philosophy" rows="3" placeholder="A meaningful quote or philosophy paragraph..."><?php echo ($serviceItem) ? htmlspecialchars($serviceItem['philosophy']) : ''; ?></textarea>
    </div>
    
    <div class="full-width form-actions">
      <a href="services.php" class="btn-outline">Cancel</a>
      <button type="submit" class="btn-gold">Save Service</button>
    </div>
  </form>

<!-- ── LIST VIEW ── -->
<?php else: ?>
  <?php
  try {
      $stmt = $pdo->query("SELECT * FROM services ORDER BY created_at DESC");
      $services = $stmt->fetchAll();
  } catch (\PDOException $e) {
      $services = [];
      $msg = "Error loading services: " . $e->getMessage();
      $msgType = "danger";
  }
  ?>
  
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th class="col-image">Image</th>
          <th class="col-title">Title</th>
          <th class="col-tags">Tags</th>
          <th class="col-featured">Featured</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($services) > 0): ?>
          <?php foreach ($services as $srv): ?>
            <?php 
              $imgSrc = $srv['image_url'];
              if ($imgSrc && strpos($imgSrc, 'http') !== 0) {
                  $imgSrc = '../' . $imgSrc;
              }
            ?>
            <tr style="cursor: pointer;" onclick="window.location='services.php?action=view&id=<?php echo $srv['id']; ?>'">
              <td class="col-image" style="width: 80px;">
                <?php if ($imgSrc): ?>
                  <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Service Image" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid rgba(245,245,240,0.1);">
                <?php else: ?>
                  <div style="width: 60px; height: 40px; background: rgba(0,0,0,0.1); border-radius: 4px; display:flex; align-items:center; justify-content:center; font-size:10px; color:rgba(26,26,26,0.3);">No Img</div>
                <?php endif; ?>
              </td>
              <td class="col-title" style="font-weight: 500; font-size: 15px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($srv['title']); ?></td>
              <td class="col-tags" style="color: rgba(26,26,26,0.65);"><?php echo htmlspecialchars($srv['tags']); ?></td>
              <td class="col-featured">
                <?php if ($srv['is_featured']): ?>
                    <span class="badge badge-success">Yes</span>
                <?php else: ?>
                    <span style="color: rgba(26,26,26,0.4);">No</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" style="text-align: center; color: rgba(26,26,26,0.45); padding: 40px 0;">
              No services found. Click 'Add New Service' to get started.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include 'layout_bottom.php'; ?>
