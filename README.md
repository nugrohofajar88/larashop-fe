# Overview Aplikasi, Fitur, dan Flow

Dokumen ini menjelaskan gambaran umum aplikasi, fitur utama untuk customer dan admin, alur proses bisnis, status order, integrasi ekspedisi, serta rencana pengembangan berikutnya.

## Daftar Isi

- [1. Overview Aplikasi](#1-overview-aplikasi)
- [2. Tujuan Aplikasi](#2-tujuan-aplikasi)
- [3. Platform](#3-platform)
- [4. Fitur Customer](#4-fitur-customer)
- [5. Fitur Admin](#5-fitur-admin)
- [6. Flow Customer](#6-flow-customer)
- [7. Flow Admin](#7-flow-admin)
- [8. Flow Pengiriman](#8-flow-pengiriman)
- [9. Status Order](#9-status-order)
- [10. Integrasi Ekspedisi](#10-integrasi-ekspedisi)
- [11. Pembayaran](#11-pembayaran)
- [12. Teknologi](#12-teknologi)
- [13. Rencana Pengembangan Berikutnya](#13-rencana-pengembangan-berikutnya)

---

## 1. Overview Aplikasi

Aplikasi ini merupakan sistem penjualan kebutuhan pertanian berbasis mobile/web yang memungkinkan customer melakukan pemesanan produk secara online dengan proses pengiriman menggunakan jasa ekspedisi JNT.

Sistem dirancang sederhana untuk tahap awal:

- pembayaran dilakukan melalui transfer manual
- konfirmasi pembayaran melalui WhatsApp admin
- admin memproses pengiriman setelah pembayaran tervalidasi
- integrasi API ekspedisi digunakan saat proses pengiriman untuk membuat order shipment dan mendapatkan nomor resi/AWB

Fokus utama aplikasi:

- mempermudah penjualan kebutuhan pertanian
- mempermudah pengelolaan order
- mempermudah pengiriman luar kota
- mempermudah pengecekan ongkir otomatis
- mempermudah admin dalam validasi pembayaran dan pengiriman

---

## 2. Tujuan Aplikasi

### Customer

- dapat melihat katalog produk pertanian
- dapat memesan produk secara online
- dapat mengetahui estimasi ongkir
- dapat melakukan checkout dengan mudah
- dapat mengetahui status pesanan

### Admin

- dapat mengelola katalog produk
- dapat memproses pesanan customer
- dapat melakukan validasi pembayaran
- dapat membuat order pengiriman ekspedisi langsung dari sistem
- dapat memperoleh nomor resi otomatis dari API ekspedisi

---

## 3. Platform

### Customer

- Mobile App / Web App

### Admin

- Web Admin Panel

---

## 4. Fitur Customer

### Authentication

- Register
- Login
- Logout

### Profile

- Edit profil
- Edit nomor WhatsApp

### Address

- Tambah alamat pengiriman
- Edit alamat pengiriman
- Pilih alamat utama

### Product Catalog

- Lihat daftar produk
- Lihat detail produk
- Cari produk
- Filter produk

### Cart

- Tambah ke keranjang
- Edit jumlah barang
- Hapus barang

### Checkout

- Pilih alamat pengiriman
- Cek ongkir otomatis
- Pilih layanan pengiriman
- Lihat total pembayaran
- Mendapat kode unik pembayaran

### Order

- Lihat daftar pesanan
- Lihat status pesanan
- Lihat nomor resi
- Tracking pengiriman

### Payment Confirmation

- Tombol konfirmasi via WhatsApp admin

---

## 5. Fitur Admin

### Authentication

- Login admin
- Logout admin

### Product Management

- Tambah produk
- Edit produk
- Hapus produk
- Upload gambar produk
- Atur stok produk
- Atur berat produk
- Atur dimensi produk

### Shipping Origin

- Atur lokasi asal pengiriman
- Digunakan untuk cek ongkir otomatis

### Order Management

- Lihat daftar pesanan
- Filter pesanan berdasarkan status
- Cari pesanan
- Lihat detail pesanan

### Payment Validation

- Validasi transfer customer
- Cari berdasarkan nomor order
- Cari berdasarkan nomor WhatsApp
- Cari berdasarkan jumlah transfer unik

### Shipment Processing

- Create shipment melalui API ekspedisi
- Generate nomor resi/AWB
- Simpan data pengiriman
- Update status pengiriman

### Dashboard

- Statistik pesanan
- Statistik penjualan
- Statistik produk

---

## 6. Flow Customer

### 1. Register

Customer melakukan registrasi dengan data:

- username
- password
- nomor WhatsApp

### 2. Login

Customer login ke aplikasi.

### 3. Browse Product

Customer melihat katalog produk.

### 4. Add to Cart

Customer menambahkan produk ke keranjang.

### 5. Checkout

Pada tahap checkout:

- customer memilih alamat pengiriman
- sistem menghitung ongkir otomatis
- customer memilih layanan pengiriman
- sistem menghitung total pembayaran
- sistem menambahkan kode unik pembayaran

### 6. Submit Order

Order dibuat dengan status:

```text
pending_payment
```

### 7. Transfer Payment

Customer melakukan transfer manual ke rekening admin.

### 8. WhatsApp Confirmation

Customer mengirim bukti transfer melalui WhatsApp admin.

---

## 7. Flow Admin

### 1. View New Orders

Admin melihat daftar order baru dengan status:

```text
pending_payment
```

### 2. Validate Payment

Admin melakukan:

- pengecekan mutasi transfer
- pencocokan nominal unik
- pencocokan nomor order

Jika valid, status berubah menjadi:

```text
paid
```

### 3. Process Shipment

Admin klik tombol:

```text
Process Shipment
```

Sistem akan:

- mengirim request ke API ekspedisi
- membuat order pengiriman
- mendapatkan nomor resi/AWB

### 4. Shipment Created

Status order berubah menjadi:

```text
shipped
```

Customer dapat melihat nomor resi.

---

## 8. Flow Pengiriman

### 1. Cek Ongkir

Saat checkout, sistem mengirim request cek ongkir menggunakan:

- berat produk
- dimensi produk
- alamat tujuan
- lokasi asal pengiriman

### 2. Create Shipment

Saat admin memproses order, sistem membuat shipment melalui API dan mendapatkan:

- nomor resi/AWB
- status shipment
- data shipment

### 3. Tracking

Customer dapat melakukan tracking pengiriman menggunakan nomor resi.

---

## 9. Status Order

### `pending_payment`

Order dibuat tetapi belum dibayar.

### `waiting_confirmation`

Customer sudah konfirmasi transfer.

### `paid`

Pembayaran tervalidasi admin.

### `processing`

Admin sedang packing atau proses pengiriman.

### `shipped`

Barang sudah dikirim dan memiliki nomor resi.

### `completed`

Barang diterima customer.

### `cancelled`

Order dibatalkan.

---

## 10. Integrasi Ekspedisi

Untuk tahap awal:

- menggunakan satu ekspedisi yaitu JNT
- menggunakan API shipping aggregator atau API JNT langsung

Fitur integrasi:

- cek ongkir
- create shipment
- generate AWB/resi
- tracking pengiriman

---

## 11. Pembayaran

Untuk tahap awal:

- pembayaran dilakukan manual transfer bank
- belum menggunakan payment gateway

Keuntungan:

- tidak ada biaya fee payment gateway
- implementasi lebih sederhana

---

## 12. Teknologi

### Backend

- Laravel API

### Frontend

- Laravel Blade
- Tailwind CSS
- Alpine.js
- Vite

### Frontend Customer

- Mobile-first Web App

### Frontend Admin

- Desktop-first Web App

### Database

- MySQL

### Authentication

- Laravel Sanctum

### Shipping API

- JNT API / Shipping Aggregator API

---

## 13. Arsitektur Frontend

Frontend akan dikembangkan langsung di project Laravel ini menggunakan Blade.

Pendekatan yang digunakan:

- frontend dan backend berada dalam satu project Laravel
- pengembangan UI dimulai lebih dulu menggunakan dummy data
- logika backend dan integrasi API dapat ditambahkan bertahap di project yang sama
- deployment lebih sederhana karena tidak membutuhkan runtime frontend terpisah

Pembagian area frontend:

- customer menggunakan tampilan mobile-first
- admin menggunakan tampilan desktop-first
- keduanya tetap berada dalam satu project Laravel

Area customer:

- dapat melihat katalog tanpa login
- harus login untuk checkout
- harus login untuk melihat status order
- harus login untuk melihat riwayat pesanan

Area admin:

- mengelola katalog produk
- mengelola pesanan customer
- melakukan validasi pembayaran
- memproses pengiriman
- integrasi ke agregator pengiriman

Struktur pengembangan yang disarankan:

- `CustomerLayout` untuk area customer
- `AdminLayout` untuk area admin
- partial Blade untuk komponen UI yang berulang
- controller untuk menyiapkan dummy data dan data bisnis
- Tailwind CSS untuk styling
- Alpine.js untuk interaksi ringan

Keuntungan pendekatan ini:

- aplikasi lebih ringan dan sederhana untuk di-hosting
- customer mendapat tampilan mobile-friendly yang tetap cepat
- admin mendapat dashboard yang rapi untuk desktop
- pengembangan customer dan admin tetap terorganisir
- integrasi fitur bisnis Laravel bisa dilakukan lebih langsung

---

## 14. Rencana Pengembangan Berikutnya

### Future Features

- notifikasi WhatsApp otomatis
- upload bukti transfer langsung di aplikasi
- multi ekspedisi
- payment gateway
- laporan penjualan lengkap
- dashboard analitik
- promo dan voucher
- multi gudang
- tracking realtime
