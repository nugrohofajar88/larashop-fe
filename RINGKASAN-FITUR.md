# Larashop — Ringkasan Fitur Frontend

**Larashop** adalah aplikasi e-commerce produk pertanian/agrokimia (pestisida, ZPT, pupuk, hormon) untuk **Akar Tani Kimia** — tampilan **premium, mobile-first, berbahasa Indonesia**, terasa eksklusif seperti butik modern.

---

## Tipe Pengguna

| Pengguna | Akses | Kebutuhan login |
|---|---|---|
| **Pengunjung (Guest)** | Lihat katalog & detail produk | Tidak perlu |
| **Customer** | Belanja penuh: keranjang, checkout, pesanan, alamat, profil | Perlu akun |
| **Admin / Staff** | Kelola produk, pesanan, pembayaran, pengiriman, customer, akun | Panel terpisah |

---

## Fitur Customer (Storefront)

**Belanja & Produk**
- Katalog dengan **pencarian, filter** (kategori, status, urutan harga/nama) dan **paginasi**
- Detail produk: galeri foto + zoom, **pilihan varian** (ukuran/berat/harga/stok **per varian**), keunggulan, dan rekomendasi produk lain
- Badge diskon, harga coret, label "terjual" (dihitung dari transaksi nyata)

**Keranjang & Checkout**
- Keranjang: atur jumlah, pilih item yang di-checkout, ringkasan harga real-time
- Checkout: pilih alamat kirim, pilih **layanan kurir + ongkir otomatis** (multi-kurir), ringkasan pembayaran
- **Saldo kode unik** (store credit) sebagai potongan
- Pembayaran: **transfer manual** atau **QRIS dinamis** (auto-validasi saat dibayar)

**Akun & Pesanan**
- Registrasi & login (username/email/no. HP)
- Manajemen **profil** & ganti password
- **Buku alamat**: banyak alamat, alamat utama, pencarian wilayah otomatis (destination_id)
- **Riwayat pesanan** + filter status (Belum bayar, Dibayar, Diproses, Dikirim, Selesai, Dibatalkan)
- **Detail pesanan**: timeline status, rincian item, info pengiriman + **resi (AWB) & lacak**, ringkasan pembayaran
- Aksi mandiri: **batalkan pesanan** (aturan sesuai status), tandai pesanan diterima
- Order belum dibayar **kedaluwarsa otomatis** setelah 24 jam (stok dikembalikan)

---

## Fitur WhatsApp (Bot)

Pelanggan bisa berinteraksi lewat WhatsApp:
- `/katalog`, `/cari <produk>`, `/cek-{id}` — lihat produk
- `/cek-ongkir <wilayah>` — estimasi ongkir
- `/pesan` — buat pesanan lewat form WA
- `/lacak <no-order>` — lacak resi
- `/tanya-admin` — teruskan ke admin
- Konfirmasi pembayaran + kirim bukti transfer → admin otomatis dinotifikasi

---

## Fitur Admin (Ringkas)

- **Dashboard** omzet/penjualan/produk terlaris (data nyata)
- **Produk**: tambah/edit, **varian sebagai sumber harga/stok**, upload foto, deskripsi rich-text, **paginasi**; hapus produk **cerdas** (produk yang pernah diorder → **diarsipkan**, sisanya dihapus permanen)
- **Pesanan**: validasi pembayaran, proses pengiriman (booking kurir + **AWB**), cetak label (bulk), setujui/tolak pembatalan, penyelesaian; **pembatalan ikut membatalkan booking di kurir**
- **QRIS**: upload/aktifkan/hapus QRIS toko
- **Customer**, **pengaturan gudang/asal kirim**, **akun staff** (super-admin kelola akun; tiap admin edit profil sendiri)

---

## Keunggulan Desain & Pengalaman

- **Mobile-first**: navigasi bawah berikon + badge keranjang; desktop dengan top-nav rapi
- Identitas visual **premium** (Material-3): tipografi elegan, palet hangat (cream + hijau emerald), sudut membulat, bayangan lembut
- Tombol **WhatsApp melayang** di storefront; modal konfirmasi konsisten (bukan `confirm()` bawaan)

---

## Teknologi

- **Backend**: Laravel 12 (REST API, Sanctum) — sumber data & logika bisnis (repo `larashop-be`)
- **Frontend**: Laravel Blade + Tailwind CSS v4 + Vite (server-rendered, BFF yang memanggil API BE)
- **Pengiriman**: **Komerce Collaborator** (multi-kurir: JNE, JNT, SiCepat, Ninja, IDE, SAP, Lion, Tiki, AnterAja) + **RajaOngkir** (tracking resi)
- **Pembayaran QRIS**: QRISLY (QRIS dinamis)
- **WhatsApp**: gateway Wablas / Fonnte (dapat diganti)
- **Database**: MySQL
