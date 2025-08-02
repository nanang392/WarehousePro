<?php
session_start();
require 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Set page title
$page_title = "Warehouse Reports";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="assets/logo.png" type="image/x-icon">
    <script>
    function printReport() {
        // Get the currently active report
        const activeReport = document.querySelector('.report-content:not(.hidden)');
        const reportTitle = activeReport.querySelector('h3').textContent;
        const table = activeReport.querySelector('table').outerHTML;
        
        // Create a print window
        const printWindow = window.open('', '_blank');
        
        // Write the print content
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>${reportTitle}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #333; text-align: center; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .print-header { margin-bottom: 30px; text-align: center; }
                    .print-footer { margin-top: 30px; text-align: right; font-size: 12px; color: #666; }
                    .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; }
                    .bg-red-100 { background-color: #fee2e2; }
                    .text-red-800 { color: #991b1b; }
                    .bg-green-100 { background-color: #dcfce7; }
                    .text-green-800 { color: #166534; }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h1>${reportTitle}</h1>
                    <p>Printed on: ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
                </div>
                ${table}
                <div class="print-footer">
                    Printed by: <?php echo htmlspecialchars($_SESSION['username'] ?? 'System'); ?>
                </div>
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    function exportReport() {
        // Get the currently active tab
        const activeTab = document.querySelector('.tab-button.border-blue-500');
        const reportType = activeTab.getAttribute('data-tab');
        
        // Redirect to the appropriate export URL
        window.location.href = `export_${reportType}.php`;
    }
    </script>
</head>
<body class="bg-gray-50">
    <?php 
    $header_path = 'includes/header.php';
    if (file_exists($header_path)) {
        include $header_path;
    } else {
        // Fallback minimal header
        echo '<header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-xl font-bold text-gray-900">'.htmlspecialchars($page_title).'</h1>
                </div>
              </header>';
    }
    ?>
    
<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Warehouse Reports</h1>
            <div class="flex space-x-3">
               <button onclick="printReport()" class="inline-flex items-center gap-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-print"></i>
                Print Report
                </button>
                <button onclick="exportReport()" class="inline-flex items-center gap-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </button>
            </div>
        </div>
        
        <!-- Report Navigation Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button data-tab="inventory" class="tab-button border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Inventory Status
                </button>
                <button data-tab="lowstock" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Low Stock Items
                </button>
                <button data-tab="transactions" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Recent Transactions
                </button>
                <button data-tab="suppliers" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Supplier Summary
                </button>
            </nav>
        </div>
        
        <!-- Report Content -->
        <div>
            <!-- Report 1: Inventory Status -->
            <div id="inventory" class="report-content">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Current Inventory Status</h3>
                <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">SKU</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Product Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Current Stock</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Min Stock</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Location</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php
                            try {
                                $query = "SELECT sku, name, category, stock, min_stock, location 
                                          FROM products 
                                          ORDER BY category, name";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $status = ($row['stock'] <= $row['min_stock']) ? 
                                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Low Stock</span>' : 
                                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">OK</span>';
                                        
                                    echo "<tr>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['sku'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['name'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['category'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['stock'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['min_stock'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".$status."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['location'])."</td>
                                          </tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='7' class='px-3 py-4 text-sm text-red-600'>Error loading inventory data: ".htmlspecialchars($e->getMessage())."</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Report 2: Low Stock Items -->
            <div id="lowstock" class="report-content hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Low Stock Items (Below Minimum Threshold)</h3>
                <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">SKU</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Product Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Current Stock</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Min Stock</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Deficit</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Location</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php
                            try {
                                $query = "SELECT sku, name, category, stock, min_stock, location 
                                          FROM products 
                                          WHERE stock <= min_stock
                                          ORDER BY (min_stock - stock) DESC";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $deficit = $row['min_stock'] - $row['stock'];
                                        echo "<tr>
                                                <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['sku'])."</td>
                                                <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['name'])."</td>
                                                <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['category'])."</td>
                                                <td class='whitespace-nowrap px-3 py-4 text-sm font-medium text-red-600'>".htmlspecialchars($row['stock'])."</td>
                                                <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['min_stock'])."</td>
                                                <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($deficit)."</td>
                                                <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['location'])."</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='px-3 py-4 text-sm text-gray-500 text-center'>No low stock items found</td></tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='7' class='px-3 py-4 text-sm text-red-600'>Error loading low stock data: ".htmlspecialchars($e->getMessage())."</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Report 3: Recent Transactions -->
            <div id="transactions" class="report-content hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions (Last 30 Days)</h3>
                <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Product</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">SKU</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Quantity</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">User</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php
                            try {
                                $query = "SELECT t.date, p.name, p.sku, t.type, t.quantity, u.fullname, t.notes
                                          FROM transactions t
                                          JOIN products p ON t.product_id = p.id
                                          JOIN users u ON t.user_id = u.id
                                          WHERE t.date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                          ORDER BY t.date DESC
                                          LIMIT 100";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $type_class = ($row['type'] == 'in') ? 'text-green-600' : 'text-red-600';
                                    $type_text = ($row['type'] == 'in') ? 'IN (+)' : 'OUT (-)';
                                    
                                    echo "<tr>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['date'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['name'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['sku'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm font-medium {$type_class}'>".htmlspecialchars($type_text)."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['quantity'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['fullname'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['notes'])."</td>
                                          </tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='7' class='px-3 py-4 text-sm text-red-600'>Error loading transaction data: ".htmlspecialchars($e->getMessage())."</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Report 4: Supplier Summary -->
            <div id="suppliers" class="report-content hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Supplier Summary</h3>
                <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Supplier</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Contact</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phone</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Products Supplied</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Last Delivery</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php
                            try {
                                $query = "SELECT s.name, s.contact, s.phone, s.email, 
                                              COUNT(DISTINCT t.product_id) as product_count,
                                              MAX(t.date) as last_delivery
                                      FROM suppliers s
                                      LEFT JOIN transactions t ON s.id = t.supplier_id AND t.type = 'in'
                                      GROUP BY s.id
                                      ORDER BY s.name";
                                $stmt = $conn->prepare($query);
                                $stmt->execute();
                                
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $product_count = $row['product_count'] ? $row['product_count'] : 0;
                                    $last_delivery = $row['last_delivery'] ? $row['last_delivery'] : 'Never';
                                    
                                    echo "<tr>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['name'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['contact'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['phone'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($row['email'])."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($product_count)."</td>
                                            <td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>".htmlspecialchars($last_delivery)."</td>
                                          </tr>";
                                }
                            } catch (PDOException $e) {
                                echo "<tr><td colspan='6' class='px-3 py-4 text-sm text-red-600'>Error loading supplier data: ".htmlspecialchars($e->getMessage())."</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all tab buttons and content areas
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.report-content');
    
    // Function to switch tabs
    function switchTab(event) {
        // Remove active class from all tabs
        tabButtons.forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });
        
        // Add active class to clicked tab
        this.classList.add('border-blue-500', 'text-blue-600');
        this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        
        // Hide all content
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show corresponding content
        const targetId = this.getAttribute('data-tab');
        document.getElementById(targetId).classList.remove('hidden');
    }
    
    // Add click event to all tab buttons
    tabButtons.forEach(button => {
        button.addEventListener('click', switchTab);
    });
});
</script>
<?php include 'includes/footer.php'; ?>
</body>
</html>