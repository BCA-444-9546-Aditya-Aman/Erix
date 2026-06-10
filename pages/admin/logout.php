<?php
session_start();
session_unset();
session_destroy();
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = substr($scriptName, 0, strpos($scriptName, 'pages/admin/'));
header("Location: " . rtrim($basePath, '/') . "/index.php");
exit;
exit;
?>
