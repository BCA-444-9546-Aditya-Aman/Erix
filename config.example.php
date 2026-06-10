<?php
/**
 * Erix Construction - Database Configuration
 * Rename this file to config.php and update with your live Hostinger credentials.
 */

// Production Database Credentials (Hostinger)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306'); // Hostinger usually uses 3306
define('DB_NAME', 'u123456789_erix_db'); // Hostinger database name format
define('DB_USER', 'u123456789_user');    // Hostinger database user format
define('DB_PASS', 'YourStrongPasswordHere!');

// Google reCAPTCHA v2 Credentials
define('RECAPTCHA_SITE_KEY', 'YOUR_SITE_KEY_HERE');
define('RECAPTCHA_SECRET_KEY', 'YOUR_SECRET_KEY_HERE');

// Optional: Application Settings
define('APP_ENV', 'production'); // 'development' or 'production'
define('DISPLAY_ERRORS', false); // Set to false in production
?>
