<?php
$adminTitle = "Manage Admins";
$activeTab = "manage_admins";
include 'layout_top.php';

$error = '';
$success = '';

// Handle add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_admin') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    
    if (!empty($username) && !empty($password)) {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $is_superadmin = ($role === 'superadmin') ? 1 : 0;
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password, is_superadmin, permissions) VALUES (?, ?, ?, ?, '[]')");
            $stmt->execute([$username, $email, $hashed, $is_superadmin]);
            $success = "Admin added successfully.";
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error = "Username already exists.";
            } else {
                $error = "Database error: " . $e->getMessage();
            }
        }
    } else {
        $error = "Please fill all required fields.";
    }
}

// Fetch all admins
try {
    $admins = $pdo->query("SELECT id, username, email, is_superadmin, created_at FROM admin_users ORDER BY id ASC")->fetchAll();
} catch (\PDOException $e) {
    $admins = [];
    $error = "Failed to load admins: " . $e->getMessage();
}
?>

<div class="content-header">
  <div class="content-title">
    <h1>Manage <span>Admins</span></h1>
    <p>Add and manage admin users and their permissions.</p>
  </div>
  <div>
    <button class="btn-gold" onclick="document.getElementById('addAdminModal').classList.add('active')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" style="margin-right:8px; vertical-align:middle;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      Add Admin
    </button>
  </div>
</div>

<?php if($error): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if($success): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h2 class="card-title">Admin List</h2>
  </div>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Created At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($admins)): ?>
        <tr><td colspan="6">No admins found.</td></tr>
        <?php else: ?>
          <?php foreach($admins as $admin): ?>
          <tr style="cursor: pointer;" onclick="window.location.href='manage_permissions.php?id=<?php echo $admin['id']; ?>'">
            <td>#<?php echo $admin['id']; ?></td>
            <td><?php echo htmlspecialchars($admin['username']); ?></td>
            <td><?php echo htmlspecialchars($admin['email'] ?? '-'); ?></td>
            <td>
                <?php if($admin['is_superadmin']): ?>
                    <span class="badge badge-success">Super Admin</span>
                <?php else: ?>
                    <span class="badge" style="background:var(--border); color:var(--gold);">Admin</span>
                <?php endif; ?>
            </td>
            <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
            <td>
              <a href="manage_permissions.php?id=<?php echo $admin['id']; ?>" class="btn-outline" style="padding: 4px 10px; font-size:12px;">Manage Permissions</a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Admin Modal -->
<div class="modal" id="addAdminModal">
  <div class="modal-content" style="max-width: 500px;">
    <div class="modal-header">
      <h3 class="modal-title">Add New Admin</h3>
      <button class="modal-close" onclick="document.getElementById('addAdminModal').classList.remove('active')">&times;</button>
    </div>
    <div class="modal-body">
      <form method="POST" action="admins.php">
        <input type="hidden" name="action" value="add_admin">
        
        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" name="username" required>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email">
        </div>
        
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" required>
        </div>

        <div class="form-group">
          <label class="form-label">Role</label>
          <select name="role">
            <option value="admin">Admin</option>
            <option value="superadmin">Super Admin</option>
          </select>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
          <button type="button" class="btn-outline" onclick="document.getElementById('addAdminModal').classList.remove('active')">Cancel</button>
          <button type="submit" class="btn-gold">Add Admin</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  tr:hover {
    background-color: rgba(212, 160, 23, 0.05);
  }
  
  /* Modal Styles */
  .modal {
    display: none; /* Hidden by default */
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

  .modal.active {
    display: flex;
  }

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

  .modal-title {
    margin: 0;
    font-size: 1.25rem;
    color: #111;
  }

  .modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    line-height: 1;
    color: #888;
    cursor: pointer;
    transition: color 0.2s;
    padding: 0;
  }

  .modal-close:hover {
    color: var(--danger, #dc3545);
  }

  .modal-body {
    padding: 25px;
  }
</style>

<?php include 'layout_bottom.php'; ?>
