<?php

require 'config/database.php';
require 'includes/header.php';
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$pageTitle = 'Dashboard';
// Get stats
$productCount = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$lowStockCount = $conn->query("SELECT COUNT(*) FROM products WHERE stock <= min_stock")->fetchColumn();
$recentTransactions = $conn->query("SELECT t.*, p.name as product_name FROM transactions t JOIN products p ON t.product_id = p.id ORDER BY t.date DESC LIMIT 5")->fetchAll();

?>

<!-- Header -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg p-6 mb-8 text-white">
    <h1 class="text-3xl font-bold">Dashboard</h1>
    <div class="flex items-center mt-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <p class="text-blue-100">Selamat datang, <?php echo $_SESSION['username']; ?></p>
    </div>
</div>

<!-- Statistik Cards dengan hover effect -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Produk -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Produk</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $productCount; ?></p>
            </div>
        </div>
        <div class="bg-blue-50 px-4 py-2 text-sm text-blue-600">
            <a href="products.php" class="flex items-center">
                Lihat semua produk
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
    
    <!-- Stok Rendah -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Stok Rendah</p>
                <p class="text-3xl font-bold text-gray-800"><?php echo $lowStockCount; ?></p>
            </div>
        </div>
        <div class="bg-red-50 px-4 py-2 text-sm text-red-600">
            <a href="products.php?filter=low_stock" class="flex items-center">
                Perlu restock
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
    
    <!-- Barang Masuk -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Barang Masuk (30 hari)</p>
                <p class="text-3xl font-bold text-gray-800">
                    <?php 
                        $incoming = $conn->query("SELECT SUM(quantity) FROM transactions WHERE type = 'in' AND date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
                        echo $incoming ? number_format($incoming) : '0';
                    ?>
                </p>
            </div>
        </div>
        <div class="bg-green-50 px-4 py-2 text-sm text-green-600">
            <a href="transactions.php?type=in" class="flex items-center">
                Lihat transaksi masuk
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
    
    <!-- Barang Keluar -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <div class="p-6 flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Barang Keluar (30 hari)</p>
                <p class="text-3xl font-bold text-gray-800">
                    <?php 
                        $outgoing = $conn->query("SELECT SUM(quantity) FROM transactions WHERE type = 'out' AND date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
                        echo $outgoing ? number_format($outgoing) : '0';
                    ?>
                </p>
            </div>
        </div>
        <div class="bg-yellow-50 px-4 py-2 text-sm text-yellow-600">
            <a href="transactions.php?type=out" class="flex items-center">
                Lihat transaksi keluar
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>

<!-- Chart dan Stok Rendah -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Chart dengan card lebih menarik -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Aktivitas Stok 30 Hari Terakhir
            </h2>
        </div>
        <div class="p-4">
            <canvas id="stockChart" height="250"></canvas>
        </div>
    </div>
    
    <!-- Produk Stok Rendah -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Produk dengan Stok Rendah
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    $lowStockProducts = $conn->query("SELECT * FROM products WHERE stock <= min_stock ORDER BY stock ASC LIMIT 5");
                    while($product = $lowStockProducts->fetch()):
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $product['name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $product['sku']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $product['stock'] == 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo $product['stock']; ?> <?php echo $product['unit']; ?>
                                </span>
                                <span class="text-xs text-gray-500 mt-1">Min: <?php echo $product['min_stock']; ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:text-blue-900">Detail</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="bg-gray-50 px-4 py-3 text-right">
            <a href="products.php?filter=low_stock" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                Lihat semua stok rendah
            </a>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            Transaksi Terakhir
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach($recentTransactions as $transaction): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-medium"><?php echo date('d M Y', strtotime($transaction['date'])); ?></div>
                        <div class="text-sm text-gray-500"><?php echo date('H:i', strtotime($transaction['date'])); ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?php echo $transaction['product_name']; ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $transaction['type'] == 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo $transaction['type'] == 'in' ? 'Masuk' : 'Keluar'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?php echo $transaction['type'] == 'in' ? 'text-green-600' : 'text-red-600'; ?>">
                        <?php echo $transaction['quantity']; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="transaction_detail.php?id=<?php echo $transaction['id']; ?>" class="text-blue-600 hover:text-blue-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="bg-gray-50 px-4 py-3 text-right">
        <a href="transactions.php" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            Lihat semua transaksi
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart dengan animasi lebih smooth
    const ctx = document.getElementById('stockChart').getContext('2d');
    
    fetch('api/get_chart_data.php')
        .then(response => response.json())
        .then(data => {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Barang Masuk',
                            data: data.incoming,
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: 'white',
                            pointBorderColor: 'rgb(16, 185, 129)',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Barang Keluar',
                            data: data.outgoing,
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.05)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: 'white',
                            pointBorderColor: 'rgb(239, 68, 68)',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleFont: { size: 14 },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 4
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                padding: 10
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                padding: 10
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
});
</script>

<?php require 'includes/footer.php'; ?>