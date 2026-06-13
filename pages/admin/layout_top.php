<?php
session_start();

// Auth check
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = substr($scriptName, 0, strpos($scriptName, 'pages/admin/'));
    $adminBase = rtrim($basePath, '/') . '/admin/';
    header("Location: " . $adminBase . "login.php");
    exit;
}

require_once 'db.php';

// Fetch user permissions
$admin_permissions = [];
$is_superadmin = 0;
if (isset($_SESSION['admin_username'])) {
    $stmt = $pdo->prepare("SELECT is_superadmin, permissions FROM admin_users WHERE username = ?");
    $stmt->execute([$_SESSION['admin_username']]);
    $user_data = $stmt->fetch();
    if ($user_data) {
        $is_superadmin = $user_data['is_superadmin'];
        $admin_permissions = $user_data['permissions'] ? json_decode($user_data['permissions'], true) : [];
        if (!is_array($admin_permissions)) $admin_permissions = [];
    }
}

function has_permission($tab) {
    global $is_superadmin, $admin_permissions;
    if ($is_superadmin) return true;
    return in_array($tab, $admin_permissions);
}

// Check access for current tab
if (!isset($activeTab)) {
    $activeTab = '';
}
if (!$is_superadmin && $activeTab !== '' && !has_permission($activeTab)) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = substr($scriptName, 0, strpos($scriptName, 'pages/admin/'));
    $basePath = rtrim($basePath, '/');
    header("Location: " . $basePath . "/error.php?code=403");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, 'pages/admin/'));
