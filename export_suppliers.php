<?php
session_start();
require 'config/database.php'; // koneksi PDO

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Siapkan query ambil data supplier
    $query = "SELECT name, contact, phone, email, address FROM suppliers ORDER BY name";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Siapkan file untuk download
    $filename = "data_supplier_" . date("Ymd_His") . ".csv";
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Buka stream output
    $output = fopen("php://output", "w");
    if ($output === false) {
        throw new Exception("Gagal membuka stream output");
    }

    // Tambahkan BOM untuk UTF-8
    fwrite($output, "\xEF\xBB\xBF");

    // Tulis header kolom
    fputcsv($output, [
        'Nama Supplier',
        'Kontak Person',
        'Telepon',
        'Email', 
        'Alamat'
    ]); // menggunakan ; sebagai delimiter

    // Tulis data supplier
    if (count($suppliers) > 0) {
        foreach ($suppliers as $row) {
            fputcsv($output, [
                $row['name'],
                $row['contact'],
                $row['phone'],
                $row['email'],
                $row['address']
            ]);
        }
    } else {
        fputcsv($output, ['Tidak ada data supplier']);
    }

    fclose($output);
    exit();

} catch (PDOException $e) {
    // Log error dan redirect
    error_log("Export supplier error: " . $e->getMessage());
    $_SESSION['export_error'] = "Gagal mengekspor data supplier";
    header("Location: suppliers.php");
    exit();
} catch (Exception $e) {
    error_log("Export supplier error: " . $e->getMessage());
    $_SESSION['export_error'] = "Terjadi kesalahan sistem";
    header("Location: suppliers.php");
    exit();
}
?>