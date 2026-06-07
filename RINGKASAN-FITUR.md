# Larashop — Ringkasan Fitur Frontend

**Larashop** adalah aplikasi e-commerce produk pertanian (benih, pupuk, pestisida, alat tani) dengan tampilan **premium, mobile-first, dan berbahasa Indonesia** — dirancang untuk "Sobat Petani" namun terasa eksklusif seperti butik modern.

---

## Tipe Pengguna

| Pengguna | Akses | Kebutuhan login |
|---|---|---|
| **Pengunjung (Guest)** | Lihat katalog & detail produk | Tidak perlu |
| **Customer** | Belanja penuh: keranjang, checkout, pesanan, alamat, profil | Perlu akun |
| **Admin / Staff** | Kelola produk, pesanan, pembayaran, pengiriman, customer | Panel terpisah |

---

## Fitur Customer (Storefront)

**Belanja & Produk**
- Katalog produk dengan **pencarian, filter** (kategori, status, urutan harga/nama)
- Halaman detail produk: galeri foto + zoom, **pilihan varian** (ukuran/berat/harga/stok), keunggulan produk, dan rekomendasi produk lain
- Badge diskon, harga coret, dan label "terjual"

**Keranjang & Checkout**
- Keranjang dengan atur jumlah, pilih item yang akan di-checkout, dan ringkasan harga real-time
- Checkout: pilih alamat kirim, pilih **layanan kurir + ongkir otomatis**, dan ringkasan pembayaran
- Dukungan **saldo kode unik** (store credit) sebagai potongan pembayaran
- Pembayaran transfer manual + konfirmasi via WhatsApp

**Akun & Pesanan**
- Registrasi & login (username/email/no. HP)
- Manajemen **profil** (data diri & ganti password)
- **Buku alamat**: simpan banyak alamat, tandai alamat utama, pencarian wilayah otomatis
- **Riwayat pesanan** dengan filter status (Belum bayar, Dibayar, Diproses, Dikirim, Selesai, Dibatalkan)
- **Detail pesanan**: timeline status, rincian item, info pengiriman + resi (AWB), dan ringkasan pembayaran
- Aksi mandiri: batalkan pesanan, tandai pesanan diterima

---

## Fitur Admin (Ringkas)

- Dashboard ringkasan & manajemen **produk** (termasuk varian & foto)
- Manajemen **pesanan**: validasi pembayaran, proses pengiriman, penyelesaian, pembatalan
- Manajemen **customer**, **pengaturan pengiriman/gudang**, dan **akun staff**

---

## Keunggulan Desain & Pengalaman

- **Mobile-first** dengan navigasi bawah berikon + badge keranjang; tampilan desktop dengan top-nav rapi
- Identitas visual **premium**: tipografi serif elegan (judul) + sans modern (isi), palet hangat (cream + hijau emerald), sudut membulat, dan bayangan lembut berlapis
- Konsisten di seluruh halaman, ramah disentuh, dan cepat dipahami

## Teknologi

- **Backend**: Laravel (REST API) — sumber data & logika bisnis
- **Frontend**: Laravel Blade + Tailwind CSS (server-rendered, ringan & cepat)
- **Autentikasi**: token (Laravel Sanctum)
- **Integrasi pengiriman**: kurir via agregator ongkir (mis. Biteship/RajaOngkir)
- **Database**: MySQL
