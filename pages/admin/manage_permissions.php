<?php
$adminTitle = "Manage Permissions";
$activeTab = "manage_admins";
include 'layout_top.php';

$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid Admin ID.</div>";
    include 'layout_bottom.php';
    exit;
}

$admin_id = (int)$_GET['id'];

// Handle actions (edit, delete, update permissions)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Check if we are interacting with the superadmin (id=1 might be protected)
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$admin_id]);
    $target_admin = $stmt->fetch();
    
    if (!$target_admin) {
        $error = "Admin not found.";
    } else {
        if ($_POST['action'] === 'update_permissions') {
            $new_permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
            $is_superadmin_input = isset($_POST['is_superadmin']) ? 1 : 0;
            
            // If editing own account or if it is the Ultimate Admin, forcefully retain current super admin status
            if ($target_admin['username'] === $_SESSION['admin_username'] || $admin_id === 1) {
                $is_superadmin_input = $target_admin['is_superadmin'];
            }
            
            try {
                $perms_json = json_encode($new_permissions);
                $update_stmt = $pdo->prepare("UPDATE admin_users SET is_superadmin = ?, permissions = ? WHERE id = ?");
                $update_stmt->execute([$is_superadmin_input, $perms_json, $admin_id]);
                $success = "Permissions updated successfully.";
            } catch (\PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        } 
        elseif ($_POST['action'] === 'edit_admin') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $is_superadmin_input = isset($_POST['is_superadmin']) ? 1 : 0;
            
            // If editing own account or if it is the Ultimate Admin, forcefully retain current super admin status
            if ($target_admin['username'] === $_SESSION['admin_username'] || $admin_id === 1) {
                $is_superadmin_input = $target_admin['is_superadmin'];
            }
            
            if (!empty($username)) {
                try {
                    if (!empty($password)) {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ?, password = ?, is_superadmin = ? WHERE id = ?");
                        $stmt->execute([$username, $email, $hashed, $is_superadmin_input, $admin_id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ?, is_superadmin = ? WHERE id = ?");
                        $stmt->execute([$username, $email, $is_superadmin_input, $admin_id]);
                    }
                    $success = "Admin details updated successfully.";
                } catch (\PDOException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        $error = "Username already exists.";
                    } else {
                        $error = "Database error: " . $e->getMessage();
                    }
                }
            } else {
                $error = "Username is required.";
            }
        }
        elseif ($_POST['action'] === 'delete_admin') {
            if ($target_admin['username'] === $_SESSION['admin_username']) {
                $error = "You cannot delete your own account.";
            } elseif ($admin_id === 1) {
                $error = "Security Block: The Ultimate Super Admin (Founder) account cannot be deleted by anyone.";
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
                    $stmt->execute([$admin_id]);
                    // Redirect to admins page
                    header("Location: admins.php");
                    exit;
                } catch (\PDOException $e) {
                    $error = "Error deleting admin: " . $e->getMessage();
                }
            }
        }
    }
}

// Fetch the admin info again to get latest data
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$target_admin = $stmt->fetch();

if (!$target_admin) {
    echo "<div class='alert alert-danger'>Admin not found.</div>";
    include 'layout_bottom.php';
    exit;
}

$current_permissions = $target_admin['permissions'] ? json_decode($target_admin['permissions'], true) : [];
if (!is_array($current_permissions)) $current_permissions = [];

$all_tabs = [
    'dashboard' => 'Dashboard',
    'projects' => 'Projects',
    'services' => 'Services',
    'blogs' => 'Blogs',
    'messages' => 'Messages',
    'security' => 'Security',
    'manage_admins' => 'Manage Admins'
];

$is_self_editing = ($target_admin['username'] === $_SESSION['admin_username']);
$is_ultimate_admin = ($admin_id === 1);
$lock_superadmin_toggle = ($is_self_editing || ($is_ultimate_admin && !$is_self_editing));
?>

