<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php echo isset($pageTitle) ? $pageTitle : "Erix Construction"; ?></title>
<link rel="stylesheet" href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>assets/css/style.css"/>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=Barlow+Condensed:wght@300;400;500&display=swap" rel="stylesheet"/>
<script>var ERIX_ROOT = "<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>";</script>
</head>
<body>

<!-- ── NAVIGATION ── -->
<nav id="mainNav" <?php echo isset($navClass) ? 'class="' . $navClass . '"' : ''; ?> <?php echo isset($navLocked) ? 'data-locked="true"' : ''; ?>>
  <a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>index.php" class="nav-logo">
    <span class="logo-e">E</span><span class="logo-rix">RIX</span>
    <span class="logo-dot"></span>
  </a>
  <ul class="nav-links">
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/about.php" <?php if(isset($currentPage) && $currentPage == 'about') echo 'class="active"'; ?>>About</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/services.php" <?php if(isset($currentPage) && $currentPage == 'services') echo 'class="active"'; ?>>Services</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/projects.php" <?php if(isset($currentPage) && $currentPage == 'projects') echo 'class="active"'; ?>>Projects</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/blogs.php" <?php if(isset($currentPage) && $currentPage == 'blogs') echo 'class="active"'; ?>>Blogs</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>pages/users/contact.php" <?php if(isset($currentPage) && $currentPage == 'contact') echo 'class="active"'; ?>>Contact</a></li>
    <li><a href="<?php echo isset($pathPrefix) ? $pathPrefix : './'; ?>index.php#contact" class="nav-cta">Get a Quote</a></li>
  </ul>
  <div class="hamburger" id="hamburger">
    <span></span><span></span><span></span>
  </div>
</nav>
