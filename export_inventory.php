<?php
session_start();
require 'config/database.php'; // koneksi PDO kamu

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Siapkan query ambil data
    $query = "SELECT sku, name, category, description, unit, stock, min_stock, location FROM products";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC); // Ambil semua data

    // Siapkan file untuk download
    $filename = "data_produk_" . date("Ymd_His") . ".csv";
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Buka stream output
    $output = fopen("php://output", "w");
    if ($output === false) {
        throw new Exception("Gagal membuka stream output");
    }

    // Tulis header kolom
    fputcsv($output, ['SKU', 'Nama Produk', 'Kategori', 'Deskripsi', 'Satuan', 'Stok', 'Stok Minimum', 'Lokasi']);

    // Tulis data produk
    if (count($products) > 0) {
        foreach ($products as $row) {
            fputcsv($output, [
                $row['sku'],
                $row['name'],
                $row['category'],
                $row['description'],
                $row['unit'],
                $row['stock'],
                $row['min_stock'],
                $row['location']
            ]);
        }
    } else {
        fputcsv($output, ['Tidak ada data produk']);
    }

    fclose($output);
    exit();

} catch (PDOException $e) {
    echo "Gagal mengekspor data: " . $e->getMessage();
    exit();
}
?>
