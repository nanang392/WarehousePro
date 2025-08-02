<?php
session_start();
require 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = 'Supplier';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        
        if(isset($_POST['action'])) {
            if($_POST['action'] === 'add') {
                // Tambah supplier baru
                $stmt = $conn->prepare("INSERT INTO suppliers (name, contact, phone, email, address) 
                                       VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['contact'],
                    $_POST['phone'],
                    $_POST['email'],
                    $_POST['address']
                ]);
                
                $_SESSION['message'] = 'Supplier berhasil ditambahkan';
                
            } elseif($_POST['action'] === 'edit') {
                // Edit supplier
                $stmt = $conn->prepare("UPDATE suppliers SET 
                                      name = ?, 
                                      contact = ?, 
                                      phone = ?, 
                                      email = ?, 
                                      address = ?
                                      WHERE id = ?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['contact'],
                    $_POST['phone'],
                    $_POST['email'],
                    $_POST['address'],
                    $_POST['id']
                ]);
                
                $_SESSION['message'] = 'Supplier berhasil diperbarui';
                
            } elseif($_POST['action'] === 'delete') {
                // Hapus supplier
                $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                
                $_SESSION['message'] = 'Supplier berhasil dihapus';
            }
        }
        
        $conn->commit();
    } catch(PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    }
    
    header("Location: suppliers.php");
    exit();
}

// Get all suppliers
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM suppliers";

if(!empty($searchQuery)) {
    $query .= " WHERE name LIKE ? OR contact LIKE ? OR phone LIKE ? OR email LIKE ?";
    $params = ["%$searchQuery%", "%$searchQuery%", "%$searchQuery%", "%$searchQuery%"];
} else {
    $params = [];
}

$query .= " ORDER BY name ASC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$suppliers = $stmt->fetchAll();
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
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Supplier</h1>
                <p class="text-gray-600">Daftar seluruh supplier yang bekerja sama</p>
            </div>
            
            <div class="mt-4 md:mt-0">
                <button onclick="openModal('addModal')" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Tambah Supplier
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-6">
            <form method="GET" class="flex">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Cari supplier...">
                </div>
                <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Cari
                </button>
                <?php if(!empty($searchQuery)): ?>
                <a href="suppliers.php" class="ml-2 px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Reset
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabel Supplier -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telepon</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(empty($suppliers)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Tidak ada data supplier
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($suppliers as $supplier): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $supplier['name']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo $supplier['contact'] ?? '-'; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo $supplier['phone'] ?? '-'; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo $supplier['email'] ?? '-'; ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 max-w-xs truncate"><?php echo $supplier['address'] ?? '-'; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="openEditModal(
                                        <?php echo $supplier['id']; ?>,
                                        '<?php echo addslashes($supplier['name']); ?>',
                                        '<?php echo addslashes($supplier['contact'] ?? ''); ?>',
                                        '<?php echo addslashes($supplier['phone'] ?? ''); ?>',
                                        '<?php echo addslashes($supplier['email'] ?? ''); ?>',
                                        '<?php echo addslashes($supplier['address'] ?? ''); ?>'
                                    )" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="openDeleteModal(<?php echo $supplier['id']; ?>)" class="text-red-600 hover:text-red-900">
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
                            Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium">10</span> dari <span class="font-medium"><?php echo count($suppliers); ?></span> hasil
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

    <!-- Modal Tambah Supplier -->
    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Tambah Supplier Baru</h3>
                <button onclick="closeModal('addModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="addSupplierForm" method="POST">
                <input type="hidden" name="action" value="add">
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier*</label>
                        <input type="text" name="name" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kontak</label>
                            <input type="text" name="contact" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input type="tel" name="phone" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="address" rows="3" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Simpan Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Supplier -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Edit Supplier</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editSupplierForm" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier*</label>
                        <input type="text" name="name" id="editName" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kontak</label>
                            <input type="text" name="contact" id="editContact" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input type="tel" name="phone" id="editPhone" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="editEmail" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="address" id="editAddress" rows="3" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus Supplier -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Konfirmasi Hapus</h3>
                <button onclick="closeModal('deleteModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="deleteSupplierForm" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="deleteId">
                
                <p class="text-gray-700 mb-6">Anda yakin ingin menghapus supplier ini? Tindakan ini tidak dapat dibatalkan.</p>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Hapus Supplier
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
    
    // Fungsi untuk edit supplier
    function openEditModal(id, name, contact, phone, email, address) {
        document.getElementById('editId').value = id;
        document.getElementById('editName').value = name;
        document.getElementById('editContact').value = contact;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editEmail').value = email;
        document.getElementById('editAddress').value = address;
        
        openModal('editModal');
    }
    
    // Fungsi untuk hapus supplier
    function openDeleteModal(id) {
        document.getElementById('deleteId').value = id;
        openModal('deleteModal');
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Konfirmasi sebelum submit form
        document.getElementById('deleteSupplierForm').addEventListener('submit', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus supplier ini?')) {
                e.preventDefault();
            }
        });
        
        // Tampilkan pesan dari session
        <?php if(isset($_SESSION['message'])): ?>
            alert('<?php echo $_SESSION['message']; unset($_SESSION['message']); ?>');
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            alert('Error: <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>');
        <?php endif; ?>
    });
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>