$adminBase = rtrim($basePath, '/') . '/admin/';
?>
  <base href="<?php echo htmlspecialchars($adminBase); ?>">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($adminTitle) ? $adminTitle : 'Admin Dashboard'; ?> - Erix Construction</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=Barlow+Condensed:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --black:   #0d0d0d;
      --gold:    #D4A017;
      --gold-lt: #e8b82a;
      --cream:   #F5F5F0;
      --white:   #ffffff;
      --dark-gray: #161616;
      --card-bg: #ffffff;
      --border: rgba(212, 160, 23, 0.2);
      --success: #28a745;
      --danger: #dc3545;
      --sidebar-w: 260px;
    }
    
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      background: var(--cream);
      color: #1a1a1a;
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      display: flex;
    }
    
    /* Sidebar Navigation */
    .sidebar {
      width: var(--sidebar-w);
      background: var(--dark-gray);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      height: 100vh;
      position: fixed;
      left: 0; top: 0;
      z-index: 100;
    }
    
    .sidebar-brand {
      padding: 30px 25px;
      border-bottom: 1px solid var(--border);
    }
    
    .logo {
      display: flex;
      align-items: baseline;
      gap: 3px;
      text-decoration: none;
    }
    
    .logo-e {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 32px;
      color: var(--gold);
      letter-spacing: 2px;
    }
    
    .logo-rix {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 32px;
      color: var(--white);
      letter-spacing: 2px;
    }
    
    .logo-dot {
      width: 5px;
      height: 5px;
      background: var(--gold);
      border-radius: 50%;
    }
    
    .sidebar-menu {
      list-style: none;
      padding: 30px 15px;
      flex-grow: 1;
    }
    
    .sidebar-menu li {
      margin-bottom: 10px;
    }
    
    .sidebar-menu a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 15px;
      color: rgba(245,245,240,0.7);
      text-decoration: none;
      border-radius: 6px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 14px;
      font-weight: 500;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      transition: all 0.3s;
    }
    
    .sidebar-menu a:hover, .sidebar-menu a.active {
      color: var(--gold);
      background: rgba(212, 160, 23, 0.08);
    }
    
    .sidebar-menu a svg {
      transition: transform 0.3s;
    }
    
    .sidebar-menu a:hover svg {
      transform: scale(1.1);
    }
    
    .sidebar-footer {
      padding: 20px 25px;
      border-top: 1px solid var(--border);
      font-size: 13px;
      color: rgba(245,245,240,0.45);
    }
    
    .user-tag {
      font-weight: 600;
      color: var(--gold);
    }
    
    /* Main Content Area */
    .main-content {
      margin-left: var(--sidebar-w);
      flex-grow: 1;
      padding: 40px;
      min-height: 100vh;
      overflow-y: auto;
    }
    
    .content-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 35px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 20px;
    }
    
    .content-title h1 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 38px;
      letter-spacing: 1.5px;
      color: #1a1a1a;
    }
    
    .content-title h2 {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 28px;
      letter-spacing: 1.5px;
      color: #1a1a1a;
    }
    
    .content-title h1 span,
    .content-title h2 span,
    .action-panel h2 span,
    .security-col h3 span {
      color: var(--gold);
    }
    
    .content-title p {
      font-size: 14px;
      color: rgba(26,26,26,0.6);
      margin-top: 5px;
      display: block;
    }
    
    /* General Admin Layout UI Elements */
    .btn-gold {
      background: var(--gold);
      color: var(--black);
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .btn-gold:hover {
      background: var(--gold-lt);
      transform: translateY(-1px);
    }
    
    .btn-outline {
      border: 1px solid var(--border);
      background: transparent;
      color: #1a1a1a;
      padding: 10px 20px;
      border-radius: 4px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 14px;
      font-weight: 500;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .btn-outline:hover {
      border-color: var(--gold);
      color: var(--gold);
    }
    
    /* Tables */
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 8px;
      margin-bottom: 30px;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }
    
    th {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 14px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: #0d0d0d;
      background: #EBE5D3;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
    }
    
    td {
      padding: 16px 20px;
      border-bottom: 1px solid rgba(212,160,23,0.08);
      font-size: 14px;
      color: #1a1a1a;
      vertical-align: middle;
    }
    
    tr:last-child td {
      border-bottom: none;
    }
    
    tr:hover td {
      background: rgba(212,160,23,0.04);
    }
    
    /* Badges */
    .badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .badge-success { background: rgba(40,167,69,0.1); color: #1e7e34; border: 1px solid rgba(40,167,69,0.25); }
    .badge-info { background: rgba(23,162,184,0.1); color: #117a8b; border: 1px solid rgba(23,162,184,0.25); }
    .badge-warning { background: rgba(255,193,7,0.1); color: #b58900; border: 1px solid rgba(255,193,7,0.25); }
    
    /* Action links inside table */
    .action-links {
      display: flex;
      gap: 12px;
    }
    
    .action-edit {
      color: #007bff;
      text-decoration: none;
      font-size: 13px;
      font-weight: 500;
    }
    
    .action-edit:hover { text-decoration: underline; }
    
    .action-delete {
      color: #dc3545;
      text-decoration: none;
      font-size: 13px;
      font-weight: 500;
    }
    
    .action-delete:hover { text-decoration: underline; }

    /* Icon-only action buttons (messages table) */
    .action-icon-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      border-radius: 7px;
      text-decoration: none;
      transition: background 0.18s, transform 0.15s, box-shadow 0.18s;
      flex-shrink: 0;
    }
    .action-icon-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .action-icon-view {
      color: #38bdf8;
      background: rgba(56,189,248,0.10);
      border: 1px solid rgba(56,189,248,0.22);
    }
    .action-icon-view:hover {
      background: rgba(56,189,248,0.20);
      border-color: rgba(56,189,248,0.45);
      color: #0ea5e9;
    }
    .action-icon-delete {
      color: #ef4444;
      background: rgba(239,68,68,0.09);
      border: 1px solid rgba(239,68,68,0.20);
    }
    .action-icon-delete:hover {
      background: rgba(239,68,68,0.18);
      border-color: rgba(239,68,68,0.40);
      color: #dc2626;
    }
    .action-links-desktop {
      display: flex;
      gap: 8px;
      align-items: center;
    }


    .admin-form {
      background: var(--card-bg);
      border: 1px solid var(--border);
      padding: 25px;
      border-radius: 8px;
      max-width: 900px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      column-gap: 20px;
      row-gap: 15px;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
    }

    .full-width {
      grid-column: 1 / -1;
    }
    
    .form-actions {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }
    
    .form-group label {
      display: block;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: rgba(26,26,26,0.8);
      margin-bottom: 8px;
    }
    
    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      background: #ffffff;
      border: 1px solid rgba(13,13,13,0.12);
      border-radius: 4px;
      padding: 12px;
      color: #1a1a1a;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      transition: all 0.3s;
    }
    
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
      outline: none;
      border-color: var(--gold);
      background: #ffffff;
      box-shadow: 0 0 10px rgba(212,160,23,0.15);
    }
    
    .password-wrapper {
      position: relative;
    }
    
    .password-wrapper input {
      padding-right: 42px;
    }
    
    .password-toggle-btn {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: rgba(26, 26, 26, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4px;
      transition: color 0.3s ease;
    }
    
    .password-toggle-btn:hover {
      color: var(--gold);
    }
    
    .password-toggle-btn svg {
      width: 18px;
      height: 18px;
    }
    
    /* Alert Messages */
    .alert {
      padding: 12px 16px;
      border-radius: 4px;
      margin-bottom: 25px;
      font-size: 14px;
    }
    
    .alert-success { background: rgba(40,167,69,0.15); border: 1px solid rgba(40,167,69,0.3); color: #82e095; }
    .alert-danger { background: rgba(220,53,69,0.15); border: 1px solid rgba(220,53,69,0.3); color: #ff858d; }
    
    /* Mobile Top Navbar */
    .mobile-navbar {
      display: none;
      height: 70px;
      background: var(--dark-gray);
      border-bottom: 1px solid var(--border);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 900;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .mobile-navbar .logo {
      display: flex;
      align-items: baseline;
      gap: 3px;
      text-decoration: none;
    }
    
    .menu-toggle {
      display: none;
      flex-direction: column;
      justify-content: space-between;
      width: 26px;
      height: 18px;
      background: transparent;
      border: none;
      cursor: pointer;
      z-index: 1100;
      padding: 0;
    }
    
    .menu-toggle span {
      display: block;
      height: 2px;
      width: 100%;
      background: var(--white);
      border-radius: 2px;
      transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }
    
    .menu-toggle.active span:nth-child(1) {
      transform: translateY(8px) rotate(45deg);
    }
    
    .menu-toggle.active span:nth-child(2) {
      opacity: 0;
      transform: scaleX(0);
    }
    
    .menu-toggle.active span:nth-child(3) {
      transform: translateY(-8px) rotate(-45deg);
    }
    
    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(4px);
      z-index: 950;
      opacity: 0;
      transition: opacity 0.4s ease;
      pointer-events: none;
    }
    
    .sidebar-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }

    @media (max-width: 992px) {
      .mobile-navbar {
        display: flex;
      }
      
      .menu-toggle {
        display: flex;
      }
      
      .sidebar-overlay {
        display: block;
      }
      
      .sidebar {
        left: auto;
        right: 0;
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        border-left: 1px solid var(--border);
        border-right: none;
        z-index: 1000;
      }
      
      .sidebar.active {
        transform: translateX(0);
      }
      
      .main-content {
        margin-left: 0;
        padding: 100px 20px 40px 20px;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .quick-actions {
        grid-template-columns: 1fr;
        gap: 20px;
      }
    }
    
    @media (max-width: 576px) {
      .content-title h1 {
        font-size: 28px !important;
      }
      
      .content-title h2 {
        font-size: 22px !important;
      }
      
      .btn-gold, .btn-outline {
        padding: 8px 14px !important;
        font-size: 12px !important;
        letter-spacing: 1px !important;
      }
      
      .admin-form {
        padding: 20px !important;
      }
      
      .form-row {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
      }
    }
    
    .sidebar-close-btn {
      display: none;
      background: transparent;
      border: none;
      color: rgba(245, 245, 240, 0.65);
      cursor: pointer;
      align-items: center;
      justify-content: center;
      padding: 4px;
      transition: color 0.3s;
      outline: none;
    }
    
    .sidebar-close-btn:hover {
      color: var(--gold);
    }
    
    .sidebar-close-btn svg {
      width: 22px;
      height: 22px;
    }
    
    @media (max-width: 992px) {
      .sidebar-close-btn {
        display: flex;
      }
      .sidebar-brand {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
      }
    }
  </style>
</head>
<body>

  <!-- Mobile Top Navbar -->
  <header class="mobile-navbar">
    <a href="index.php" class="logo">
      <span class="logo-e">E</span><span class="logo-rix">RIX</span>
      <span class="logo-dot"></span>
    </a>
    <button class="menu-toggle" id="mobile-menu-toggle" aria-label="Toggle Navigation">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </header>

  <!-- Sidebar Overlay -->
  <div class="sidebar-overlay" id="sidebar-overlay"></div>

  <!-- Sidebar Wrapper -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <a href="index.php" class="logo">
        <span class="logo-e">E</span><span class="logo-rix">RIX</span>
        <span class="logo-dot"></span>
      </a>
      <button class="sidebar-close-btn" id="mobile-menu-close" aria-label="Close Navigation">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>
    
    <ul class="sidebar-menu">
      <?php if (has_permission('dashboard')): ?>
      <li>
        <a href="index.php" <?php if(isset($activeTab) && $activeTab == 'dashboard') echo 'class="active"'; ?>>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </a>
      </li>
      <?php endif; ?>
      <?php if (has_permission('projects')): ?>
      <li>
        <a href="projects.php" <?php if(isset($activeTab) && $activeTab == 'projects') echo 'class="active"'; ?>>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
          Projects
        </a>
      </li>
      <?php endif; ?>
      <?php if (has_permission('services')): ?>
      <li>
        <a href="services.php" <?php if(isset($activeTab) && $activeTab == 'services') echo 'class="active"'; ?>>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
          Services
        </a>
      </li>
      <?php endif; ?>
      <?php if (has_permission('blogs')): ?>
      <li>
        <a href="blogs.php" <?php if(isset($activeTab) && $activeTab == 'blogs') echo 'class="active"'; ?>>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          Blogs
        </a>
      </li>
      <?php endif; ?>
      <?php if (has_permission('messages')): ?>
      <li>
        <a href="messages.php" <?php if(isset($activeTab) && $activeTab == 'messages') echo 'class="active"'; ?>>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          Leads
        </a>
      </li>
      <?php endif; ?>
      <?php if (has_permission('security')): ?>
      <li>
        <a href="security.php" <?php if(isset($activeTab) && $activeTab == 'security') echo 'class="active"'; ?>>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
          Security
        </a>
      </li>
      <?php endif; ?>
      <?php if (has_permission('manage_admins')): ?>
      <li>
        <a href="admins.php" <?php if(isset($activeTab) && $activeTab == 'manage_admins') echo 'class="active"'; ?>>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
          Manage Admins
        </a>
      </li>
      <?php endif; ?>
      <li style="margin-top: 30px;">
        <a href="logout.php" style="color: #ff858d;">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 01-2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
          Logout
        </a>
      </li>
    </ul>
    
    <div class="sidebar-footer">
      <div>Logged in as: <span class="user-tag"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span></div>
    </div>
  </aside>

  <!-- Main Content Wrapper -->
  <main class="main-content">
