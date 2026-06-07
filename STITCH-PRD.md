# Larashop — Design PRD for Google Stitch

> **How to use this file with Stitch (stitch.withgoogle.com):**
> 1. Start a new project. Paste **Section 2 (Design System)** first as the global style/context.
> 2. Then generate each screen one at a time by pasting its block from **Section 4 (Screens)**.
> 3. Keep the design system text at the top of every prompt if Stitch loses context between screens.
> 4. UI copy is in **Bahasa Indonesia** (keep it). Layout/structure instructions are in English.

---

## 0. Base Prompt (paste this + ONE screen each time)

> Copy this whole block, then append one screen from Section 4. This keeps style + navigation consistent across every Stitch generation.

```
Design a mobile-first premium e-commerce screen for "Larashop", an Indonesian agricultural-supplies shop. Warm, boutique, trustworthy feel. UI copy in Bahasa Indonesia.

STYLE:
- App background warm cream #F6F1E7; white cards #FFFFFF; thin borders #E7E5E4.
- Brand accent emerald #059669 / #047857; brand gradient #0F9B6D→#32C28B (logo tile & avatars).
- Text: headings #0C0A09, body #1C1917, muted #78716C. Prices/sale in rose #E11D48. Highlight tags amber #D97706.
- Headings/titles use the serif font "Fraunces" (weight 500–600); body uses "Instrument Sans". Eyebrow labels: uppercase, wide tracking, emerald, with a small emerald dot.
- Very rounded corners (cards 1.3–2rem), soft warm layered shadows; cards lift slightly on hover. Inputs: rounded, light stone-50 fill, emerald focus ring.
- Outline icons (Heroicons style, 1.7 stroke). Buttons: dark stone-900 primary, emerald primary, and ghost (white + stone border → emerald on hover).
- Soft emerald radial glow behind the top header.

NAVIGATION (show on every screen):
- Desktop: sticky top bar — left: emerald-gradient "LS" logo tile + "Larashop" (serif) + tagline; center: nav links with icons "Katalog", "Keranjang" (red count badge), "Pesanan" (active = white pill + emerald underline); right: account avatar+username dropdown, or emerald "Masuk" button if logged out.
- Mobile: fixed bottom tab bar with 4 icon tabs — Produk, Keranjang (count badge), Pesanan, Profil; active tab emerald on emerald-50.
- Content centered, max-width ~ md on mobile up to 6xl on desktop, roomy padding.

SCREEN TO DESIGN:
<<paste one screen block from Section 4 here>>
```

---

## 1. Product Overview

**Larashop** is a mobile-first e-commerce app selling **agricultural supplies** (seeds, fertilizer, pesticides, farming tools) to Indonesian farmers ("Sobat Petani"). It has two faces:

- **Storefront** (customer) — mobile-first, warm and friendly but premium. *This PRD focuses here.*
- **Admin panel** (staff) — desktop-first dashboard. *Brief notes in Section 5.*

**Brand personality:** warm, trustworthy, premium-but-approachable, rooted in Indonesian farming culture. Think a boutique organic-market feel, not a cold tech marketplace.

**Tech context (for reference only — Stitch outputs UI, not this stack):** Laravel Blade + Tailwind CSS, talking to a separate REST API. Designs should map cleanly to utility-class HTML.

---

## 2. Design System (paste this first into Stitch)

> Build all screens in this exact visual language.

### Color palette
- **Background (app canvas):** warm cream `#F6F1E7`
- **Surface / cards:** white `#FFFFFF`
- **Primary accent (brand green):** emerald — `#059669` (emerald-600), darker `#047857` (emerald-700)
- **Primary brand gradient:** linear `#0F9B6D → #32C28B` (used on logo tile & avatars)
- **Text — heading:** near-black warm `#0C0A09` (stone-950)
- **Text — body:** `#1C1917` (stone-900)
- **Text — muted/secondary:** `#78716C` (stone-500)
- **Borders / dividers:** `#E7E5E4` (stone-200)
- **Price / sale / discount:** rose `#E11D48` (rose-600)
- **Highlight / sort tags:** amber `#D97706` (amber-600) on amber-100 bg
- **Success:** emerald-50 bg + emerald-800 text. **Error:** rose-50 bg + rose-800 text.
- Subtle **emerald radial glow** behind the top header (very soft, ~20% opacity, fading to transparent).

