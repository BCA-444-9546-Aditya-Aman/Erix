<?php
$configFile = __DIR__ . '/../../config.php';
if (file_exists($configFile)) {
    require_once $configFile;
}

// Check for constants, otherwise fallback to local XAMPP defaults
$host = defined('DB_HOST') ? DB_HOST : 'localhost';
$port = defined('DB_PORT') ? DB_PORT : '3307';
$db   = defined('DB_NAME') ? DB_NAME : 'erix_db';
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';
$charset = 'utf8mb4';

// reCAPTCHA keys (Use test keys for local dev if not defined)
$recaptchaSiteKey = defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
$recaptchaSecretKey = defined('RECAPTCHA_SECRET_KEY') ? RECAPTCHA_SECRET_KEY : '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

// Set error reporting based on environment
if (defined('DISPLAY_ERRORS') && !DISPLAY_ERRORS) {
    ini_set('display_errors', 0);
    error_reporting(0);
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Auto-seed default admin if table is empty
     try {
         $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
         if ($stmt) {
             $count = $stmt->fetchColumn();
             if ($count == 0) {
                 $hash = password_hash('admin123', PASSWORD_DEFAULT);
                 $insert = $pdo->prepare("INSERT INTO admin_users (username, password, created_at) VALUES ('admin', ?, NOW())");
                 $insert->execute([$hash]);
             }
         }
     } catch (\PDOException $e) {
         // Silently ignore if table doesn't exist yet
     }
} catch (\PDOException $e) {
     die("Database connection failed. Please ensure MySQL is running in XAMPP and the database is imported. Error: " . $e->getMessage());
}
?>
