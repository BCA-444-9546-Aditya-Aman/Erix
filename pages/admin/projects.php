<?php
$adminTitle = "Manage Projects";
$activeTab = "projects";
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
    .col-category, .col-location, .col-year, .col-stats {
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
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Project deleted successfully.";
        $msgType = "success";
        $action = 'list'; // Go back to list
    } catch (\PDOException $e) {
        $msg = "Error deleting project: " . $e->getMessage();
        $msgType = "danger";
    }
}

// Handle Gallery Image Delete
if ($action === 'delete_image' && isset($_GET['image_id'])) {
    $image_id = (int)$_GET['image_id'];
    $return_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM project_images WHERE id = ?");
        $stmt->execute([$image_id]);
        $msg = "Gallery image deleted successfully.";
        $msgType = "success";
        $action = 'edit';
        $id = $return_id;
    } catch (\PDOException $e) {
        $msg = "Error deleting image: " . $e->getMessage();
        $msgType = "danger";
        $action = 'edit';
        $id = $return_id;
    }
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add' || $action === 'edit')) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $location = trim($_POST['location']);
    $year = trim($_POST['year']);
    $description = trim($_POST['description']);
    $floors = trim($_POST['floors']);
    $units = trim($_POST['units']);
    $sq_ft = trim($_POST['sq_ft']);
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
            // Create assets/images if not exists
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

    if (!empty($name) && !empty($category) && !empty($location) && !empty($year) && !empty($description)) {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO projects (name, category, location, year, description, floors, units, sq_ft, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $category, $location, $year, $description, $floors, $units, $sq_ft, $image_url]);
                $project_id = $pdo->lastInsertId();
                $msg = "Project added successfully.";
                $msgType = "success";
                $action = 'list';
            } else {
                $stmt = $pdo->prepare("UPDATE projects SET name = ?, category = ?, location = ?, year = ?, description = ?, floors = ?, units = ?, sq_ft = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$name, $category, $location, $year, $description, $floors, $units, $sq_ft, $image_url, $id]);
                $project_id = $id;
                $msg = "Project updated successfully.";
                $msgType = "success";
                $action = 'list';
            }

            // Handle Gallery Images Upload
            if (isset($_FILES['gallery_files'])) {
                $fileCount = count($_FILES['gallery_files']['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['gallery_files']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['gallery_files']['tmp_name'][$i];
                        $fileName = $_FILES['gallery_files']['name'][$i];
                        $fileNameCmps = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));
                        
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $uploadFileDir = '../../assets/images/';
                            if (!is_dir($uploadFileDir)) {
                                mkdir($uploadFileDir, 0755, true);
                            }
                            $newFileName = md5(time() . $fileName . $i) . '.' . $fileExtension;
                            $dest_path = $uploadFileDir . $newFileName;
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $gal_url = 'assets/images/' . $newFileName;
                                $galStmt = $pdo->prepare("INSERT INTO project_images (project_id, image_url) VALUES (?, ?)");
                                $galStmt->execute([$project_id, $gal_url]);
                            }
                        }
                    }
                }
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

