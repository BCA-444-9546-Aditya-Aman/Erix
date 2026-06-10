<?php
require_once 'pages/admin/db.php';

header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

$baseUrl = "https://" . $_SERVER['HTTP_HOST'] . str_replace("sitemap.php", "", $_SERVER['SCRIPT_NAME']);

// Static Pages
$pages = [
    'index' => '1.0',
    'pages/users/about' => '0.8',
    'pages/users/services' => '0.9',
    'pages/users/projects' => '0.9',
    'pages/users/blogs' => '0.8',
    'pages/users/contact' => '0.7'
];

foreach ($pages as $page => $priority) {
    echo "<url>";
    echo "<loc>" . htmlspecialchars($baseUrl . $page) . "</loc>";
    echo "<lastmod>" . date('Y-m-d') . "</lastmod>";
    echo "<changefreq>monthly</changefreq>";
    echo "<priority>{$priority}</priority>";
    echo "</url>";
}

// Dynamic Projects
try {
    $stmt = $pdo->query("SELECT id FROM projects");
    while ($row = $stmt->fetch()) {
        echo "<url>";
        echo "<loc>" . htmlspecialchars($baseUrl . "pages/users/project-details?id=" . $row['id']) . "</loc>";
        echo "<lastmod>" . date('Y-m-d') . "</lastmod>";
        echo "<changefreq>monthly</changefreq>";
        echo "<priority>0.8</priority>";
        echo "</url>";
    }
} catch (\PDOException $e) {}

// Dynamic Services
try {
    $stmt = $pdo->query("SELECT id FROM services");
    while ($row = $stmt->fetch()) {
        echo "<url>";
        echo "<loc>" . htmlspecialchars($baseUrl . "pages/users/service-details?id=" . $row['id']) . "</loc>";
        echo "<lastmod>" . date('Y-m-d') . "</lastmod>";
        echo "<changefreq>monthly</changefreq>";
        echo "<priority>0.8</priority>";
        echo "</url>";
    }
} catch (\PDOException $e) {}

// Dynamic Blogs
try {
    $stmt = $pdo->query("SELECT id, date_published FROM blogs");
    while ($row = $stmt->fetch()) {
        echo "<url>";
        echo "<loc>" . htmlspecialchars($baseUrl . "pages/users/blog-details?id=" . $row['id']) . "</loc>";
        echo "<lastmod>" . date('Y-m-d', strtotime($row['date_published'])) . "</lastmod>";
        echo "<changefreq>monthly</changefreq>";
        echo "<priority>0.7</priority>";
        echo "</url>";
    }
} catch (\PDOException $e) {}

echo '</urlset>';
?>
