<?php
session_start();
require 'config/database.php';

$error = '';
$success = '';

if(isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $fullname = trim($_POST['fullname']);
    $role = 'staff'; // Default role untuk user baru

    // Validasi
    if(empty($username) || empty($password) || empty($confirm_password) || empty($fullname)) {
        $error = 'Semua field harus diisi!';
    } elseif($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif(strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if($stmt->rowCount() > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            // Enkripsi password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Simpan ke database
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, fullname) VALUES (?, ?, ?, ?)");
            if($stmt->execute([$username, $hashed_password, $role, $fullname])) {
                $success = 'Akun berhasil dibuat! Silakan login.';
                header("refresh:2;url=login.php");
            } else {
                $error = 'Gagal membuat akun. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarehousePro - Daftar Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="assets/logo.png" type="image/x-icon">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-blue-600">WarehousePro</h1>
                <p class="text-gray-600">Daftar Akun Baru</p>
            </div>
            
            <?php if($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label for="fullname" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="fullname" name="fullname" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" id="username" name="username" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Gunakan kombinasi huruf dan angka</p>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">Minimal 6 karakter</p>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">
                            Sudah punya akun? Login disini
                        </a>
                    </div>
                </div>
                
                <div>
                    <button type="submit" name="register" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Daftar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Validasi password client-side
    document.addEventListener('DOMContentLoaded', function() {
        const password = document.getElementById('password');
        const confirm_password = document.getElementById('confirm_password');
        
        function validatePassword() {
            if(password.value !== confirm_password.value) {
                confirm_password.setCustomValidity("Password tidak sama!");
            } else {
                confirm_password.setCustomValidity('');
            }
        }
        
        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    });
    </script>
</body>
</html>