### Typography
- **Headings / display:** **Fraunces** (serif), weight 500–600, tight letter-spacing (-0.01em). Used for page titles and section titles. Gives a boutique/editorial feel.
- **Body / UI:** **Instrument Sans**, weights 400–700.
- Small **eyebrow labels** above titles: uppercase, letter-spacing 0.22em, emerald-700, with a tiny emerald dot before the text.

### Shape & elevation
- **Very rounded** corners: cards `1.35rem–1.75rem`, large containers up to `2rem`, pills/buttons fully rounded or `1rem`.
- **Soft, warm, layered shadows** — low opacity, large blur, slight downward offset. On hover, cards **lift** slightly (translate up ~2px) with a soft emerald-tinted shadow.
- Inputs: rounded-2xl, light stone-50 fill, emerald border + soft emerald focus ring on focus.

### Iconography
- **Outline icons** (Heroicons style), 1.7 stroke width, rounded line caps. Stone-400 default, emerald when active.

### Motion
- Gentle: 200–300ms transitions, ease. Hover = lift + image zoom (1.04). Active = scale 0.98 on buttons.

### Buttons
- **Primary (dark):** stone-900 fill, white text, rounded-2xl.
- **Primary (emerald):** emerald-600 fill, white text, soft emerald shadow.
- **Ghost:** white fill, stone-300 border, hover → emerald border + emerald text.

### Reusable components
- **Surface card** — white, rounded-3xl, soft shadow, thin stone border.
- **Pill chip** — small rounded-full label (categories, filters, statuses), colored by context (emerald/stone/amber/rose).
- **Section title block** — eyebrow + Fraunces title + muted description line.

---

## 3. Global Navigation

### Desktop (≥1024px) — top navigation bar
- Sticky header on cream/blur background, thin bottom border.
- **Left:** logo = rounded-2xl emerald-gradient tile with "LS", next to it "Larashop" (Fraunces) + tagline "Kebutuhan pertanian, siap kirim".
- **Center:** horizontal nav links with outline icons + label: **Katalog**, **Keranjang** (with red count badge), **Pesanan**. Active link = white pill with soft shadow + emerald text + emerald underline.
- **Right:** account menu — avatar (initial on emerald gradient) + username + chevron. Dropdown shows name/username header, "Akun Saya", "Pesanan Saya", and "Log Out" (rose). If logged out: emerald **"Masuk"** button.

### Mobile (<1024px) — fixed bottom tab bar
- White/blur bar, 4 tabs, each = outline icon in a rounded square + tiny label.
- Tabs: **Produk**, **Keranjang** (red count badge on icon), **Pesanan**, **Profil**.
- Active tab = emerald icon on emerald-50 rounded square + emerald label.
- Respect safe-area inset at the bottom.

Content max-width: `max-w-md` (mobile) scaling up to `max-w-6xl` (desktop), centered, generous horizontal padding.

---

## 4. Screens (generate each one in Stitch)

### 4.1 Catalog / Home  `(route: /)`
**Purpose:** browse & search all products.
**Layout (top → bottom):**
1. Section title block — eyebrow "HAI, SOBAT PETANI SUKSES!", title (Fraunces) "Yuk, belanja kebutuhan pertanianmu!", description "Cek barang dari beragam kategori".
2. **Filter card** (surface card): a search input with a leading magnifier icon ("Cari produk, kategori, atau kebutuhan pertanian..."), three dropdowns (Kategori / Status / Urutkan), and a dark primary **"Cari"** button with magnifier icon. On desktop these sit in one row; stack on mobile.
3. Result count line ("Menampilkan 24 produk") + "Reset filter" link (emerald) when filters active.
4. Active-filter **pill chips** row (e.g. "Cari: pupuk", "Kategori: Benih").
5. **Product grid:** 2 columns mobile, 4 columns desktop, 5 columns XL. Generous gap.

