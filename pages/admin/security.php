<?php
$adminTitle = "Security Settings";
$activeTab = "security";
include 'layout_top.php';

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_username = trim($_POST['new_username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $username = $_SESSION['admin_username'];

    if (empty($current_password)) {
        $msg = "Please verify your identity by entering your current password.";
        $msgType = "danger";
    } else {
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($current_password, $user['password'])) {
                $updates = [];
                $params = [];
                $success_parts = [];

                // Handle Username Change
                if (!empty($new_username) && $new_username !== $username) {
                    // Check if new username is already taken
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ? AND id != ?");
                    $checkStmt->execute([$new_username, $user['id']]);
                    if ($checkStmt->fetchColumn() > 0) {
                        throw new Exception("Username '$new_username' is already in use.");
                    }
                    $updates[] = "username = ?";
                    $params[] = $new_username;
                    $success_parts[] = "username updated";
                }

                // Handle Password Change
                if (!empty($new_password)) {
                    if (strlen($new_password) < 6) {
                        throw new Exception("New password must be at least 6 characters long.");
                    }
                    if ($new_password !== $confirm_password) {
                        throw new Exception("New passwords do not match.");
                    }
                    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $updates[] = "password = ?";
                    $params[] = $new_hash;
                    $success_parts[] = "password updated";
                }

                if (count($updates) > 0) {
                    $sql = "UPDATE admin_users SET " . implode(", ", $updates) . " WHERE id = ?";
                    $params[] = $user['id'];
                    
                    $updateStmt = $pdo->prepare($sql);
                    $updateStmt->execute($params);

                    // Update session variables if username changed
                    if (!empty($new_username) && $new_username !== $username) {
                        $_SESSION['admin_username'] = $new_username;
                    }

                    $msg = "Success: " . implode(" and ", $success_parts) . " successfully.";
                    $msgType = "success";
                } else {
                    $msg = "No changes were made.";
                    $msgType = "warning";
                }

            } else {
                $msg = "Incorrect current password.";
                $msgType = "danger";
            }
        } catch (Exception $e) {
            $msg = "Error: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}
?>

<style>
  .security-page-container {
    max-width: 800px;
    margin: 0 auto;
    padding-top: 10px;
  }
  
  .security-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    padding: 26px;
    border-radius: 8px;
    width: 100%;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
  }
  
  .security-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 26px;
  }
  
  .security-col h3 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px;
    letter-spacing: 1px;
    color: #1a1a1a;
    display: table;
    margin-bottom: 12px;
    border-bottom: 1px solid rgba(212, 160, 23, 0.08);
    padding-bottom: 6px;
  }
  
  .security-col p {
    font-size: 12px;
    color: rgba(26,26,26,0.6);
    margin-bottom: 12px;
    line-height: 1.4;
  }
  
  .security-card .form-group {
    margin-bottom: 15px;
  }
  
  .security-card .form-group label {
    font-size: 12px;
    margin-bottom: 4px;
    color: rgba(26,26,26,0.8);
    letter-spacing: 1px;
  }
  
  .security-card .form-group input {
    font-size: 14px;
    padding: 10px 14px;
    border-radius: 4px;
  }
  
  .security-card .btn-gold, .security-card .btn-outline {
    padding: 10px 18px;
    font-size: 14px;
    border-radius: 4px;
  }
  
  .security-card .password-toggle-btn {
    right: 12px;
  }
  
  .security-card .password-toggle-btn svg {
    width: 18px;
    height: 18px;
  }
  
  @media (max-width: 768px) {
    .security-grid {
      grid-template-columns: 1fr;
      gap: 15px;
    }
  }
</style>

<div class="content-header">
  <div class="content-title">
    <h1>Security <span>Settings</span></h1>
    <p>Update your administrator credentials and login password here.</p>
  </div>
</div>

<div class="security-page-container">

  <?php if ($msg): ?>
    <div class="alert alert-<?php echo $msgType; ?>" style="margin-bottom: 20px;">
      <?php echo htmlspecialchars($msg); ?>
    </div>
  <?php endif; ?>

  <form action="security.php" method="POST" class="security-card">
    <div class="security-grid">
      <!-- Left Column: Username & Auth -->
      <div class="security-col">
        <h3>Account <span>Username</span></h3>
        
        <div class="form-group">
          <label for="current_username">Current Username</label>
          <input type="text" id="current_username" value="<?php echo htmlspecialchars($_SESSION['admin_username']); ?>" disabled style="opacity: 0.5; cursor: not-allowed;">
        </div>

        <div class="form-group">
          <label for="new_username">Change Username</label>
          <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($_SESSION['admin_username']); ?>" placeholder="Enter new username" required>
        </div>

        <h3 style="margin-top: 25px;">Authorize <span>Changes</span></h3>
        <div class="form-group">
          <label for="current_password" style="color: var(--gold);">Current Password <span style="color:var(--danger)">*</span></label>
          <p>Verify your current password to authorize these modifications.</p>
          <div class="password-wrapper">
            <input type="password" id="current_password" name="current_password" placeholder="Enter password to authorize" required>
            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('current_password', this)" tabindex="-1" title="Show/Hide Password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Right Column: Password Change -->
      <div class="security-col">
        <h3>Change <span>Password</span></h3>
        <p>Leave password fields blank if you only wish to change your username.</p>
        
        <div class="form-group">
          <label for="new_password">New Password</label>
          <div class="password-wrapper">
            <input type="password" id="new_password" name="new_password" placeholder="Min. 6 characters">
            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('new_password', this)" tabindex="-1" title="Show/Hide Password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
        
        <div class="form-group" style="margin-top: 15px;">
          <label for="confirm_password">Confirm New Password</label>
          <div class="password-wrapper">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter new password">
            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('confirm_password', this)" tabindex="-1" title="Show/Hide Password">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div style="border-top: 1px solid rgba(212, 160, 23, 0.08); margin-top: 20px; padding-top: 15px; display: flex; justify-content: flex-end; gap: 12px;">
      <a href="index.php" class="btn-outline">Cancel</a>
      <button type="submit" class="btn-gold">Save Settings</button>
    </div>
  </form>

</div>

<script>
  function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    
    const eyeIcon = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
    const eyeOffIcon = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
    
    btn.innerHTML = isPassword ? eyeOffIcon : eyeIcon;
  }
</script>

<?php
include 'layout_bottom.php';
?>