// Fetch project for viewing or editing
$project = null;
$gallery_images = [];
if (($action === 'edit' || $action === 'view') && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        
        $galStmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY created_at ASC");
        $galStmt->execute([$id]);
        $gallery_images = $galStmt->fetchAll();

        if (!$project) {
            $msg = "Project not found.";
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
    <h1>Manage <span>Projects</span></h1>
    <?php if ($action === 'add'): ?><p>Add a new showcase project to the Erix portfolio.</p>
    <?php elseif ($action === 'edit'): ?><p>Modify project details.</p>
    <?php elseif ($action === 'view'): ?><p>Detailed view of project #<?php echo $id; ?></p>
    <?php else: ?><p>View, modify, or delete items in the projects database.</p><?php endif; ?>
  </div>
  <div>
    <?php if ($action === 'list'): ?>
      <a href="projects.php?action=add" class="btn-gold">Add New Project</a>
    <?php else: ?>
      <a href="projects.php" class="btn-outline">Back to List</a>
    <?php endif; ?>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert-<?php echo $msgType; ?>">
    <?php echo htmlspecialchars($msg); ?>
  </div>
<?php endif; ?>

<!-- ── DETAIL VIEW ── -->
<?php if ($action === 'view' && $project): ?>
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

    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .gallery-grid img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid var(--border);
      transition: transform 0.3s;
    }
    .gallery-grid img:hover {
      transform: scale(1.02);
    }
  </style>

  <div class="split-detail-card">
    
    <!-- LEFT COLUMN -->
    <div class="detail-left-col">
      <?php if ($project['image_url']): ?>
        <div class="project-cover-view">
          <?php $src = (strpos($project['image_url'], 'http') === 0) ? $project['image_url'] : '../../' . $project['image_url']; ?>
          <img src="<?php echo htmlspecialchars($src); ?>" alt="Project Cover Image" />
        </div>
      <?php endif; ?>

      <div class="meta-row">
        <div class="meta-item" style="width: 100%;">
          <div class="meta-label">Project Name</div>
          <div class="meta-value" style="font-size: 20px; color: var(--gold);"><?php echo htmlspecialchars($project['name']); ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Category</div>
          <div class="meta-value"><span class="badge badge-info"><?php echo htmlspecialchars($project['category']); ?></span></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Location</div>
          <div class="meta-value"><?php echo htmlspecialchars($project['location']); ?></div>
        </div>
      </div>

      <div class="meta-row" style="border-bottom: none; padding-bottom: 0;">
        <div class="meta-item">
          <div class="meta-label">Year</div>
          <div class="meta-value"><?php echo htmlspecialchars($project['year']); ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Floors</div>
          <div class="meta-value"><?php echo htmlspecialchars($project['floors'] ?: 'N/A'); ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Units</div>
          <div class="meta-value"><?php echo htmlspecialchars($project['units'] ?: 'N/A'); ?></div>
        </div>
        <div class="meta-item">
          <div class="meta-label">Sq. Ft.</div>
          <div class="meta-value"><?php echo htmlspecialchars($project['sq_ft'] ?: 'N/A'); ?></div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="detail-right-col">
      <div class="meta-label" style="margin-bottom: 10px; font-size: 15px;">Description</div>
      <div class="message-body" style="font-size: 15px;"><?php echo htmlspecialchars($project['description']); ?></div>

      <?php if (!empty($gallery_images)): ?>
        <div class="meta-label" style="margin-top: 20px; margin-bottom: 15px; font-size: 15px;">Gallery Images</div>
        <div class="gallery-grid">
          <?php foreach ($gallery_images as $img): ?>
            <?php $galSrc = (strpos($img['image_url'], 'http') === 0) ? $img['image_url'] : '../../' . $img['image_url']; ?>
            <img src="<?php echo htmlspecialchars($galSrc); ?>" alt="Gallery Image" />
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div style="margin-top: auto; padding-top: 30px; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="projects.php?action=edit&id=<?php echo $project['id']; ?>" class="btn-gold">Edit Project</a>
        <a href="projects.php?action=delete&id=<?php echo $project['id']; ?>" class="btn-outline" style="color: #ff858d; border-color: rgba(220,53,69,0.3);" onclick="return confirm('Are you sure you want to delete this project?');">Delete</a>
      </div>
    </div>
    
  </div>

<!-- ── LIST VIEW ── -->
<?php elseif ($action === 'list'): ?>
  <?php
  try {
      $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
      $projects = $stmt->fetchAll();
  } catch (\PDOException $e) {
      echo "<div class='alert alert-danger'>Error loading projects: " . $e->getMessage() . "</div>";
      $projects = [];
  }
  ?>

  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th class="col-image">Image</th>
          <th class="col-name">Project Name</th>
          <th class="col-category">Category</th>
          <th class="col-location">Location</th>
          <th class="col-year">Year</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($projects) > 0): ?>
          <?php foreach ($projects as $proj): ?>
            <tr style="cursor: pointer;" onclick="window.location='projects.php?action=view&id=<?php echo $proj['id']; ?>'">
              <td class="col-image">
                <?php if ($proj['image_url']): ?>
                  <?php 
                    $src = (strpos($proj['image_url'], 'http') === 0) ? $proj['image_url'] : '../../' . $proj['image_url'];
                  ?>
                  <img src="<?php echo htmlspecialchars($src); ?>" alt="Project thumbnail" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);" />
                <?php else: ?>
                  <span style="font-size: 11px; color: rgba(26,26,26,0.4);">No Image</span>
                <?php endif; ?>
              </td>
              <td class="col-name" style="font-weight: 500; font-size: 15px;"><?php echo htmlspecialchars($proj['name']); ?></td>
              <td class="col-category"><span class="badge badge-info"><?php echo htmlspecialchars($proj['category']); ?></span></td>
              <td class="col-location"><?php echo htmlspecialchars($proj['location']); ?></td>
              <td class="col-year"><?php echo htmlspecialchars($proj['year']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align: center; color: rgba(245,245,240,0.45); padding: 40px 0;">
              No projects found. Tap "Add New Project" to write your first portfolio entry.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<!-- ── ADD / EDIT FORM ── -->
<?php elseif ($action === 'add' || $action === 'edit'): ?>
  <form action="projects.php?action=<?php echo $action; ?><?php echo ($action === 'edit') ? '&id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="form-group full-width">
      <label for="name">Project Name <span style="color:var(--danger)">*</span></label>
      <input type="text" id="name" name="name" value="<?php echo ($project) ? htmlspecialchars($project['name']) : ''; ?>" placeholder="e.g. Skyline Residences" required>
    </div>
    
    <div class="form-group">
      <label for="category">Category <span style="color:var(--danger)">*</span></label>
      <select id="category" name="category" required>
        <option value="" disabled <?php echo (!$project) ? 'selected' : ''; ?>>Select category...</option>
        <option value="Residential" <?php echo ($project && $project['category'] === 'Residential') ? 'selected' : ''; ?>>Residential</option>
        <option value="Commercial" <?php echo ($project && $project['category'] === 'Commercial') ? 'selected' : ''; ?>>Commercial</option>
        <option value="Renovation" <?php echo ($project && $project['category'] === 'Renovation') ? 'selected' : ''; ?>>Renovation</option>
        <option value="Interior" <?php echo ($project && $project['category'] === 'Interior') ? 'selected' : ''; ?>>Interior</option>
      </select>
    </div>
    
    <div class="form-group">
      <label for="year">Completion Year <span style="color:var(--danger)">*</span></label>
      <input type="text" id="year" name="year" value="<?php echo ($project) ? htmlspecialchars($project['year']) : ''; ?>" placeholder="e.g. 2024" required>
    </div>
    
    <div class="form-group">
      <label for="location">Location <span style="color:var(--danger)">*</span></label>
      <input type="text" id="location" name="location" value="<?php echo ($project) ? htmlspecialchars($project['location']) : ''; ?>" placeholder="e.g. Mumbai, MH" required>
    </div>
    
    <div class="form-group">
      <label for="sq_ft">Size / Sq. Ft.</label>
      <input type="text" id="sq_ft" name="sq_ft" value="<?php echo ($project) ? htmlspecialchars($project['sq_ft']) : ''; ?>" placeholder="e.g. 1.8M or 6,000">
    </div>
    
    <div class="form-group">
      <label for="floors">Floors Count</label>
      <input type="text" id="floors" name="floors" value="<?php echo ($project) ? htmlspecialchars($project['floors']) : ''; ?>" placeholder="e.g. 24 (or leave blank)">
    </div>
    
    <div class="form-group">
      <label for="units">Units Count</label>
      <input type="text" id="units" name="units" value="<?php echo ($project) ? htmlspecialchars($project['units']) : ''; ?>" placeholder="e.g. 180 (or leave blank)">
    </div>
    
    <div class="form-group full-width">
      <label for="description">Project Description <span style="color:var(--danger)">*</span></label>
      <textarea id="description" name="description" rows="6" placeholder="Write a detailed description of the project..." required><?php echo ($project) ? htmlspecialchars($project['description']) : ''; ?></textarea>
    </div>
    
    <div class="form-group full-width" style="border-top: 1px solid rgba(245,245,240,0.1); padding-top: 20px;">
      <label>Cover Image (Required)</label>
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 5px;">
        <div>
          <label for="image_file" style="color: rgba(245,245,240,0.6); font-size:12px;">Upload Cover Image:</label>
          <input type="file" id="image_file" name="image_file" accept="image/*" style="display:block; margin-top:8px;">
        </div>
        <div>
          <label for="image_url" style="color: rgba(245,245,240,0.6); font-size:12px;">Or Paste URL:</label>
          <input type="url" id="image_url" name="image_url" value="<?php echo ($project) ? htmlspecialchars($project['image_url']) : ''; ?>" placeholder="e.g. https://images.unsplash.com/photo-..." style="margin-top:8px;">
        </div>
      </div>

      <?php if ($project && $project['image_url']): ?>
        <div style="margin-top: 10px; font-size: 13px; color: rgba(245,245,240,0.6);">
          Current Cover Image:<br/>
          <?php 
            $current_src = (strpos($project['image_url'], 'http') === 0) ? $project['image_url'] : '../../' . $project['image_url'];
          ?>
          <img src="<?php echo htmlspecialchars($current_src); ?>" alt="Current cover" style="max-width: 200px; margin-top: 5px; border-radius: 4px; border: 1px solid var(--border);">
        </div>
      <?php endif; ?>
    </div>

    <!-- Gallery Images Upload Section -->
    <div class="form-group full-width" style="margin-top: 10px;">
      <label>Gallery Images (Optional)</label>
      <div style="margin-top: 5px;">
        <label for="gallery_files" style="color: rgba(245,245,240,0.6); font-size:12px;">Upload Multiple Additional Images (Hold CTRL/CMD to select multiple):</label>
        <input type="file" id="gallery_files" name="gallery_files[]" multiple accept="image/*" style="display:block; margin-top:8px;">
      </div>
      
      <!-- Display Existing Gallery Images -->
      <?php if (!empty($gallery_images)): ?>
        <div style="margin-top: 15px;">
          <div style="font-size: 13px; color: rgba(245,245,240,0.6); margin-bottom: 10px;">Current Gallery Images:</div>
          <div style="display: flex; flex-wrap: wrap; gap: 15px;">
            <?php foreach ($gallery_images as $img): ?>
              <div style="position: relative; width: 120px;">
                <?php $galSrc = (strpos($img['image_url'], 'http') === 0) ? $img['image_url'] : '../../' . $img['image_url']; ?>
                <img src="<?php echo htmlspecialchars($galSrc); ?>" style="width: 120px; height: 90px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);">
                <a href="projects.php?action=delete_image&image_id=<?php echo $img['id']; ?>&id=<?php echo $id; ?>" 
                   onclick="return confirm('Delete this gallery image?');"
                   style="position: absolute; top: 4px; right: 4px; background: rgba(220,53,69,0.9); color: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 14px;" title="Delete">
                   &times;
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="full-width form-actions">
      <a href="projects.php" class="btn-outline">Cancel</a>
      <button type="submit" class="btn-gold">Save Project</button>
    </div>
  </form>
<?php endif; ?>

<?php
include 'layout_bottom.php';
?>
