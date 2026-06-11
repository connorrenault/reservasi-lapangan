# 🏟️ Reservasi Lapangan

**Reservasi Lapangan** adalah sebuah aplikasi sistem pemesanan dan manajemen lapangan (seperti lapangan futsal, badminton, basket, atau tenis) berbasis web. Aplikasi ini dirancang untuk mempermudah masyarakat dalam melakukan booking lapangan secara online tanpa harus datang langsung ke lokasi, serta membantu pemilik lapangan dalam mengelola jadwal dan pembayaran.

Tujuan utama proyek ini adalah menyediakan platform yang transparan dan efisien, di mana pengguna dapat melihat ketersediaan jadwal secara *real-time*, melakukan reservasi, dan admin dapat memantau seluruh transaksi serta jadwal sewa dengan mudah.

---

## 👥 Hak Akses & Fitur Aplikasi

Aplikasi ini menggunakan sistem *Role-Based Access Control* (RBAC) dengan dua peran utama: **Admin** dan **User (Penyewa)**.

### 1. Fitur User (Pelanggan/Penyewa)
* **Cari Lapangan:** Melihat daftar lapangan yang tersedia beserta fasilitas dan harganya.
* **Cek Jadwal Real-Time:** Melihat kalender atau jam berapa saja lapangan yang sudah di-booking orang lain agar tidak bentrok.
* **Booking Online:** Melakukan pemesanan lapangan dengan memilih tanggal, jam, dan durasi bermain.
* **Riwayat Pesanan:** Memantau status booking (Menunggu Pembayaran, Diterima, atau Selesai).

### 2. Fitur Admin (Pengelola Lapangan)
* **Manajemen Lapangan:** Menambah, mengubah, atau menghapus data lapangan serta mengatur harga sewa per jam.
* **Konfirmasi Pembayaran:** Memverifikasi bukti transfer atau pembayaran yang dikirimkan oleh user.
* **Manajemen Jadwal:** Melihat dan mengatur seluruh jadwal booking masuk untuk menghindari jadwal ganda (bentrok).
* **Laporan Pendapatan:** Melihat total pemasukan dari hasil penyewaan lapangan secara berkala.

---

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan kombinasi teknologi modern berikut:

* **Backend / Framework:** PHP / Laravel
* **Database:** MySQL
* **Frontend:** Bootstrap 5 / Tailwind CSS
* **Ikon & Emoji:** FontAwesome & Standard Unicode Emojis
