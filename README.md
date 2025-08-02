# Warehouse Pro: Sistem Manajemen Gudang Modern 📦

<p align="center">
  <img style="margin-right: 8px;" src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img style="margin-right: 8px;" src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5">
  <img style="margin-right: 8px;" src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
  <img style="margin-right: 8px;" src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <a href="https://github.com/nanang392/WarehousePro"><img src="https://img.shields.io/badge/GitHub-181717?style=for-the-badge&logo=github&logoColor=white" alt="GitHub"></a>
</p>

Warehouse Pro adalah sistem manajemen gudang berbasis web yang dirancang untuk menyederhanakan dan mengoptimalkan operasi gudang Anda. Sistem ini menyediakan berbagai fitur untuk membantu Anda melacak inventaris, mengelola transaksi, dan menghasilkan laporan. Dengan antarmuka yang intuitif dan mudah digunakan, Warehouse Pro memungkinkan Anda meningkatkan efisiensi dan mengurangi kesalahan.

Sistem ini dirancang untuk membantu bisnis mengelola stok barang, melacak pergerakan barang, dan menghasilkan laporan penting untuk pengambilan keputusan. Warehouse Pro memungkinkan Anda untuk memiliki visibilitas penuh atas inventaris Anda dan mengelola operasi gudang Anda dengan lebih efektif.

## Fitur Utama ✨

*   **Manajemen Inventaris 📦**: Kelola stok barang dengan mudah, termasuk menambah, mengurangi, dan memperbarui informasi produk.
*   **Pelaporan Mendalam 📊**: Dapatkan wawasan berharga dengan laporan yang dapat disesuaikan, termasuk tingkat stok, transaksi, dan lainnya. Export laporan penting ke format yang berbeda.
*   **Manajemen Transaksi 🧾**: Catat dan kelola semua transaksi gudang, termasuk penerimaan, pengiriman, dan penyesuaian.
*   **Dashboard Interaktif 📈**: Dapatkan gambaran umum yang jelas tentang kinerja gudang Anda dengan dashboard yang intuitif.
*   **Manajemen Supplier 🚚**: Kelola data supplier, export data supplier dan lacak data supplier dengan mudah.

## Tech Stack 🛠️

*   PHP 🐘
*   HTML 🧱
*   JavaScript ☕
*   Tailwind CSS 🎨
*   MySQL (kemungkinan, berdasarkan `config/database.php`) 🗄️

## Instalasi & Menjalankan 🚀

1.  Clone repositori:
    ```bash
    git clone https://github.com/nanang392/WarehousePro
    ```
2.  Masuk ke direktori:
    ```bash
    cd WarehousePro
    ```
3.  Instal dependensi (Asumsi menggunakan Composer untuk manajemen dependensi PHP):
    ```bash
    composer install
    ```
4.  Konfigurasi database:
    *   Edit file `config/database.php` dan masukkan informasi koneksi database Anda (host, username, password, nama database).
5. Jalankan proyek (dengan server PHP bawaan):

    ```bash
    php -S localhost:8000 -t .
    ```

    Buka browser Anda dan kunjungi `http://localhost:8000`. Atau konfigurasi melalui web server seperti Apache atau Nginx.

## Cara Berkontribusi 🤝

1.  Fork repositori ini.
2.  Buat branch baru untuk fitur atau perbaikan Anda: `git checkout -b nama-fitur`
3.  Lakukan perubahan dan commit: `git commit -m "Tambahkan fitur baru"`
4.  Push ke branch Anda: `git push origin nama-fitur`
5.  Buat Pull Request ke branch `main` repositori ini.
