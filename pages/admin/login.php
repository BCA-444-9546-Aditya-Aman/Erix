<?php
session_start();
require_once 'db.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged'] = true;
                $_SESSION['admin_username'] = $user['username'];
                header("Location: index.php");
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (\PDOException $e) {
            $error = 'Database error occurred: ' . $e->getMessage();
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Erix Construction</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=Barlow+Condensed:wght@300;400;500&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --black:   #0d0d0d;
      --gold:    #D4A017;
      --gold-lt: #e8b82a;
      --cream:   #F5F5F0;
      --white:   #ffffff;
      --dark-gray: #161616;
    }
    
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      background: var(--cream);
      color: #1a1a1a;
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }
    
    /* Background decorative glow */
    body::before {
      content: '';
      position: absolute;
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(212,160,23,0.1) 0%, rgba(245,245,240,0) 70%);
      top: -100px;
      right: -100px;
      z-index: 0;
    }
    
    body::after {
      content: '';
      position: absolute;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(212,160,23,0.06) 0%, rgba(245,245,240,0) 70%);
      bottom: -150px;
      left: -150px;
      z-index: 0;
    }
    
    .login-container {
      position: relative;
      z-index: 10;
      width: 100%;
      max-width: 420px;
      padding: 40px;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(212, 160, 23, 0.2);
      border-radius: 12px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.08);
      animation: fadeIn 0.8s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .logo-area {
      text-align: center;
      margin-bottom: 35px;
    }
    
    .logo {
      display: inline-flex;
      align-items: baseline;
      gap: 3px;
      text-decoration: none;
    }
    
    .logo-e {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 42px;
      color: var(--gold);
      letter-spacing: 2px;
      line-height: 1;
    }
    
    .logo-rix {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 42px;
      color: #1a1a1a;
      letter-spacing: 2px;
      line-height: 1;
    }
    
    .logo-dot {
      width: 6px;
      height: 6px;
      background: var(--gold);
      border-radius: 50%;
    }
    
    .subtitle {
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 13px;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: rgba(26,26,26,0.6);
      margin-top: 10px;
    }
    
    .error-alert {
      background: rgba(220, 53, 69, 0.1);
      border: 1px solid rgba(220, 53, 69, 0.25);
      color: #dc3545;
      padding: 12px 16px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 14px;
    }
    
    .form-group {
      margin-bottom: 24px;
    }
    
    label {
      display: block;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 13px;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: rgba(26,26,26,0.8);
      margin-bottom: 8px;
    }
    
    input {
      width: 100%;
      background: #ffffff;
      border: 1px solid rgba(13, 13, 13, 0.12);
      border-radius: 6px;
      padding: 12px 16px;
      color: #1a1a1a;
      font-family: 'DM Sans', sans-serif;
      font-size: 15px;
      transition: all 0.3s ease;
    }
    
    input:focus {
      outline: none;
      border-color: var(--gold);
      background: #ffffff;
      box-shadow: 0 0 10px rgba(212, 160, 23, 0.15);
    }
    
    .password-wrapper {
      position: relative;
    }
    
    .password-wrapper input {
      padding-right: 48px;
    }
    
    .password-toggle-btn {
      position: absolute;
      right: 14px;
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
      width: 20px;
      height: 20px;
    }
    
    .submit-btn {
      width: 100%;
      background: var(--gold);
      color: var(--black);
      border: none;
      border-radius: 6px;
      padding: 14px;
      font-family: 'Barlow Condensed', sans-serif;
      font-size: 15px;
      font-weight: 600;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    
    .submit-btn:hover {
      background: var(--gold-lt);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(212, 160, 23, 0.2);
    }
    
    .submit-btn:active {
      transform: translateY(0);
    }
    
    .back-home {
      text-align: center;
      margin-top: 25px;
    }
    
    .back-home a {
      color: rgba(26,26,26,0.6);
      text-decoration: none;
      font-size: 13px;
      font-family: 'Barlow Condensed', sans-serif;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      transition: color 0.3s;
    }
    
    .back-home a:hover {
      color: var(--gold);
    }
    
    @media only screen and (max-width: 480px) {
      .login-container {
        width: calc(100% - 32px);
        max-width: 380px;
        padding: 24px 20px;
        margin: 16px;
      }
      
      .logo-e, .logo-rix {
        font-size: 34px;
      }
      
      .subtitle {
        font-size: 11px;
        letter-spacing: 2px;
      }
      
      .logo-area {
        margin-bottom: 24px;
      }
      
      .form-group {
        margin-bottom: 18px;
      }
      
      label {
        font-size: 12px;
        margin-bottom: 6px;
      }
      
      input {
        padding: 10px 12px;
        font-size: 14px;
      }
      
      .password-wrapper input {
        padding-right: 40px;
      }
      
      .password-toggle-btn {
        right: 10px;
      }
      
      .submit-btn {
        padding: 12px;
        font-size: 14px;
        letter-spacing: 2px;
      }
      
      .back-home {
        margin-top: 20px;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="logo-area">
      <div class="logo">
        <span class="logo-e">E</span><span class="logo-rix">RIX</span>
        <span class="logo-dot"></span>
      </div>
      <div class="subtitle">Control Panel Login</div>
    </div>
    
    <?php if ($error): ?>
      <div class="error-alert">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>
    
    <form action="login.php" method="POST">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter admin username" required autocomplete="off">
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" placeholder="Enter password" required>
          <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password', this)" tabindex="-1" title="Show/Hide Password">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>
      
      <button type="submit" class="submit-btn">
        Login
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </form>
    
    <div class="back-home">
      <a href="../../index.php">← Back to website</a>
    </div>
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
</body>
</html>
