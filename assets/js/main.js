// Confirm before delete
document.addEventListener('DOMContentLoaded', function() {
    // Confirm before delete
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });
    
    // Auto generate SKU
    const skuInput = document.getElementById('sku');
    const generateSkuBtn = document.getElementById('generate-sku');
    
    if (generateSkuBtn && skuInput) {
        generateSkuBtn.addEventListener('click', function() {
            fetch('api/generate_sku.php')
                .then(response => response.json())
                .then(data => {
                    skuInput.value = data.sku;
                });
        });
    }
    
    // Dynamic form for transactions
    const transactionType = document.getElementById('transaction-type');
    const supplierField = document.getElementById('supplier-field');
    
    if (transactionType && supplierField) {
        transactionType.addEventListener('change', function() {
            if (this.value === 'in') {
                supplierField.classList.remove('hidden');
            } else {
                supplierField.classList.add('hidden');
            }
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });
    }
});