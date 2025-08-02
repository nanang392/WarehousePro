<?php
require '../config/database.php';
header('Content-Type: application/json');

$labels = [];
$incomingData = [];
$outgoingData = [];

// Get data for last 30 days
for ($i = 30; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('d M', strtotime($date));
    
    // Incoming
    $stmt = $conn->prepare("SELECT IFNULL(SUM(quantity), 0) FROM transactions WHERE type = 'in' AND DATE(date) = ?");
    $stmt->execute([$date]);
    $incomingData[] = $stmt->fetchColumn();
    
    // Outgoing
    $stmt = $conn->prepare("SELECT IFNULL(SUM(quantity), 0) FROM transactions WHERE type = 'out' AND DATE(date) = ?");
    $stmt->execute([$date]);
    $outgoingData[] = $stmt->fetchColumn();
}

echo json_encode([
    'labels' => $labels,
    'incoming' => $incomingData,
    'outgoing' => $outgoingData
]);
?>