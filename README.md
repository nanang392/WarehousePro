# Warehouse Pro: Sistem Manajemen Gudang Modern 📦

<p align="center">
  <img src="assets/logogudang.png" alt="WarehousePro Logo" width="200">
</p>

<p align="center">
    <a href="https://shields.io/">
        <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" style="margin-right: 8px;" alt="PHP Badge">
    </a>
    <a href="https://shields.io/">
        <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" style="margin-right: 8px;" alt="HTML5 Badge">
    </a>
    <a href="https://shields.io/">
        <img src="https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white" style="margin-right: 8px;" alt="Tailwind CSS Badge">
    </a>
    <a href="https://shields.io/">
        <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" style="margin-right: 8px;" alt="JavaScript Badge">
    </a>
    <a href="https://shields.io/">
        <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" style="margin-right: 8px;" alt="MySQL Badge">
    </a>
</p>

Warehouse Pro adalah sistem manajemen gudang yang dirancang untuk membantu bisnis mengoptimalkan operasi penyimpanan mereka. Dengan antarmuka yang intuitif dan fitur-fitur canggih, Warehouse Pro menyederhanakan pelacakan inventaris, pengelolaan pesanan, dan pelaporan. Sistem ini dibangun dengan PHP, HTML, Tailwind CSS, dan JavaScript, dengan database MySQL.

## Fitur Utama ✨

*   **📈 Analisis Inventaris *Real-time***: Dapatkan visibilitas penuh atas inventaris Anda dengan pemantauan real-time, dasbor interaktif, dan laporan yang dapat disesuaikan.
*   **🚚 Manajemen Alur Kerja yang Efisien**: Otomatiskan alur kerja penerimaan, penyimpanan, pengambilan, dan pengiriman untuk meningkatkan efisiensi dan mengurangi kesalahan.
*   **📊 Laporan dan Analisis yang Mendalam**: Hasilkan laporan mendetail tentang kinerja inventaris, tren penjualan, dan efisiensi operasional untuk pengambilan keputusan yang lebih baik.
*   **API 🌐**: Tersedia API untuk integrasi dengan sistem lain seperti e-commerce atau akuntansi. Memudahkan pertukaran data dan otomatisasi proses.

## Tech Stack 🛠️

*   Bahasa Pemrograman: PHP
*   Frontend: HTML, JavaScript, Tailwind CSS
*   Database: MySQL
*   API

## Instalasi & Menjalankan 🚀

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan WarehousePro:

1.  Clone repositori:
    ```bash
    git clone https://github.com/nanang392/WarehousePro
    ```
2.  Masuk ke direktori:
    ```bash
    cd WarehousePro
    ```
3.  Install dependensi (dengan Composer):
    ```bash
    composer install
    ```
4.  Konfigurasi database:
    *   Edit file `config/database.php` dengan informasi database MySQL Anda (nama host, nama database, nama pengguna, kata sandi).
5.  Jalankan server PHP (misalnya, menggunakan server bawaan PHP):
    ```bash
    php -S localhost:8000 -t .
    ```
    Atau, konfigurasi server web seperti Apache atau Nginx untuk menunjuk ke direktori proyek.
6.  Buka browser dan kunjungi `http://localhost:8000` (atau alamat yang Anda konfigurasi untuk server web Anda).

## Cara Berkontribusi 🤝

Kami menyambut kontribusi dari komunitas! Untuk berkontribusi pada WarehousePro, ikuti langkah-langkah berikut:

1.  Fork repositori ini.
2.  Buat branch untuk fitur atau perbaikan bug Anda: `git checkout -b feature/nama-fitur` atau `git checkout -b fix/nama-bug`
3.  Lakukan commit dengan pesan yang jelas dan deskriptif: `git commit -m "Menambahkan fitur baru"`
4.  Push ke branch Anda: `git push origin feature/nama-fitur`
5.  Buat Pull Request ke branch `main` di repositori ini.
