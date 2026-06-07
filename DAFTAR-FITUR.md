# Larashop — Daftar Fitur

E-commerce produk pertanian. Terdiri dari **Storefront (customer)** dan **Panel Admin**.

---

## 🛒 STOREFRONT (Customer)

### Tanpa Login (Guest)
- [x] Lihat katalog produk
- [x] Pencarian produk (nama / kategori / kebutuhan)
- [x] Filter kategori, status, dan urutan (harga termurah/termahal, nama A–Z)
- [x] Lihat halaman detail produk
- [x] Galeri foto produk + zoom (lightbox)
- [x] Lihat varian produk (ukuran/berat, harga, stok)
- [x] Lihat keunggulan produk & rekomendasi produk lain
- [x] Badge diskon, harga coret, dan label terjual

### Autentikasi
- [x] Registrasi akun (nama, username, no. HP, email, password)
- [x] Login (username / email / no. HP)
- [x] Tampil/sembunyikan password (toggle)
- [x] Logout

### Keranjang
- [x] Tambah produk ke keranjang (pilih varian & jumlah)
- [x] Ubah jumlah item (stepper +/−)
- [x] Hapus item
- [x] Centang item yang akan di-checkout (pilih semua)
- [x] Ringkasan harga otomatis
- [x] Badge jumlah keranjang di navigasi

### Checkout & Pembayaran
- [x] Pilih alamat pengiriman
- [x] Pilih layanan kurir + **ongkir otomatis**
- [x] Gunakan saldo **kode unik** (store credit) sebagai potongan
- [x] Ringkasan pembayaran (produk + ongkir + total transfer)
- [x] Buat pesanan (pembayaran transfer manual)
- [x] Beli langsung dari halaman produk

### Akun & Profil
- [x] Lihat & edit profil (nama, username, email, no. HP)
- [x] Ganti password

### Buku Alamat
- [x] Simpan banyak alamat
- [x] Tambah / edit / hapus alamat
- [x] Pencarian wilayah otomatis (provinsi, kota, kecamatan, kode pos)
- [x] Tetapkan alamat utama

### Pesanan
- [x] Riwayat pesanan dengan filter status (Belum bayar, Dibayar, Diproses, Dikirim, Selesai, Dibatalkan)
- [x] Detail pesanan: timeline status, rincian item, pengiriman + resi (AWB), ringkasan pembayaran
- [x] Batalkan pesanan (saat belum bayar)
- [x] Tandai pesanan sudah diterima
- [x] Konfirmasi pembayaran via WhatsApp

---

## 🛠️ PANEL ADMIN

### Autentikasi & Dashboard
- [x] Login admin (panel terpisah)
- [x] Dashboard ringkasan
- [x] Logout

### Manajemen Produk
- [x] Daftar produk
- [x] Tambah produk
- [x] Lihat detail produk
- [x] Edit produk (termasuk varian & foto)

### Manajemen Pesanan
- [x] Daftar pesanan
- [x] Lihat detail pesanan
- [x] Validasi pembayaran
- [x] Proses pengiriman (buat shipment)
- [x] Tandai pesanan selesai
- [x] Batalkan pesanan

### Manajemen Pengiriman
- [x] Daftar shipment
- [x] Lihat detail shipment
- [x] Pengaturan pengiriman / gudang asal (origin)
- [x] Pencarian wilayah origin

### Manajemen Customer
- [x] Daftar customer
- [x] Tambah customer
- [x] Lihat detail customer
- [x] Edit customer

### Manajemen Akun Staff
- [x] Daftar akun
- [x] Tambah akun
- [x] Lihat detail akun
- [x] Edit akun
- [x] Hapus akun

---

## 🎨 Desain & Pengalaman
- [x] **Mobile-first** + tampilan desktop
- [x] Navigasi bawah berikon (mobile) & top-nav (desktop) + badge keranjang
- [x] Identitas premium: tipografi serif elegan + sans modern, palet hangat (cream + hijau), sudut membulat, shadow lembut
- [x] Notifikasi sukses/error yang konsisten
- [x] Antarmuka berbahasa Indonesia

---

## 🚀 Rencana Pengembangan (Roadmap)

### Integrasi Logistik Penuh (End-to-End)
Saat ini sistem sudah terhubung untuk **cek ongkir otomatis**. Tahap berikutnya: integrasi penuh dengan penyedia logistik sehingga seluruh alur pengiriman berjalan otomatis tanpa input manual.

- [ ] **Buat order pengiriman otomatis** ke kurir/logistik langsung dari sistem (booking, bukan hanya cek tarif)
- [ ] **Generate resi & label pengiriman otomatis** (AWB langsung dari penyedia logistik)
- [ ] **Penjadwalan pickup** — kurir dijadwalkan menjemput barang dari gudang
- [ ] **Pelacakan pengiriman real-time** (tracking) hingga barang diterima
- [ ] **Update status otomatis** ke pesanan & notifikasi ke customer di setiap tahap
- [ ] Dukungan **multi-kurir / multi-ekspedisi** dalam satu alur

### Potensi Pengembangan Lain
- [ ] Pembayaran online otomatis (payment gateway) — selain transfer manual
- [ ] Notifikasi otomatis via WhatsApp/email (konfirmasi, status pesanan)
- [ ] Laporan & analitik penjualan untuk admin
- [ ] Ulasan & rating produk oleh customer
