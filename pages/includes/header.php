<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php echo isset($pageTitle) ? $pageTitle : "Erix Construction"; ?></title>

<?php
$metaDesc = isset($metaDescription) ? $metaDescription : "Erix Construction - Industry-leading construction and engineering firm specializing in luxury residential, commercial, and structural retrofitting.";
$metaKeys = isset($metaKeywords) ? $metaKeywords : "construction company, building, luxury residential construction, commercial construction, structural engineering, Erix Construction";
$ogImg    = isset($ogImage) ? $ogImage : (isset($pathPrefix) ? $pathPrefix : './') . "assets/images/team_member.png";
?>
<meta name="description" content="<?php echo htmlspecialchars($metaDesc); ?>" />
<meta name="keywords" content="<?php echo htmlspecialchars($metaKeys); ?>" />

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="<?php echo htmlspecialchars(isset($pageTitle) ? $pageTitle : "Erix Construction"); ?>" />
<meta property="og:description" content="<?php echo htmlspecialchars($metaDesc); ?>" />
<meta property="og:image" content="<?php echo htmlspecialchars($ogImg); ?>" />
<meta property="og:type" content="website" />
<meta name="twitter:card" content="summary_large_image" />

<?php
// Load reCAPTCHA key securely
$configFile = __DIR__ . '/../../config.php';
if (file_exists($configFile)) {
    require_once $configFile;
}
$siteKey = defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
?>
<link rel="stylesheet" href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>assets/css/style.css"/>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=Barlow+Condensed:wght@300;400;500&display=swap" rel="stylesheet"/>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
  var ERIX_ROOT = "<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>";
  var RECAPTCHA_SITE_KEY = "<?php echo htmlspecialchars($siteKey); ?>";
</script>
</head>
<body>

<!-- ── NAVIGATION ── -->
<div class="sidebar-overlay" id="user-sidebar-overlay"></div>
<nav id="mainNav" <?php echo isset($navClass) ? 'class="' . $navClass . '"' : ''; ?> <?php echo isset($navLocked) ? 'data-locked="true"' : ''; ?>>
  <a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>index" class="nav-logo">
    <span class="logo-e">E</span><span class="logo-rix">RIX</span>
    <span class="logo-dot"></span>
  </a>
  <ul class="nav-links">
    <li class="mobile-nav-brand">
      <a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>index" class="nav-logo">
        <span class="logo-e">E</span><span class="logo-rix">RIX</span>
        <span class="logo-dot"></span>
      </a>
    </li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/about" <?php if(isset($currentPage) && $currentPage == 'about') echo 'class="active"'; ?>>About</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/services" <?php if(isset($currentPage) && $currentPage == 'services') echo 'class="active"'; ?>>Services</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/projects" <?php if(isset($currentPage) && $currentPage == 'projects') echo 'class="active"'; ?>>Projects</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/blogs" <?php if(isset($currentPage) && $currentPage == 'blogs') echo 'class="active"'; ?>>Blogs</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/contact" <?php if(isset($currentPage) && $currentPage == 'contact') echo 'class="active"'; ?>>Contact</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>index#contact" class="nav-cta">Get a Quote</a></li>
  </ul>
  <div class="hamburger" id="hamburger">
    <span></span><span></span><span></span>
  </div>
</nav>
