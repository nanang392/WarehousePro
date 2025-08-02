<?php
session_start();
require 'config/database.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Set timezone
    date_default_timezone_set('Asia/Jakarta');
    
    // Prepare query to get transaction data with supplier information
    $query = "SELECT 
                t.id,
                t.date,
                p.name AS product_name,
                p.sku,
                s.name AS supplier_name,
                CASE 
                    WHEN t.type = 'in' THEN 'Stock In' 
                    ELSE 'Stock Out' 
                END AS transaction_type,
                t.quantity,
                p.unit,
                u.fullname AS user_name,
                t.notes
              FROM transactions t
              JOIN products p ON t.product_id = p.id
              LEFT JOIN suppliers s ON t.supplier_id = s.id
              JOIN users u ON t.user_id = u.id
              ORDER BY t.date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare file for download
    $filename = "transaction_history_" . date("Ymd_His") . ".csv";
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Open output stream
    $output = fopen("php://output", "w");
    if ($output === false) {
        throw new Exception("Failed to open output stream");
    }

    // Add UTF-8 BOM for proper encoding
    fwrite($output, "\xEF\xBB\xBF");

    // Write CSV headers
    fputcsv($output, [
        'ID',
        'Date/Time',
        'Product Name',
        'SKU',
        'Supplier',
        'Transaction Type',
        'Quantity',
        'Unit',
        'Processed By',
        'Notes'
    ]);

    // Write transaction data
    if (count($transactions) > 0) {
        foreach ($transactions as $transaction) {
            fputcsv($output, [
                $transaction['id'],
                $transaction['date'],
                $transaction['product_name'],
                $transaction['sku'],
                $transaction['supplier_name'] ?? 'N/A',
                $transaction['transaction_type'],
                $transaction['quantity'],
                $transaction['unit'],
                $transaction['user_name'],
                $transaction['notes']
            ]);
        }
    } else {
        fputcsv($output, ['No transaction records found']);
    }

    fclose($output);
    exit();

} catch (PDOException $e) {
    error_log("Transaction export error: " . $e->getMessage());
    $_SESSION['export_error'] = "Failed to export transaction data: Database error";
    header("Location: reports.php?tab=transactions");
    exit();
} catch (Exception $e) {
    error_log("Transaction export error: " . $e->getMessage());
    $_SESSION['export_error'] = "Failed to export transaction data: System error";
    header("Location: reports.php?tab=transactions");
    exit();
}
?>