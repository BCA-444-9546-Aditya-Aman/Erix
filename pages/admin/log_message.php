<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Only POST requests allowed."]);
    exit;
}

require_once 'db.php';

// Get raw JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Fallback to form data if JSON decode fails
if (!$input) {
    $input = $_POST;
}

$full_name = isset($input['full_name']) ? trim($input['full_name']) : '';
$phone     = isset($input['phone']) ? trim($input['phone']) : '';
$email     = isset($input['email']) ? trim($input['email']) : '';
$service   = isset($input['service']) ? trim($input['service']) : '';
$message   = isset($input['message']) ? trim($input['message']) : '';

if (empty($full_name) || empty($phone) || empty($email) || empty($service) || empty($message)) {
    echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO contact_messages (full_name, phone, email, service, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$full_name, $phone, $email, $service, $message]);
    echo json_encode(["status" => "success", "message" => "Message logged successfully!"]);
} catch (\PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database insert failed: " . $e->getMessage()]);
}
?>
