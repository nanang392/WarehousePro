<?php
require '../config/database.php';
header('Content-Type: application/json');

function generateRandomString($length = 4) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $randomString;
}

// Generate SKU with format: WP-YYYYMMDD-XXXX
$sku = 'WP-' . date('Ymd') . '-' . generateRandomString();

// Check if SKU exists
$stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE sku = ?");
$stmt->execute([$sku]);

// If exists, generate new one
while ($stmt->fetchColumn() > 0) {
    $sku = 'WP-' . date('Ymd') . '-' . generateRandomString();
    $stmt->execute([$sku]);
}

echo json_encode(['sku' => $sku]);
?>