<style>
  .admin-detail-item {
    margin-bottom: 15px;
  }
  .admin-detail-label {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #888;
    margin-bottom: 4px;
  }
  .admin-detail-value {
    font-size: 1.1rem;
    font-weight: 500;
    color: #222;
  }
  
  /* Modal Styles */
  .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    align-items: center;
    justify-content: center;
  }
  .modal.active { display: flex; }
  .modal-content {
    background-color: var(--white);
    border-radius: 8px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    animation: slideIn 0.3s ease;
    overflow: hidden;
  }
  @keyframes slideIn {
    from { transform: translateY(-30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  .modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid var(--border);
    background-color: #fafafa;
  }
  .modal-title { margin: 0; font-size: 1.25rem; color: #111; }
  .modal-close {
    background: none; border: none; font-size: 1.5rem; line-height: 1;
    color: #888; cursor: pointer; transition: color 0.2s; padding: 0;
  }
  .modal-close:hover { color: var(--danger, #dc3545); }
  .modal-body { padding: 25px; }

  /* Grid layout for card */
  .card-grid {
    display: flex;
    flex-wrap: wrap;
  }
  .card-col-left {
    flex: 1 1 60%;
    min-width: 300px;
    padding: 30px;
    border-right: 1px solid var(--border);
  }
  .card-col-right {
    flex: 1 1 35%;
    min-width: 250px;
    padding: 30px;
    background-color: #fafafa;
  }

  @media (max-width: 768px) {
    .card-col-left { border-right: none; border-bottom: 1px solid var(--border); }
  }
  
  /* Hover effect for permission items */
  .perm-label:hover {
    background: rgba(212, 160, 23, 0.05) !important;
    border-color: rgba(212, 160, 23, 0.3) !important;
  }
</style>

<div class="content-header">
  <div class="content-title">
    <h1>Manage <span>Admin</span></h1>
    <p>View details and manage permissions for: <strong style="color:var(--gold);"><?php echo htmlspecialchars($target_admin['username']); ?></strong></p>
  </div>
  <div>
    <a href="admins.php" class="btn-outline">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" style="margin-right:8px; vertical-align:middle;"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to Admins
    </a>
  </div>
</div>

<?php if($error): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if($success): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="card" style="width: 100%; padding: 0; overflow: hidden;">
  <div class="card-grid">
    
    <!-- LEFT COLUMN: Permissions -->
    <div class="card-col-left">
      <h2 style="font-size: 1.4rem; margin-bottom: 25px; color: #111;">Access Control</h2>
      
      <form method="POST" action="manage_permissions.php?id=<?php echo $admin_id; ?>">
        <input type="hidden" name="action" value="update_permissions">
        
        <div class="form-group" style="background: rgba(212, 160, 23, 0.05); padding: 20px; border-radius: 8px; border: 1px solid rgba(212, 160, 23, 0.2); margin-bottom: 30px;">
          <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; font-size: 1.15rem; color: var(--gold);">
            <input type="checkbox" id="superAdminToggle" name="is_superadmin" value="1" <?php if($target_admin['is_superadmin']) echo 'checked'; ?> <?php if($lock_superadmin_toggle) echo 'disabled'; ?> style="width: 20px; height: 20px; margin-right: 12px; accent-color: var(--gold); cursor: pointer;">
            Grant Super Admin Access
          </label>
          <p style="margin-top: 10px; font-size: 0.9rem; color: #777; margin-left: 32px; line-height: 1.5;">
            Super Admins have full administrative privileges and can access all sections of the panel, overriding any individual tab restrictions.
          </p>
        </div>

        <h3 style="margin-bottom: 20px; font-size: 1.1rem; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px;">Individual Tab Permissions</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-bottom: 35px;">
          <?php foreach ($all_tabs as $tab_id => $tab_name): ?>
            <label class="perm-label" style="display: flex; align-items: center; cursor: pointer; padding: 12px 15px; background: #fff; border: 1px solid #eaeaea; border-radius: 6px; transition: all 0.2s;">
              <input type="checkbox" class="tab-permission-checkbox" name="permissions[]" value="<?php echo $tab_id; ?>" <?php if(in_array($tab_id, $current_permissions)) echo 'checked'; ?> style="width: 18px; height: 18px; margin-right: 12px; accent-color: var(--gold); cursor: pointer;">
              <span style="font-weight: 500; color: #444;"><?php echo $tab_name; ?></span>
            </label>
          <?php endforeach; ?>
        </div>

        <div>
          <button type="submit" class="btn-gold" style="padding: 12px 25px; font-size: 1.05rem;">Save Permissions</button>
        </div>
      </form>
    </div>
    
    <!-- RIGHT COLUMN: Admin Details -->
    <div class="card-col-right">
      <h2 style="font-size: 1.4rem; margin-bottom: 25px; color: #111;">Admin Details</h2>
      
      <div class="admin-detail-item">
        <div class="admin-detail-label">Username</div>
        <div class="admin-detail-value"><?php echo htmlspecialchars($target_admin['username']); ?></div>
      </div>
      
      <div class="admin-detail-item">
        <div class="admin-detail-label">Email</div>
        <div class="admin-detail-value"><?php echo htmlspecialchars($target_admin['email'] ?? 'Not provided'); ?></div>
      </div>
      
      <div class="admin-detail-item">
        <div class="admin-detail-label">Role</div>
        <div class="admin-detail-value">
            <?php if($target_admin['is_superadmin']): ?>
                <span class="badge badge-success">Super Admin</span>
            <?php else: ?>
                <span class="badge" style="background:var(--border); color:var(--gold);">Admin</span>
            <?php endif; ?>
        </div>
      </div>

      <div class="admin-detail-item">
        <div class="admin-detail-label">Created At</div>
        <div class="admin-detail-value"><?php echo date('M d, Y h:i A', strtotime($target_admin['created_at'])); ?></div>
      </div>
      
      <div style="margin-top: 40px; display: flex; gap: 10px; flex-wrap: wrap;">
        <button class="btn-outline" style="flex: 1;" onclick="document.getElementById('editAdminModal').classList.add('active')">Edit Profile</button>
        
        <?php if(!$is_self_editing && !$is_ultimate_admin): ?>
        <form method="POST" action="manage_permissions.php?id=<?php echo $admin_id; ?>" style="flex: 1;" onsubmit="return confirm('Are you sure you want to permanently delete this admin? This action cannot be undone.');">
          <input type="hidden" name="action" value="delete_admin">
          <button type="submit" class="btn-outline" style="width: 100%; color: #dc3545; border-color: rgba(220,53,69,0.3);">Delete</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    
  </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal" id="editAdminModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title">Edit Admin Details</h3>
      <button class="modal-close" onclick="document.getElementById('editAdminModal').classList.remove('active')">&times;</button>
    </div>
    <div class="modal-body">
      <form method="POST" action="manage_permissions.php?id=<?php echo $admin_id; ?>">
        <input type="hidden" name="action" value="edit_admin">
        
        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" name="username" value="<?php echo htmlspecialchars($target_admin['username']); ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" value="<?php echo htmlspecialchars($target_admin['email'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
          <label class="form-label">Password <span style="font-size: 0.8rem; font-weight: normal; color: #888;">(Leave blank to keep current)</span></label>
          <input type="password" name="password">
        </div>

        <div class="form-group">
          <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500;">
            <input type="checkbox" name="is_superadmin" value="1" <?php if($target_admin['is_superadmin']) echo 'checked'; ?> <?php if($lock_superadmin_toggle) echo 'disabled'; ?> style="width: 16px; height: 16px; margin-right: 10px; accent-color: var(--gold);">
            Super Admin
          </label>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;">
          <button type="button" class="btn-outline" onclick="document.getElementById('editAdminModal').classList.remove('active')">Cancel</button>
          <button type="submit" class="btn-gold">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const superAdminToggle = document.getElementById('superAdminToggle');
  const tabCheckboxes = document.querySelectorAll('.tab-permission-checkbox');
  const permLabels = document.querySelectorAll('.perm-label');

  // Store original checked states in case they uncheck superadmin
  const originalStates = {};
  tabCheckboxes.forEach((cb, index) => {
    originalStates[index] = cb.checked;
    
    // Add click listener to update original states if not superadmin
    cb.addEventListener('change', function() {
      if (!superAdminToggle.checked) {
        originalStates[index] = this.checked;
      }
    });
  });

  function updateCheckboxes() {
    if (superAdminToggle.checked) {
      tabCheckboxes.forEach((cb, index) => {
        cb.checked = true;
        // Make them unclickable but still submittable
        cb.style.pointerEvents = 'none';
        // Gray out the parent label
        permLabels[index].style.opacity = '0.5';
        permLabels[index].style.pointerEvents = 'none';
        permLabels[index].style.background = '#f5f5f5';
      });
    } else {
      tabCheckboxes.forEach((cb, index) => {
        // Restore original state
        cb.checked = originalStates[index];
        cb.style.pointerEvents = 'auto';
        permLabels[index].style.opacity = '1';
        permLabels[index].style.pointerEvents = 'auto';
        permLabels[index].style.background = '#fff';
      });
    }
  }

  // Run on load
  updateCheckboxes();

  // Run on toggle
  superAdminToggle.addEventListener('change', updateCheckboxes);
});
</script>

<?php include 'layout_bottom.php'; ?>
