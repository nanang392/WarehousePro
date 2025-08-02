<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarehousePro - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.0.0/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="assets/js/main.js" defer></script>
    <link rel="icon" href="assets/logo.png" type="image/x-icon">
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 bg-blue-800">
                <div class="flex items-center h-16 px-4 bg-blue-900">
    <div class="flex items-center space-x-2">
        <img src="assets/logogudang.png" alt="Logo" class="h-8 w-auto">
        <span class="text-white font-bold text-xl">WarehousePro</span>
    </div>
</div>
                <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
                    <nav class="flex-1 space-y-2">
                        <a href="dashboard.php" class="flex items-center px-2 py-2 text-sm font-medium text-white rounded-md hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-600' : ''; ?>">
                            <i class="fas fa-tachometer-alt mr-3"></i>
                            Dashboard
                        </a>
                        <a href="products.php" class="flex items-center px-2 py-2 text-sm font-medium text-white rounded-md hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'bg-blue-600' : ''; ?>">
                            <i class="fas fa-boxes mr-3"></i>
                            Produk
                        </a>
                        <a href="transactions.php" class="flex items-center px-2 py-2 text-sm font-medium text-white rounded-md hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'bg-blue-600' : ''; ?>">
                            <i class="fas fa-exchange-alt mr-3"></i>
                            Transaksi
                        </a>
                        <a href="suppliers.php" class="flex items-center px-2 py-2 text-sm font-medium text-white rounded-md hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'suppliers.php' ? 'bg-blue-600' : ''; ?>">
                            <i class="fas fa-truck mr-3"></i>
                            Supplier
                        </a>
                        <a href="reports.php" class="flex items-center px-2 py-2 text-sm font-medium text-white rounded-md hover:bg-blue-700 <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-blue-600' : ''; ?>">
                            <i class="fas fa-chart-bar mr-3"></i>
                            Laporan
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-blue-700">
                    <div class="flex items-center">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white"><?php echo $_SESSION['username']; ?></p>
                            <p class="text-xs font-medium text-blue-200"><?php echo ucfirst($_SESSION['role']); ?></p>
                        </div>
                    </div>
                <button id="mobileLogoutBtn" onclick="confirmLogout()" class="w-full text-left block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-blue-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </button>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 overflow-auto">
            <div class="flex flex-col h-full">
                <!-- Mobile header -->
                <div class="md:hidden flex items-center justify-between px-4 py-3 bg-blue-800 border-b border-blue-700">
                    <span class="text-white font-bold">WarehousePro</span>
                    <button id="mobile-menu-button" class="text-white focus:outline-none">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                
                <!-- Mobile sidebar (hidden by default) -->
                <div id="mobile-menu" class="hidden md:hidden bg-blue-800">
                    <nav class="px-2 py-4 space-y-1">
                        <a href="dashboard.php" class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-blue-700">Dashboard</a>
                        <a href="products.php" class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-blue-700">Produk</a>
                        <a href="transactions.php" class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-blue-700">Transaksi</a>
                        <a href="suppliers.php" class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-blue-700">Supplier</a>
                        <a href="reports.php" class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-blue-700">Laporan</a>
                        <a href="logout.php" class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-blue-700">Logout</a>
                    </nav>
                </div>
                <main class="flex-1 p-4 bg-gray-50">

                <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
        function confirmLogout() {
        console.log("Logout button clicked"); // Tambahkan log
        Swal.fire({
            title: 'Kamu yakin ingin keluar?',
            text: "Jika ya, kamu akan keluar dari aplikasi ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, keluar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    }
      // Toggle menu mobile
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
