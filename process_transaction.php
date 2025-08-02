<?php
session_start();
require 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $userId = $_SESSION['user_id'];
    
    try {
        $conn->beginTransaction();
        
        if($action === 'add') {
            // Validasi data
            $type = $_POST['type'];
            $productId = $_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            $date = $_POST['date'];
            $notes = $_POST['notes'] ?? null;
            $supplierId = ($type === 'in') ? ($_POST['supplier_id'] ?? null) : null;
            
            // Validasi stok untuk transaksi keluar
            if($type === 'out') {
                $currentStock = $conn->query("SELECT stock FROM products WHERE id = $productId")->fetchColumn();
                if($currentStock < $quantity) {
                    throw new Exception("Stok tidak mencukupi untuk transaksi ini");
                }
            }
            
            // Tambahkan transaksi
            $stmt = $conn->prepare("INSERT INTO transactions (type, product_id, quantity, date, notes, user_id, supplier_id) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$type, $productId, $quantity, $date, $notes, $userId, $supplierId]);
            
            // Update stok produk
            if($type === 'in') {
                $conn->exec("UPDATE products SET stock = stock + $quantity WHERE id = $productId");
            } else {
                $conn->exec("UPDATE products SET stock = stock - $quantity WHERE id = $productId");
            }
            
            $_SESSION['message'] = 'Transaksi berhasil ditambahkan';
            
        } elseif($action === 'edit') {
            $id = $_POST['id'];
            $type = $_POST['type'];
            $productId = $_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            $date = $_POST['date'];
            $notes = $_POST['notes'] ?? null;
            $supplierId = ($type === 'in') ? ($_POST['supplier_id'] ?? null) : null;
            
            // Dapatkan data transaksi lama
            $oldTransaction = $conn->query("SELECT type, product_id, quantity FROM transactions WHERE id = $id")->fetch();
            
            // Update transaksi
            $stmt = $conn->prepare("UPDATE transactions SET 
                                  type = ?, 
                                  product_id = ?, 
                                  quantity = ?, 
                                  date = ?, 
                                  notes = ?, 
                                  supplier_id = ?
                                  WHERE id = ?");
            $stmt->execute([$type, $productId, $quantity, $date, $notes, $supplierId, $id]);
            
            // Update stok produk
            if($oldTransaction['product_id'] == $productId) {
                // Produk sama
                $quantityDiff = $quantity - $oldTransaction['quantity'];
                
                if($type === 'in') {
                    $conn->exec("UPDATE products SET stock = stock + $quantityDiff WHERE id = $productId");
                } else {
                    $conn->exec("UPDATE products SET stock = stock - $quantityDiff WHERE id = $productId");
                }
            } else {
                // Produk berbeda, kembalikan stok lama dan kurangi stok baru
                if($oldTransaction['type'] === 'in') {
                    $conn->exec("UPDATE products SET stock = stock - {$oldTransaction['quantity']} WHERE id = {$oldTransaction['product_id']}");
                } else {
                    $conn->exec("UPDATE products SET stock = stock + {$oldTransaction['quantity']} WHERE id = {$oldTransaction['product_id']}");
                }
                
                if($type === 'in') {
                    $conn->exec("UPDATE products SET stock = stock + $quantity WHERE id = $productId");
                } else {
                    $conn->exec("UPDATE products SET stock = stock - $quantity WHERE id = $productId");
                }
            }
            
            $_SESSION['message'] = 'Transaksi berhasil diperbarui';
            
        } elseif($action === 'delete') {
            $id = $_POST['id'];
            
            // Dapatkan data transaksi sebelum dihapus
            $stmt = $conn->prepare("SELECT type, product_id, quantity FROM transactions WHERE id = ?");
            $stmt->execute([$id]);
            $transaction = $stmt->fetch();
            
            if($transaction) {
                // Hapus transaksi
                $conn->prepare("DELETE FROM transactions WHERE id = ?")->execute([$id]);
                
                // Kembalikan stok jika perlu
                if($transaction['type'] === 'in') {
                    $conn->exec("UPDATE products SET stock = stock - {$transaction['quantity']} WHERE id = {$transaction['product_id']}");
                } else {
                    $conn->exec("UPDATE products SET stock = stock + {$transaction['quantity']} WHERE id = {$transaction['product_id']}");
                }
                
                $_SESSION['message'] = 'Transaksi berhasil dihapus';
            }
        }
        
        $conn->commit();
    } catch(Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
    
    header("Location: transactions.php");
    exit();
}
?>