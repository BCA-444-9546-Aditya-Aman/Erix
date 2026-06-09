<?php
$host = 'localhost';
$db   = 'erix_db';
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$port='3307';
$charset = 'utf8mb4';

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
