<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = 'Transaksi';

// Filter transactions
$filterType = isset($_GET['type']) ? $_GET['type'] : 'all';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT t.*, p.name as product_name, p.sku as product_sku, 
          s.name as supplier_name 
          FROM transactions t
          LEFT JOIN products p ON t.product_id = p.id
          LEFT JOIN suppliers s ON t.supplier_id = s.id";

$whereClauses = [];
$params = [];

if($filterType !== 'all') {
    $whereClauses[] = "t.type = ?";
    $params[] = $filterType;
}

if(!empty($searchQuery)) {
    $whereClauses[] = "(p.name LIKE ? OR p.sku LIKE ? OR s.name LIKE ? OR t.notes LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

if(!empty($whereClauses)) {
    $query .= " WHERE " . implode(" AND ", $whereClauses);
}

$query .= " ORDER BY t.date DESC";

// Prepare and execute
$stmt = $conn->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Get counts for filter badges
$allCount = $conn->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$inCount = $conn->query("SELECT COUNT(*) FROM transactions WHERE type = 'in'")->fetchColumn();
$outCount = $conn->query("SELECT COUNT(*) FROM transactions WHERE type = 'out'")->fetchColumn();

// Get products and suppliers for dropdowns
$products = $conn->query("SELECT id, name, sku, stock FROM products ORDER BY name")->fetchAll();
$suppliers = $conn->query("SELECT id, name FROM suppliers ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarehousePro - <?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.0.0/dist/css/tom-select.css" rel="stylesheet">
    <link rel="icon" href="assets/logo.png" type="image/x-icon">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
<main class="flex-1 p-6">
        <!-- Header dengan filter dan search -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Transaksi</h1>
                <p class="text-gray-600">Daftar seluruh transaksi barang masuk dan keluar</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <button onclick="openModal('addModal')" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Transaksi Baru
                </button>
            </div>
        </div>

        <!-- Filter dan Search -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Filter Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Tipe</label>
                    <div class="flex space-x-2">
                        <a href="transactions.php?type=all" 
                           class="<?php echo $filterType === 'all' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'; ?> px-3 py-1 rounded-full text-sm font-medium flex items-center">
                            Semua <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full ml-2"><?php echo $allCount; ?></span>
                        </a>
                        <a href="transactions.php?type=in" 
                           class="<?php echo $filterType === 'in' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?> px-3 py-1 rounded-full text-sm font-medium flex items-center">
                            Masuk <span class="bg-green-600 text-white text-xs px-2 py-0.5 rounded-full ml-2"><?php echo $inCount; ?></span>
                        </a>
                        <a href="transactions.php?type=out" 
                           class="<?php echo $filterType === 'out' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'; ?> px-3 py-1 rounded-full text-sm font-medium flex items-center">
                            Keluar <span class="bg-red-600 text-white text-xs px-2 py-0.5 rounded-full ml-2"><?php echo $outCount; ?></span>
                        </a>
                    </div>
                </div>
                
                <!-- Search -->
                <div>
                    <form method="GET" class="flex">
                        <input type="hidden" name="type" value="<?php echo $filterType; ?>">
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" 
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                   placeholder="Cari transaksi...">
                        </div>
                        <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Cari
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Transaksi -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(empty($transactions)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data transaksi
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($transactions as $transaction): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo date('d M Y', strtotime($transaction['date'])); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo date('H:i', strtotime($transaction['date'])); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo $transaction['product_name']; ?></div>
                                            <div class="text-sm text-gray-500"><?php echo $transaction['product_sku']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo $transaction['supplier_name'] ?? '-'; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $transaction['type'] == 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $transaction['type'] == 'in' ? 'Masuk' : 'Keluar'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?php echo $transaction['type'] == 'in' ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $transaction['quantity']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 max-w-xs truncate"><?php echo $transaction['notes'] ?? '-'; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="openEditModal(
                                        <?php echo $transaction['id']; ?>,
                                        '<?php echo $transaction['type']; ?>',
                                        <?php echo $transaction['product_id']; ?>,
                                        <?php echo $transaction['quantity']; ?>,
                                        '<?php echo date('Y-m-d\TH:i', strtotime($transaction['date'])); ?>',
                                        '<?php echo addslashes($transaction['notes'] ?? ''); ?>',
                                        <?php echo $transaction['supplier_id'] ?? 'null'; ?>
                                    )" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="openDeleteModal(<?php echo $transaction['id']; ?>)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium">10</span> dari <span class="font-medium"><?php echo count($transactions); ?></span> hasil
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <a href="#" aria-current="page" class="z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                1
                            </a>
                            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                2
                            </a>
                            <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                3
                            </a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Transaksi -->
    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Tambah Transaksi Baru</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addTransactionForm" action="process_transaction.php" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
                        <select name="type" id="transactionType" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required
                                onchange="toggleSupplierField('transactionType')">
                            <option value="in">Barang Masuk</option>
                            <option value="out">Barang Keluar</option>
                        </select>
                    </div>
                    
                    <div id="supplierField">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="supplier_id" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Pilih Supplier</option>
                            <?php foreach($suppliers as $supplier): ?>
                            <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                        <select name="product_id" id="productSelect" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Pilih Produk</option>
                            <?php foreach($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" data-stock="<?php echo $product['stock']; ?>">
                                <?php echo $product['name']; ?> (Stok: <?php echo $product['stock']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <input type="number" name="quantity" min="1" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        <p id="stockWarning" class="text-xs text-red-600 mt-1 hidden">Jumlah melebihi stok tersedia!</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="datetime-local" name="date" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" rows="2" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Transaksi -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Edit Transaksi</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editTransactionForm" action="process_transaction.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi</label>
                        <select name="type" id="editType" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required
                                onchange="toggleSupplierField('editType')">
                            <option value="in">Barang Masuk</option>
                            <option value="out">Barang Keluar</option>
                        </select>
                    </div>
                    
                    <div id="editSupplierField">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="supplier_id" id="editSupplier" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Pilih Supplier</option>
                            <?php foreach($suppliers as $supplier): ?>
                            <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                        <select name="product_id" id="editProduct" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Pilih Produk</option>
                            <?php foreach($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" data-stock="<?php echo $product['stock']; ?>">
                                <?php echo $product['name']; ?> (Stok: <?php echo $product['stock']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <input type="number" name="quantity" id="editQuantity" min="1" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="datetime-local" name="date" id="editDate" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="notes" id="editNotes" rows="2" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus Transaksi -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Konfirmasi Hapus</h3>
                <button onclick="closeModal('deleteModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="deleteTransactionForm" action="process_transaction.php" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteId">
                
                <p class="text-gray-700 mb-6">Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.</p>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Hapus Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Fungsi untuk modal
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Fungsi untuk edit transaksi
    function openEditModal(id, type, productId, quantity, date, notes, supplierId) {
        document.getElementById('editId').value = id;
        document.getElementById('editType').value = type;
        document.getElementById('editProduct').value = productId;
        document.getElementById('editQuantity').value = quantity;
        document.getElementById('editDate').value = date;
        document.getElementById('editNotes').value = notes;
        
        if(supplierId) {
            document.getElementById('editSupplier').value = supplierId;
        }
        
        toggleSupplierField('editType');
        openModal('editModal');
    }
    
    // Fungsi untuk hapus transaksi
    function openDeleteModal(id) {
        document.getElementById('deleteId').value = id;
        openModal('deleteModal');
    }
    
    // Toggle supplier field berdasarkan tipe transaksi
    function toggleSupplierField(typeElementId) {
        const type = document.getElementById(typeElementId).value;
        const supplierField = document.getElementById(typeElementId === 'transactionType' ? 'supplierField' : 'editSupplierField');
        
        if(type === 'in') {
            supplierField.classList.remove('hidden');
        } else {
            supplierField.classList.add('hidden');
        }
    }
    
    // Validasi stok
    document.getElementById('productSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stock = selectedOption ? parseInt(selectedOption.getAttribute('data-stock')) : 0;
        const quantityInput = document.querySelector('input[name="quantity"]');
        const warning = document.getElementById('stockWarning');
        
        if(stock > 0) {
            quantityInput.setAttribute('max', stock);
            warning.classList.add('hidden');
        }
    });
    
    document.querySelector('input[name="quantity"]').addEventListener('input', function() {
        const max = parseInt(this.getAttribute('max'));
        const value = parseInt(this.value);
        const warning = document.getElementById('stockWarning');
        
        if(value > max) {
            warning.classList.remove('hidden');
        } else {
            warning.classList.add('hidden');
        }
    });
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi tombol filter supplier
        toggleSupplierField('transactionType');
    });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>