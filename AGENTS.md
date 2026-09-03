# AGENTS.md — larashop-fe (Storefront + Admin, BFF)

Frontend toko "Akar Tani Kimia". **Bukan** aplikasi berisi DB domain sendiri — ini **BFF (Backend-for-Frontend)**: semua data produk/order/dll diambil dari **larashop-be** lewat `LarashopApi`, lalu dirender dengan Blade.

- Stack: **Laravel 12, Blade, Tailwind v4, Vite**.
- Dua UI dalam satu app:
  - **Storefront** (pelanggan) — desain **Material-3**: pakai token warna `surface`/`on-surface`/`primary`/`outline`/`error` dan kelas font `font-headline-*`/`font-body-*`/`font-label-*`.
  - **Panel admin** — Tailwind biasa: palet **stone/emerald** (+ **rose** untuk aksi hapus).
- Produksi: `https://akartanikimia.com`. Backend: `https://be.akartanikimia.com`.

## Menjalankan
```bash
php artisan serve --port=8000     # atau via Herd (.test)
npm run dev                       # Vite (hot reload) — WAJIB saat dev
```
`.env` → **`LARASHOP_API_BASE_URL`** harus menunjuk BE (dev: `http://localhost:8001/api/v1`, prod: `https://be.akartanikimia.com/api/v1`).
BE harus jalan juga (FE memanggilnya). Setelah ubah `.env`: `php artisan config:clear`.

## Konvensi & alur
- **`app/Support/LarashopApi.php`** — satu-satunya pintu ke BE. `requestAsAdmin()` (bawa token admin dari session) untuk endpoint admin; `request()`/`publicProducts()` untuk publik. Return **array** (bukan model).
- **Controller**:
  - `StorefrontController` (pelanggan), `AdminController` (panel admin).
  - Mereka **memetakan** respons API → data view (`mapProductSummary`, `mapProductVariant`, `mapCustomerDetail`, dst). Tidak ada Eloquent untuk data domain.
- **Auth**: admin login lewat BE → token disimpan di `session('admin.token')`. Pelanggan pakai token pelanggan.
- **Modal konfirmasi global**: pakai atribut `data-confirm` / `data-confirm-title` / `data-confirm-ok` pada `<form>` (handler di layout admin & customer). **Jangan pakai `confirm()` bawaan browser.**

## Aset & deploy (GOTCHA)
- **`public/build` SENGAJA TIDAK di-gitignore** — hosting tanpa Node, jadi build di lokal lalu **di-commit** supaya ikut `git pull`. Disiplin: **`npm run build` SEBELUM commit** (kalau lupa, asset lama yang ke-deploy). `public/hot`, `public/storage`, `public/uploads` tetap di-ignore.
- Deploy: `npm run build` → `git commit`/`push` → di hosting `git pull` → `php artisan view:clear`.
- Gejala "lokal jalan, hosting tidak" untuk fitur JS/CSS = `public/build` belum ter-deploy.

## Upload gambar produk
- Disimpan ke **`public/uploads`** via disk **`media`** (`config/filesystems.php`, root `public_path('uploads')`) → URL `/uploads/products/...`. **TIDAK butuh `storage:link`** (aman di shared hosting/cPanel yang mematikan symlink). Lihat `buildProductPayload` di `AdminController`.

## Editor produk & varian (rawan bug — hati-hati)
- Deskripsi produk = **Trix** (rich text). Disimpan sebagai HTML aman via `sanitizeRichText()` (allowlist tag), dirender **`{!! !!}`** dengan kelas anak `[&_ul]:list-disc [&_ol]:list-decimal ...` (Tailwind preflight me-reset list-style).
- `resources/js/admin/product-variants.js`:
  - Field harga = **`type="text"` bertopeng ribuan** (`80.000`), di-parse balik ke angka saat simpan. Pakai `price_value` (angka) sebelum `price` (string "Rp…") — kalau kebalik, harga jadi 0.
  - Editor **membawa `id` varian** (`data-id`, `collectVariants`) supaya BE bisa UPSERT (id varian stabil). Jangan hilangkan `id`.
- Payload dibangun di `AdminController::buildProductPayload` → `variants_json` → `normalizeProductVariants` (teruskan `id`).

## Paginasi
- **Admin list produk**: filter/sort dilakukan di FE (koleksi), lalu di-paginate FE (`LengthAwarePaginator`, 12/hal), query string filter ikut ke link halaman.
- **Storefront katalog**: BE yang `paginate` (kirim meta total/current_page/last_page), FE **merekonstruksi** `LengthAwarePaginator` untuk render tombol Sebelumnya/Berikutnya (filter tetap terbawa).
- **WA `/katalog`**: belum ada paginasi (masih limit 30, satu pesan besar).

## Blade gotchas
- `@if` yang menempel pada kata tidak ter-compile (mis. `pickup@if`).
- `<form>` bersarang tidak valid — browser membuang tag form dalam.
- Untuk komponen (`<x-...>`), atribut tambahan (mis. `data-...`) diteruskan lewat `{{ $attributes }}` di elemen root komponen.

## Produksi
`APP_ENV=production`, `APP_DEBUG=false`, `LARASHOP_API_BASE_URL` = BE produksi. `.env` gitignored (jangan commit).
