<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); 
}

$pageTitle = 'Manajemen Produk';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                // Tambah produk baru
                $stmt = $conn->prepare("INSERT INTO products (sku, name, category, description, unit, stock, min_stock, location) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['sku'],
                    $_POST['name'],
                    $_POST['category'],
                    $_POST['description'],
                    $_POST['unit'],
                    $_POST['stock'] ?? 0,
                    $_POST['min_stock'] ?? 5,
                    $_POST['location']
                ]);
                
                $_SESSION['message'] = 'Produk berhasil ditambahkan';
                
            } elseif ($_POST['action'] === 'edit') {
                // Edit produk
                $stmt = $conn->prepare("UPDATE products SET 
                                      sku = ?,
                                      name = ?,
                                      category = ?,
                                      description = ?,
                                      unit = ?,
                                      stock = ?,
                                      min_stock = ?,
                                      location = ?
                                      WHERE id = ?");
                $stmt->execute([
                    $_POST['sku'],
                    $_POST['name'],
                    $_POST['category'],
                    $_POST['description'],
                    $_POST['unit'],
                    $_POST['stock'],
                    $_POST['min_stock'],
                    $_POST['location'],
                    $_POST['id']
                ]);
                
                $_SESSION['message'] = 'Produk berhasil diperbarui';
                
            } elseif ($_POST['action'] === 'delete') {
                // Hapus produk
                $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                
                $_SESSION['message'] = 'Produk berhasil dihapus';
            }
        }
        
        $conn->commit();
    } catch(PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
    
    header("Location: products.php");
    exit();
}

// Get all products
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM products";

if(!empty($searchQuery)) {
    $query .= " WHERE name LIKE ? OR sku LIKE ? OR category LIKE ? OR description LIKE ?";
    $params = ["%$searchQuery%", "%$searchQuery%", "%$searchQuery%", "%$searchQuery%"];
} else {
    $params = [];
}

$query .= " ORDER BY name";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WarehousePro - <?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="assets/logo.png" type="image/x-icon">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <main class="flex-1 p-6">
        <!-- Header dengan search -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?php echo $pageTitle; ?></h1>
                <p class="text-gray-600">Daftar seluruh produk di gudang</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <button onclick="openModal('addModal')" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah Produk
                </button>
            </div>
        </div>

        <!-- Pesan -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <form method="GET" class="flex">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Cari produk...">
                </div>
                <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Cari
                </button>
                <?php if(!empty($searchQuery)): ?>
                <a href="products.php" class="ml-2 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Reset
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabel Produk -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(empty($products)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data produk
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($products as $product): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $product['sku']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $product['name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $product['description']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $product['category']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $product['stock'] <= $product['min_stock'] ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo $product['stock']; ?> <?php echo $product['unit']; ?>
                                        </span>
                                        <?php if($product['stock'] <= $product['min_stock']): ?>
                                        <span class="ml-2 text-xs text-yellow-600">(min: <?php echo $product['min_stock']; ?>)</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $product['location']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="openEditModal(
                                        <?php echo $product['id']; ?>,
                                        '<?php echo addslashes($product['sku']); ?>',
                                        '<?php echo addslashes($product['name']); ?>',
                                        '<?php echo addslashes($product['category']); ?>',
                                        '<?php echo addslashes($product['description']); ?>',
                                        '<?php echo addslashes($product['unit']); ?>',
                                        <?php echo $product['stock']; ?>,
                                        <?php echo $product['min_stock']; ?>,
                                        '<?php echo addslashes($product['location']); ?>'
                                    )" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="openDeleteModal(<?php echo $product['id']; ?>)" class="text-red-600 hover:text-red-900">
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
                            Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium">10</span> dari <span class="font-medium"><?php echo count($products); ?></span> hasil
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

    <!-- Modal Tambah Produk -->
    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Tambah Produk Baru</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addProductForm" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU*</label>
                        <div class="flex rounded-md shadow-sm">
                            <input type="text" name="sku" id="sku" required 
                                   class="flex-1 block w-full rounded-none rounded-l-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <button type="button" id="generate-sku" 
                                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                                Generate
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk*</label>
                        <input type="text" name="name" required 
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="category" 
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="3" 
                                  class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan*</label>
                            <select name="unit" required 
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="pcs">pcs</option>
                                <option value="kg">kg</option>
                                <option value="gram">gram</option>
                                <option value="liter">liter</option>
                                <option value="box">box</option>
                                <option value="pack">pack</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal*</label>
                            <input type="number" name="stock" min="0" value="0" required 
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min. Stok*</label>
                            <input type="number" name="min_stock" min="1" value="5" required 
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Gudang</label>
                        <input type="text" name="location" 
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Produk -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Edit Produk</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editProductForm" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU*</label>
                        <input type="text" name="sku" id="editSku" required 
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk*</label>
                        <input type="text" name="name" id="editName" required 
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="category" id="editCategory" 
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="editDescription" rows="3" 
                                  class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satuan*</label>
                            <select name="unit" id="editUnit" required 
                                    class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="pcs">pcs</option>
                                <option value="kg">kg</option>
                                <option value="gram">gram</option>
                                <option value="liter">liter</option>
                                <option value="box">box</option>
                                <option value="pack">pack</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok*</label>
                            <input type="number" name="stock" id="editStock" min="0" required 
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min. Stok*</label>
                            <input type="number" name="min_stock" id="editMinStock" min="1" required 
                                   class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Gudang</label>
                        <input type="text" name="location" id="editLocation" 
                               class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('editModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus Produk -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Konfirmasi Hapus</h3>
                <button onclick="closeModal('deleteModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="deleteProductForm" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteId">
                
                <p class="text-gray-700 mb-6">Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.</p>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('deleteModal')" 
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Hapus Produk
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
    
    // Fungsi untuk edit produk
    function openEditModal(id, sku, name, category, description, unit, stock, minStock, location) {
        document.getElementById('editId').value = id;
        document.getElementById('editSku').value = sku;
        document.getElementById('editName').value = name;
        document.getElementById('editCategory').value = category;
        document.getElementById('editDescription').value = description;
        document.getElementById('editUnit').value = unit;
        document.getElementById('editStock').value = stock;
        document.getElementById('editMinStock').value = minStock;
        document.getElementById('editLocation').value = location;
        
        openModal('editModal');
    }
    
    // Fungsi untuk hapus produk
    function openDeleteModal(id) {
        document.getElementById('deleteId').value = id;
        openModal('deleteModal');
    }
    
    // Generate SKU
    document.getElementById('generate-sku').addEventListener('click', function() {
        const prefix = 'PRD-';
        const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        const date = new Date().toISOString().slice(0, 10).replace(/-/g, '');
        document.getElementById('sku').value = prefix + date + '-' + random;
    });
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Konfirmasi sebelum hapus
        document.getElementById('deleteProductForm').addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                e.preventDefault();
            }
        });
    });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>