**Product card:**
- Square-ish product image (aspect ~0.9) on a subtle stone→white gradient, slight padding, rounded inner image. Image zooms 1.04 on hover; whole card lifts.
- **Discount badge** = rose pill overlaid top-left of the image (e.g. "-20%").
- Product name (2 lines max, turns emerald on hover).
- **Price** in bold rose-600; below it the original price struck-through in muted stone.
- Footer row (thin top border): small cart/sold icon + sold label ("Terjual 120").

**Empty state:** dashed-border card, muted text "Produk tidak ditemukan...".

---

### 4.2 Product Detail  `(route: /products/{slug})`
**Purpose:** view one product, pick a variant, add to cart / buy.
**Layout:** two-column on desktop (gallery left, info right), stacked on mobile.
- **Gallery:** large main image (rounded-3xl) + a row of thumbnail images below; selecting a thumbnail swaps the main image.
- **Info column:**
  - Category pill chip + product name (Fraunces, large).
  - Price block: large bold rose price, struck original price, discount pill.
  - Stock / status chip (e.g. "Tersedia", "Pre-order", "Stok terbatas").
  - **Variant selector:** selectable pill buttons (e.g. sizes/weights); selected = dark/emerald, others = ghost.
  - Quantity stepper (− / value / +).
  - Two CTAs: emerald **"Beli Sekarang"** and ghost **"+ Keranjang"** with cart icon.
  - Highlights list (bullet points with small check icons).
  - Rich description section (formatted text).
- **Related products:** "Produk lainnya" — a horizontal scroll / small grid reusing the product card.

---

### 4.3 Cart  `(route: /cart)`
**Purpose:** review items, select which to checkout, adjust quantity.
**Layout:** two columns desktop (items left ~1fr, summary right ~0.9fr), stacked mobile.
- Section title — eyebrow "KERANJANG", title "Pilih item yang ingin dibawa ke checkout".
- "Pilih semua" ghost toggle button (right-aligned).
- **Cart item card** (surface card) per item: checkbox (emerald) + product thumbnail + name/variant + unit price + quantity stepper + line subtotal + a trash/remove icon.
- **Summary card** (sticky on desktop): "Ringkasan" — selected product count, selected total (bold), and a primary **"Lanjut ke Checkout"** button.
- Empty state: friendly illustration/icon + "Keranjang masih kosong" + emerald "Mulai belanja" button.

---

### 4.4 Checkout  `(route: /checkout)`
**Purpose:** choose address + shipping, confirm payment summary, place order.
**Layout:** two columns desktop (form left, payment summary right sticky), stacked mobile.
- Section title — "Checkout".
- **Address selector card:** shows the selected shipping address (recipient name, phone, full address) with a "Ubah alamat" action that opens a list/modal of saved addresses (radio-select), plus "Tambah alamat".
- **Shipping option selector card:** list of courier services as selectable rows (radio): courier name + service level + ETA (e.g. "JNT Reguler · estimasi 2–3 hari") + price on the right. Selected row highlighted emerald.
- **Unique code / store credit toggle:** a switch "Gunakan saldo kode unik" with the available balance shown.
- **Payment summary card** (sticky): items total, shipping total, unique-code line, **Grand total** (bold, large). Note about manual bank-transfer payment. Primary **"Buat Pesanan"** button.

---

### 4.5 Login  `(route: /login)`
- Centered single card on cream background, emerald glow behind.
- Logo tile + Fraunces welcome title "Masuk ke Larashop" + muted subtitle.
- Inputs: "Username / Email / No. HP" and "Password" (with show/hide eye toggle).
- Primary emerald **"Masuk"** button (full width).
- Link row: "Belum punya akun? Daftar" (emerald).
- Inline error alert (rose) above the form when login fails.

---

### 4.6 Register  `(route: /register)`
- Same card style as login. Title "Buat akun Larashop".
- Inputs: Nama, Username, No. HP, Email (optional), Password, Konfirmasi Password (eye toggles).
- Primary emerald **"Daftar"** button.
- Link: "Sudah punya akun? Masuk".

