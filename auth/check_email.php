<?php
require_once '../classes/database.php';
header('Content-Type: application/json');

$email = trim($_GET['email'] ?? '');
$response = ['exists' => false];

if ($email) {
    $db = new Database();
    $conn = $db->openConnection();
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $response['exists'] = true;
    }
}

echo json_encode($response);