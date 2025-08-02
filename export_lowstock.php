<?php
session_start();
require 'config/database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Prepare query to get low stock items (stock <= min_stock)
    $query = "SELECT 
                p.sku, 
                p.name, 
                p.category, 
                p.stock, 
                p.min_stock,
                (p.min_stock - p.stock) AS deficit,
                p.location,
                p.unit
              FROM products p
              WHERE p.stock <= p.min_stock
              ORDER BY deficit DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $lowStockItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare file for download
    $filename = "low_stock_items_" . date("Ymd_His") . ".csv";
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Open output stream
    $output = fopen("php://output", "w");
    if ($output === false) {
        throw new Exception("Failed to open output stream");
    }

    // Add UTF-8 BOM
    fwrite($output, "\xEF\xBB\xBF");

    // Write CSV headers
    fputcsv($output, [
        'SKU',
        'Product Name',
        'Category',
        'Current Stock',
        'Minimum Stock', 
        'Deficit (Shortage)',
        'Location',
        'Unit'
    ]);

    // Write data rows
    if (count($lowStockItems) > 0) {
        foreach ($lowStockItems as $item) {
            fputcsv($output, [
                $item['sku'],
                $item['name'],
                $item['category'],
                $item['stock'],
                $item['min_stock'],
                $item['deficit'],
                $item['location'],
                $item['unit']
            ]);
        }
    } else {
        fputcsv($output, ['No low stock items found']);
    }

    fclose($output);
    exit();

} catch (PDOException $e) {
    // Log error and redirect
    error_log("Export low stock error: " . $e->getMessage());
    $_SESSION['export_error'] = "Failed to export low stock items: " . $e->getMessage();
    header("Location: reports.php?tab=lowstock");
    exit();
} catch (Exception $e) {
    error_log("Export low stock error: " . $e->getMessage());
    $_SESSION['export_error'] = "System error occurred";
    header("Location: reports.php?tab=lowstock");
    exit();
}
?>