---

### 4.7 Account — Profile  `(route: /profile)`
**Layout:** account shell with a **left sidebar** (desktop) / horizontal pill nav (mobile).
- **Sidebar card:** avatar (initial, emerald gradient) + username + name; grouped nav: "Akun Saya" → Profil, Alamat; "Pesanan Saya" → Riwayat Pesanan. Active item = emerald-50 pill.
- **Main content:** "Profil" form card — Nama, Username, Email, No. HP, optional new password + confirm. Primary save button. Success/error alerts at top.

---

### 4.8 Account — Addresses  `(route: /addresses)`
- Same account shell + sidebar.
- Header row: title "Alamat" + emerald **"Tambah Alamat"** button.
- **Address cards grid:** each card = label chip (e.g. "Rumah") + "Utama" badge if primary, recipient name + phone, full address lines, and actions: "Jadikan utama", "Ubah", "Hapus".
- **Add/Edit modal:** form with Label, Nama penerima, No. HP, a **destination search** (type ≥3 chars → dropdown of province/city/district/subdistrict/postal results), full address line, note, "Jadikan alamat utama" checkbox. Save / Cancel.

---

### 4.9 Orders — List  `(route: /orders)`
**Purpose:** order history with status filtering.
- Section title — eyebrow "PESANAN SAYA", title "Riwayat Pesanan".
- **Status tabs** (scrollable pills) with counts: Semua, Belum bayar, Dibayar, Diproses, Dikirim, Selesai, Dibatalkan. Active tab = dark/emerald pill.
- **Order card** per order: order code + status badge (colored by status) + date; a compact list of item thumbnails/names; grand total (bold); a "Lihat detail" action. Pending-payment orders show a prominent "Bayar / lihat instruksi" CTA.
- Empty state per tab.

---

### 4.10 Order — Detail  `(route: /orders/{code})`
**Layout:** two columns desktop (timeline + items left, summary right), stacked mobile.
- Header: order code (Fraunces) + status badge + date.
- **Vertical timeline / stepper:** Order dibuat → Menunggu pembayaran → Validasi admin → Proses pengiriman → Pesanan diterima. Completed steps emerald with check; future steps muted. A separate **cancelled** state shown in rose when applicable. Each step has a label + small note.
- **Items card:** product rows (thumbnail, name, variant, qty, subtotal).
- **Shipping card:** recipient, address, courier, AWB/resi (copyable) when shipped.
- **Payment summary card:** items, shipping, unique code, grand total. Bank-transfer instructions for pending payment.
- **Actions:** "Batalkan pesanan" (when allowed, rose ghost) and "Pesanan diterima" (emerald, when shipped).

---

## 5. Admin Panel (optional, desktop-first)

If you also want admin screens, keep the **same palette and rounding** but desktop-first with a **left vertical sidebar** navigation (Dashboard, Produk, Pesanan, Pengiriman, Customer, Akun) and a top bar. Screens: dashboard with stat cards + recent orders table; data tables with filters for products/orders/customers; detail/edit forms in surface cards; order processing actions (validate payment, process shipment, complete). Tables: clean rows, status pills, row actions. Keep it more utilitarian/dense than the storefront, but same warm brand colors.

---

## 6. Content & Tone Guidelines
- **Language:** Bahasa Indonesia, warm and encouraging ("Sobat Petani"), but not childish.
- **Currency:** Indonesian Rupiah formatted "Rp1.250.000".
- **Dates:** Indonesian format ("2 Juni 2026").
- Keep CTAs short and action-first ("Beli Sekarang", "Buat Pesanan", "Tambah Alamat").

---

## 7. What to ask Stitch to output
- Mobile and desktop frames for each storefront screen above.
- A components sheet: buttons, input, pill chips, product card, order status badges, cart item, navigation (top bar + bottom tabs).
- Keep everything consistent with Section 2 so the result maps directly to Tailwind utility classes.
