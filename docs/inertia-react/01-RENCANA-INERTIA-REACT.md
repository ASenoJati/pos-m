# Rencana Implementasi Inertia + React & Aset Offline — Muning Photo (`kemuning`)

| Item | Nilai |
|---|---|
| Tanggal disusun | 5 November 2026 |
| Kondisi dasar | Laravel 13.39.0, PHP 8.3.36, Node 22.18, npm 11.7, branch `upgrade/laravel-13` |
| Sasaran | Semua halaman Blade → **Inertia + React**; semua aset CDN → **lokal (offline)** |
| Estimasi | ±10–11 hari kerja (+buffer 30% ≈ 13–14 hari kerja, ±3 minggu kalender) |
| Versi pustaka | Diverifikasi dari registry npm / Packagist / GitHub pada 5 November 2026 (tabel §4) |

---

## 1. Tujuan dan ruang lingkup

**Tujuan**

1. Mengganti seluruh lapisan tampilan Blade (28 file, 2.887 baris) dengan halaman React
   yang dirender lewat Inertia, tanpa mengubah perilaku bisnis.
2. Menghilangkan **semua** permintaan jaringan ke domain luar pada saat aplikasi dipakai,
   sehingga aplikasi berfungsi penuh tanpa internet (aset, font, ikon, JS, CSS semuanya
   disajikan dari server sendiri).
3. Menghapus dependensi frontend lama (jQuery, DataTables, select2, Mix) dan dependensi
   PHP yang tidak diperlukan lagi (`yajra/laravel-datatables-oracle`).

**Di luar ruang lingkup**

- Menambah fitur baru (termasuk autentikasi — tetap terbuka sebagai keputusan terpisah).
- Redesign visual (AdminLTE tetap dipakai sebagai dasar, lihat §8 poin 1).
- Inertia SSR (server-side rendering) — tidak diperlukan untuk aplikasi internal ini.

**Definisi selesai (DoD)**

- `grep -R "cdn.jsdelivr\|unpkg\|cdn.datatables\|raw.github"` pada kode **0 hasil aktif**
  (kecuali komentar/link navigasi yang disetujui di §7).
- `npm run build` sukses; aplikasi jalan penuh dengan **DevTools Network tanpa request
  eksternal** (uji mode offline/airplane).
- `php artisan test` hijau (test diperbarui untuk Inertia, §9).
- Semua fungsi 22 langkah UAT di checklist lama tetap bekerja (buat order, transaksi,
  admin, finance, invoice).
- `webpack.mix.js`, `laravel-mix`, jQuery, DataTables, select2, moment, daterangepicker
  terhapus dari `package.json`/`public/`.

---

## 2. Kondisi saat ini (inventaris)

### 2.1 Halaman dan template Blade

| Kelompok | File | Status |
|---|---|---|
| Halaman kasir | `v2/dashboard.blade.php` | aktif |
| | `v2/transaction.blade.php` (halaman terpanjang & paling kompleks: select2, hotkey F2/F3, kalkulasi bayar) | aktif |
| | `v2/transaction-item.blade.php` (partial baris item) | aktif |
| | `v2/recent-customer.blade.php` (partial tabel pelanggan terbaru) | aktif |
| Admin | `v2/admin/home.blade.php`, `customer.blade.php`, `item.blade.php`, `item-edit.blade.php`, `order-detail.blade.php` | aktif |
| Finance | `v2/finance/home.blade.php`, `progress.blade.php`, `recap-photo.blade.php`, `recap-frame.blade.php`, `recap-nonphoto.blade.php` | aktif |
| Invoice | `v2/invoice/customer-invoice.blade.php` | aktif |
| Template | `v2/template/{app,sidebar,navbar,footer}.blade.php` (umum) | aktif |
| | `v2/admin/template/{app,sidebar,navbar,footer}.blade.php` + `app-sidebarn.blade.php` (duplikat mati) | aktif/mati |
| | `v2/finance/template/{app,sidebar,navbar,footer}.blade.php` | aktif |

Total 13 halaman + 2 partial + 13 file template (salah satunya duplikat mati).

### 2.2 Referensi eksternal (CDN) — hasil grep `https?://` di `resources/views/`

| File | URL | Status | Rencana (§7) |
|---|---|---|---|
| 4× `template/app*.blade.php` | `jsdelivr/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css` | **aktif** | npm `bootstrap-icons` |
| `admin/template/app`, `finance/template/app` | `jsdelivr/@fontsource/source-sans-3@5.0.12` | **aktif** | npm `@fontsource/source-sans-3` |
| `template/app`, `app-sidebarn` | fontsource + overlayscrollbars + apexcharts (dalam `{{-- --}}`) | komentar | hapus baris komentar |
| 4× template | `jsdelivr/jsvectormap@1.5.3/css` | **aktif, tapi tidak ada elemen peta/grafik** | hapus |
| `finance/progress` | `jsdelivr/jquery@3.6.0` + `cdn.datatables.net/2.0.8/{css/js}` (bootstrap5) | **aktif** | hapus → tabel React |
| `finance/recap-photo/frame/nonphoto` | jquery CDN + DataTables 2.0.8 CDN (3 file tiap halaman) | **aktif** | hapus → tabel React |
| `admin/customer`, `finance/progress` | `jsdelivr/momentjs` + `jsdelivr/daterangepicker (.js/.css)` | **aktif** | hapus → `dayjs` + input tanggal native |
| `invoice/customer-invoice` | `github.com/anvilco/.../heart.png` (gambar remote) | **aktif** | pakai lokal `public/dist/assets/img/new/heart.png` (sudah ada) |
| `admin/order-detail`, `admin/customer` | `web.whatsapp.com` / `api.whatsapp.com` (tombol chat) | link navigasi | bukan aset; tetap, opsional disembunyikan saat offline |
| `template/footer` (3×) | `adminlte.io` (teks footer) | link navigasi | bukan aset; ganti teks polos |
| `finance/home`, `invoice` | `w3.org/2000/svg` | namespace XML | bukan request jaringan; biarkan |

### 2.3 Aset lokal `public/`

| Lokasi | Isi | Nasib |
|---|---|---|
| `public/vendor/jquery/` (3.6.0) | jQuery | hapus (React) |
| `public/vendor/datatable/` (DataTables 2.0.8 + bootstrap5) | dipakai admin/dashboard | hapus → `react-data-table-component` |
| `public/vendor/sweetalert/` (sweetalert2 11.12.2) | dipakai transaksi | ganti npm `sweetalert2` (bundle) |
| `public/vendor/hotkey/` (hotkeys-js 3.13.7) | F2/F3 transaksi | ganti npm `react-hotkeys-hook` |
| `public/vendor/popperjs/`, `vendor/bootstrap-5.3.3/` | template | ganti npm `bootstrap` (memuat popper) |
| `public/vendor/overlayscrollbars/` | template admin/finance | ganti npm `overlayscrollbars` |
| `public/vendor/ChatOnWhatsAppButton/` | 1 SVG | pindah ke `resources/img` atau tetap di `public/` |
| `public/dist/css|js/adminlte*` (AdminLTE 4) | CSS+JS semua halaman | **CSS** via npm `admin-lte`; **JS-nya tidak dipakai lagi** (perilaku layout diimplementasikan di React + `bootstrap` npm) |
| `public/dist/select2/` (+60 file i18n) | transaksi | hapus → `react-select` |
| `public/dist/assets/img/**` (50+ file: logo, avatar, foto produk) | ikon/gambar | tetap di `public/` (gambar statis; pembersihan opsional) |
| `public/css/app.css`, `public/js/app.js`, `mix-manifest.json` | hasil build Mix (tidak dipakai view mana pun) | hapus |
| `resources/js/app.js` + `bootstrap.js` (axios 0.25, lodash) | tidak di-include view mana pun | tulis ulang total (Fase 1) |

### 2.4 Toolchain & backend

- `package.json` masih berisi `laravel-mix` (vestigial), `preline` (tidak dipakai — satu-satunya
  kemunculan adalah teks "Preline Inc." di blok komentar invoice), `tailwindcss` (tidak dipakai).
- Tidak ada `vite.config.js`; tidak ada `@vite`/`@inertia` di view mana pun.
- `composer.json`: Laravel 13.39.0, Sanctum 4, **`yajra/laravel-datatables-oracle ^13.0`** (dipakai
  3 endpoint tabel), tinker 3, guzzle 7.9.
- Rute web: 29; endpoint data/partial yang akan diubah (lihat §6.3):
  `GET /recent_customer`, `GET /customer/item/{id}`, `GET /getEmployees/{id}` (sudah JSON),
  `GET /admin/customerData/table`, `GET /admin/itemData/table`, `GET /finance/progress/table`,
  `GET|POST /finance/recap/{photo|nonphoto|frame}`.
- Test: `tests/Feature/SmokeTest.php` (9 test, assertion berbasis Blade) — harus diperbarui
  ke assertion Inertia.

---

## 3. Arsitektur target

```
Browser (React SPA-ish)
   │ Inertia (JSON page props + XHR navigasi, CSRF otomatis via XSRF-TOKEN)
   ▼
Laravel 13
   Inertia\Middleware → Controller: Inertia::render('Pages/Dashboard', [...])
   │
   └── JSON endpoints untuk tabel/detail (fetch biasa oleh React)
```

**Prinsip**

1. **Rute Laravel tidak berubah** — React memakai URL yang sama dengan sekarang.
2. **Konversi bertahap, dua sistem paralel** — file Blade lama dipertahankan sampai halaman
   penggantinya lulus UAT; satu commit per halaman/modul; rollback = revert commit.
3. **Perilaku bisnis tidak disentuh** — controller hanya mengganti `view()` →
   `Inertia::render()`; endpoint aksi (POST additem/edit/cancel/dll.) tidak berubah.
4. **Tanpa jQuery** — semua plugin jQuery (DataTables, select2, daterangepicker, hotkeys via
   global) diganti pustaka React.

### 3.1 Struktur folder frontend

```
resources/
  views/app.blade.php                # satu-satunya view Blade tersisa (root @vite + @inertia)
  js/
    app.jsx                          # createInertiaApp + resolve halaman
    Layouts/AppLayout.jsx            # template umum (sidebar+navbar+footer)
    Layouts/AdminLayout.jsx
    Layouts/FinanceLayout.jsx
    Pages/Dashboard.jsx
    Pages/Transaction.jsx
    Pages/Admin/{Home,Customer,Item,ItemEdit,OrderDetail}.jsx
    Pages/Finance/{Home,Progress,RecapPhoto,RecapFrame,RecapNonPhoto}.jsx
    Pages/Invoice.jsx
    Components/
      CheckoutRows.jsx               # baris item transaksi (pengganti transaction-item.blade)
      RecentCustomers.jsx            # pengganti recent-customer.blade
      DataTable.jsx                  # wrapper react-data-table-component (server-side)
      DateRangeFilter.jsx            # pengganti daterangepicker (2 input date native)
      StatusBadge.jsx                # FINISH/ON PROCESS/HOLD dll (pengganti string HTML badge)
      Rupiah.jsx                     # format angka (pengganti Blade @currency)
    lib/format.js                    # formatRupiah, formatTanggal (dayjs)
    lib/api.js                       # helper fetch JSON endpoint (axios)
  css/app.css                        # impor: adminlte.css, bootstrap-icons, fontsource, dsb.
```

### 3.2 Server (Laravel)

- `composer require inertiajs/inertia-laravel:^3.0` → versi rilis **v3.7.0**
  (mendukung Laravel `^11.35|^12|^13`, PHP `^8.2`).
- `php artisan inertia:middleware` → `app/Http/Middleware/HandleInertiaRequests.php`;
  didaftarkan ke grup `web` di `app/Http/Kernel.php` (struktur Kernel lama masih dipakai).
- Root view `resources/views/app.blade.php`:

  ```blade
  <!DOCTYPE html>
  <html lang="id">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title inertia>{{ config('app.name', 'Muning Photo') }}</title>
      @vite(['resources/js/app.jsx', 'resources/css/app.css'])
      @inertiaHead
  </head>
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
      @inertia
  </body>
  </html>
  ```

- `HandleInertiaRequests::share()`: nama aplikasi + `version` (hash manifest untuk
  cache-busting). Flash sweetalert bisa ditambahkan nanti bila diperlukan.
- Blade directive `@currency` (`AppServiceProvider`) → `lib/format.js::formatRupiah()`.

---

## 4. Matriks dependensi (versi diverifikasi 5 November 2026)

### 4.1 Composer

| Paket | Aksi | Versi target | Catatan |
|---|---|---|---|
| `inertiajs/inertia-laravel` | **tambah** | `^3.0` (rilis v3.7.0) | php ^8.2, laravel ^11.35\|^12\|^13 ✓ |
| `yajra/laravel-datatables-oracle` | **hapus** (Fase 3) | — | diganti endpoint JSON paginasi + tabel React; hapus provider di `config/app.php` |

### 4.2 npm

| Paket | Aksi | Versi (terverifikasi) | Menggantikan |
|---|---|---|---|
| `react`, `react-dom` | tambah | 19.3.1 | — |
| `@inertiajs/react` | tambah | 3.11.0 (peer react ^19) | — |
| `vite` | tambah (dev) | 8.3.6 | laravel-mix |
| `@vitejs/plugin-react` | tambah (dev) | 6.2.1 | — |
| `laravel-vite-plugin` | tambah (dev) | 3.2.1 | — |
| `bootstrap` | tambah | 5.3.8 | `vendor/bootstrap-5.3.3`, `vendor/popperjs` |
| `admin-lte` | tambah (hanya CSS) | 4.10.0 | `dist/css/adminlte.css` |
| `bootstrap-icons` | tambah | 1.13.2 | CDN bootstrap-icons |
| `@fontsource/source-sans-3` | tambah | 5.x (cek saat eksekusi) | CDN fontsource |
| `overlayscrollbars` | tambah | 2.17.0 | `vendor/overlayscrollbars` |
| `sweetalert2` | tambah | 11.26.25 | `vendor/sweetalert/sweetalert2@11.js` |
| `react-select` | tambah | 5.10.2 | select2 |
| `react-data-table-component` | tambah | 8.12.0 | DataTables (server & client-side) |
| `react-hotkeys-hook` | tambah | 5.x (cek saat eksekusi) | `vendor/hotkey` (F2/F3) |
| `dayjs` | tambah | 1.11.x | moment (hanya dipakai format/filter tanggal) |
| `axios` | tambah | 1.x | fetch JSON endpoint tabel/detail |
| `jquery`, `laravel-mix`, `lodash`, `preline`, `tailwindcss`, `autoprefixer`, `postcss` | **hapus** | — | tidak dipakai di React |

Catatan: `daterangepicker` (plugin jQuery) **tidak diinstall** — diganti komponen
`DateRangeFilter` dengan dua `<input type="date">` (tanpa dependensi tambahan).

---

## 5. Fase eksekusi

Satu fase = satu commit; branch kerja `feature/inertia-react`.

### Fase 0 — Baseline & persiapan (0,5 hari)

- [ ] Commit bersih, `git checkout -b feature/inertia-react`
- [ ] `.gitignore` + `/public/build`
- [ ] Keputusan §8 ditetapkan (terutama no. 1 dan 2)
- [ ] Inventaris dokumen ini difinalisasi (jadikan acuan grep di Fase 4)

### Fase 1 — Toolchain Inertia + React + Vite (1 hari)

- [ ] `composer require inertiajs/inertia-laravel:^3.0`
- [ ] `php artisan inertia:middleware`; daftarkan `HandleInertiaRequests` di grup `web` (`Kernel.php`)
- [ ] Tulis ulang `package.json` sesuai §4.2; hapus `webpack.mix.js`, `postcss.config.js`, `tailwind.config.js`
- [ ] `vite.config.js`:

  ```js
  import { defineConfig } from 'vite';
  import laravel from 'laravel-vite-plugin';
  import react from '@vitejs/plugin-react';

  export default defineConfig({
      plugins: [
          laravel({ input: ['resources/js/app.jsx', 'resources/css/app.css'], refresh: true }),
          react(),
      ],
  });
  ```

- [ ] `npm install && npm run dev`
- [ ] Root view `app.blade.php` (§3.2)
- [ ] `resources/js/app.jsx` + `Layouts/AppLayout.jsx` minimal
- [ ] **Halaman pilot**: konversi `GET /finance` (`FinanceDashboardController::index` →
      `Inertia::render('Pages/Finance/Home')`) — halaman paling sederhana (4 kartu statistik)
- [ ] `npm run build` → `public/build/manifest.json` terbentuk; halaman pilot tampil
- [ ] Commit `feat(inertia): toolchain vite + react + halaman pilot`

### Fase 2 — Konversi halaman bertahap (5–6 hari)

Urutan berdasarkan kompleksitas naik. Tiap modul: konversi controller `view()` →
`Inertia::render()` + halaman React + UAT + commit terpisah.

| Sub-fase | Modul | Isi | Estimasi |
|---|---|---|---|
| 2a | Layout | `AppLayout`/`AdminLayout`/`FinanceLayout` (sidebar, navbar, footer; collapse sidebar di state React; dropdown Bootstrap via `bootstrap` npm) | 1 hari |
| 2b | Kasir | `Dashboard.jsx` + `RecentCustomers.jsx` (fetch JSON `/recent_customer`; tombol "Order Baru" POST `/` → redirect Inertia ke `/transaction/{track}`) | 1 hari |
| 2c | Transaksi | `Transaction.jsx` + `CheckoutRows.jsx` — paling kompleks: react-select untuk item & harga dinamis, kalkulasi subtotal/bayar/kembalian/kekurangan, tambah/hapus item (POST/DELETE), simpan pembayaran & progress, hold/cancel (sweetalert2 konfirmasi), hotkey F2/F3 (`react-hotkeys-hook`) | 2 hari |
| 2d | Admin | `Home` (kartu + tabel antrean), `Customer` (tabel server-side + filter tanggal), `Item` (tabel), `ItemEdit` (form), `OrderDetail` (detail + tombol WA) | 1,5–2 hari |
| 2e | Finance | `Home`, `Progress` (tabel server-side + filter), `RecapPhoto/Frame/NonPhoto` (filter tanggal → query string GET, tabel client-side dari props) | 1,5 hari |
| 2f | Invoice | `Invoice.jsx` (cetak via `window.print()`; gambar `heart.png` lokal) | 0,5 hari |

Strategi paralel: selama konversi, file Blade lama **tidak dihapus**; rute per modul langsung
dialihkan ke Inertia setelah halaman React lulus UAT. Rollback per modul = revert satu commit.

### Fase 3 — Refactor endpoint data (1 hari)

- [ ] `GET /admin/customerData/table`, `GET /admin/itemData/table`, `GET /finance/progress/table`:
      ganti respons Yajra dengan JSON paginasi standar:
      `{ data: [...], meta: { current_page, last_page, per_page, total } }`
      (filter `from_date`/`to_date`/`search` via query string)
- [ ] `GET /recent_customer` → JSON array; `GET /customer/item/{id}` → JSON array (pengganti partial)
- [ ] `GET /finance/recap/{type}?daterange=...` (GET + query, bukan POST + reload)
- [ ] `GET /getEmployees/{id}` tetap (sudah JSON)
- [ ] Hapus `yajra/laravel-datatables-oracle` dari composer + provider di `config/app.php`
- [ ] Commit `feat(api): endpoint json untuk tabel react, hapus yajra`

### Fase 4 — Offlineisasi & pembersihan (1 hari)

- [ ] Pasang semua pengganti CDN (§7) dan verifikasi **0 URL eksternal aktif**:

  ```powershell
  Get-ChildItem resources,public\build -Recurse -File | Select-String -Pattern 'https?://' |
    Where-Object { $_.Line -notmatch 'w3.org/2000/svg|adminlte.io|whatsapp.com' }
  ```

- [ ] Hapus: seluruh `resources/views/v2/**` (setelah semua halaman hijau),
      `public/vendor/**` usang (jquery, datatable, hotkey, select2, popperjs, bootstrap-5.3.3,
      sweetalert, overlayscrollbars), `public/dist/js/adminlte.js*`, `public/dist/select2`,
      `public/css/app.css`, `public/js/app.js`, `public/mix-manifest.json`,
      `resources/js/bootstrap.js` lama
- [ ] `npm run build`; pastikan aset ter-referensi (font bootstrap-icons, source-sans, CSS
      AdminLTE) ter-emit ke `public/build` dan **tanpa URL eksternal** di dalamnya
- [ ] Commit `feat(offline): semua aset lokal, hapus mix dan vendor usang`

### Fase 5 — Uji & rilis (1–1,5 hari)

- [ ] Perbarui `tests/Feature/SmokeTest.php`:

  ```php
  $response = $this->get('/admin');
  $response->assertOk();
  $response->assertInertia(fn (Assert $page) => $page->component('Pages/Admin/Home'));
  ```

  Endpoint JSON diuji `assertJsonStructure(['data', 'meta'])`; test CSRF tetap dipertahankan.
- [ ] UAT manual 22 langkah (pakai tabel verifikasi lama) + transaksi end-to-end
- [ ] Uji offline sungguhan: matikan internet / DevTools "Offline" → semua halaman berfungsi
- [ ] `npm run build` final, commit, `git tag v2.0-inertia-react`

---

## 6. Pemetaan halaman & data

### 6.1 Blade → React

| Blade | Halaman React | Props utama | Interaksi |
|---|---|---|---|
| `v2/dashboard.blade.php` | `Pages/Dashboard.jsx` | — (tabel recent di-fetch) | POST `/` → `router.visit('/transaction/{track}')`; POST `/editorder`; refresh berkala `/recent_customer` |
| `v2/recent-customer.blade.php` | komponen `RecentCustomers.jsx` | `data[]` (JSON) | — |
| `v2/transaction.blade.php` | `Pages/Transaction.jsx` | `customer_id, customer_data, track_id, items[]` | react-select (item+harga), add/delete item, simpan/edit (subtotal, pay, deficiency, progress, hold), cancel (Swal), F2 bayar / F3 simpan |
| `v2/transaction-item.blade.php` | komponen `CheckoutRows.jsx` | `checkouts[]` (JSON `/customer/item/{id}`) | hapus baris (DELETE) |
| `v2/admin/home.blade.php` | `Pages/Admin/Home.jsx` | `customer[]` (antrean), 4 counter | tabel antrean |
| `v2/admin/customer.blade.php` | `Pages/Admin/Customer.jsx` | `today` | tabel server-side + `DateRangeFilter`; aksi FINISH/ON PROCESS/HOLD/WA |
| `v2/admin/item.blade.php` | `Pages/Admin/Item.jsx` | — | tabel server-side; link edit |
| `v2/admin/item-edit.blade.php` | `Pages/Admin/ItemEdit.jsx` | `itemdata` | POST `/admin/saveUpdateItemData` |
| `v2/admin/order-detail.blade.php` | `Pages/Admin/OrderDetail.jsx` | `track_id, customerData, itemCustomer[]` | tombol WA (link eksternal, opsional offline) |
| `v2/finance/home.blade.php` | `Pages/Finance/Home.jsx` | 4 kartu (customerToday dll.) | — |
| `v2/finance/progress.blade.php` | `Pages/Finance/Progress.jsx` | — | tabel server-side + filter tanggal |
| `v2/finance/recap-{photo,frame,nonphoto}.blade.php` | `Pages/Finance/Recap{...}.jsx` | `{photoOrder|frame|nonPhotoOrder}[]` | filter tanggal (GET query); tabel client-side |
| `v2/invoice/customer-invoice.blade.php` | `Pages/Invoice.jsx` | `customer, itemCustomer[]` | `window.print()` |
| `v2/template/*` + `admin/template/*` + `finance/template/*` | `Layouts/{App,Admin,Finance}Layout.jsx` | — | sidebar/navbar state React; hapus `app-sidebarn` |

### 6.2 Pengganti komponen lama

| Lama (jQuery) | Baru (React) |
|---|---|
| DataTables server-side (Yajra) | `react-data-table-component` mode server-side + JSON paginasi |
| DataTables client-side (recap) | `react-data-table-component` mode client-side (data dari props) |
| select2 (pilih item + harga dinamis) | `react-select` (async options dari `/getEmployees/{id}` atau props) |
| daterangepicker + moment | `DateRangeFilter` (2× `<input type="date">`) + `dayjs` |
| hotkeys.js global (F2/F3) | `react-hotkeys-hook` |
| sweetalert2 global (sudah lokal) | `sweetalert2` npm (API sama) |
| Badge HTML string di kolom (raw HTML) | komponen `StatusBadge` (React element, bukan HTML string) |
| `@currency` Blade | `formatRupiah()` di `lib/format.js` |

### 6.3 Endpoint yang berubah respons

| Route | Sekarang | Target |
|---|---|---|
| `GET /recent_customer` | partial Blade | JSON `{ data: [...] }` |
| `GET /customer/item/{id}` | partial Blade | JSON array checkout |
| `GET /admin/customerData/table` | Yajra (`draw/recordsTotal/...`) | JSON paginasi `{ data, meta }` |
| `GET /admin/itemData/table` | Yajra | JSON paginasi |
| `GET /finance/progress/table` | Yajra | JSON paginasi |
| `GET\|POST /finance/recap/{type}` | Blade + POST filter | `Inertia::render` + GET `?daterange=` |
| `GET /getEmployees/{id}` | JSON (sudah) | tetap |

---

## 7. Rencana offline (peta CDN → lokal)

| # | Referensi eksternal | Solusi |
|---|---|---|
| 1 | `bootstrap-icons` CDN (4 template) | `npm i bootstrap-icons` → `import 'bootstrap-icons/font/bootstrap-icons.css'`; font woff/woff2 ikut ter-bundle Vite |
| 2 | `@fontsource/source-sans-3` CDN (admin+finance) | `npm i @fontsource/source-sans-3` → import di layout; hilangkan dari template umum (sudah komentar) |
| 3 | `jsvectormap` CSS (4 template, tak ada elemen peta) | **hapus** — dead asset |
| 4 | `apexcharts` CSS (semua dalam komentar) | **hapus** baris komentar |
| 5 | `overlayscrollbars` CDN (komentar) | `npm i overlayscrollbars` (dipakai layout admin/finance); hapus `vendor/overlayscrollbars` |
| 6 | jQuery + DataTables CDN (finance ×4) | **hapus** → `react-data-table-component`; jQuery tidak diinstall |
| 7 | moment + daterangepicker CDN (customer, progress) | **hapus** → `dayjs` + input tanggal native |
| 8 | `heart.png` dari github (invoice) | `asset('dist/assets/img/new/heart.png')` (file lokal sudah ada) |
| 9 | `web.whatsapp.com`/`api.whatsapp.com` | link navigasi — bukan aset; tetap berfungsi saat online, otomatis "mati" saat offline (tidak memblokir halaman) |
| 10 | `adminlte.io` (teks footer) | ganti teks statis tanpa link |
| 11 | `http://www.w3.org/2000/svg` | namespace XML pada `<svg xmlns>` — tidak menghasilkan request; biarkan |

**Verifikasi offline (Fase 4 & 5)**

1. Grep sisa URL eksternal pada `resources/`, `public/build/`, dan `public/` (kecuali 3
   pengecualian di atas).
2. `npm run build`; buka aplikasi dengan DevTools → Network → pastikan tidak ada request ke
   domain luar.
3. Mode offline (DevTools "Offline" / matikan WiFi) → ulangi 22 langkah UAT.
4. Catatan deploy: `npm ci && npm run build` butuh internet **di mesin build**; hasil build
   (`public/build`) yang disajikan server membuat **runtime** bebas internet. Untuk deploy
   offline penuh, commit `public/build` (pilihan default proyek ini) atau bangun di CI lalu
   salin artefak.

---

## 8. Keputusan yang perlu disetujui sebelum Fase 1

1. **UI dasar**: pertahankan AdminLTE CSS (rekomendasi — visual tidak berubah, konversi
   tercepat) vs ganti Tailwind + pustaka komponen (redesign penuh, +2 minggu).
2. **TypeScript vs JavaScript**: JavaScript dulu (rekomendasi — konversi cepat, tim kecil);
   TypeScript bisa ditambahkan kemudian.
3. **Tabel**: `react-data-table-component` (rekomendasi — server-side built-in, cocok dengan
   DataTables) vs TanStack Table (lebih fleksibel, lebih banyak kode).
4. **Penghapusan library mati**: setujui penghapusan jsvectormap, apexcharts, jQuery,
   DataTables, select2, moment, daterangepicker, preline, tailwind (tidak ada pemakaian aktif).
5. **Dua sistem paralel** selama migrasi (rekomendasi ya): Blade lama tetap ada sampai
   halaman React lulus UAT, dihapus massal di Fase 4.
6. **Invoice**: `window.print()` (rekomendasi) vs `react-to-print`.
7. **Autentikasi**: tetap di luar ruang lingkup ini (seperti keputusan sebelumnya); kalau
   mau dipasang, idealnya sebelum Fase 2d agar layout admin/finance sekalian memakai guard.

---

## 9. Risiko dan mitigasi

| # | Risiko | Peluang | Dampak | Mitigasi |
|---|---|---|---|---|
| R1 | Halaman transaksi (JS terpanjang: select2, kalkulasi, hotkey) kehilangan perilaku | Sedang | Tinggi | Port 1:1 + UAT khusus transaksi (tambah item, bayar, hold, cancel, F2/F3); commit terpisah |
| R2 | Perilaku layout AdminLTE (collapse sidebar, dropdown) hilang karena JS-nya di-drop | Sedang | Rendah | Implementasi ulang di React (state + `bootstrap` npm); uji desktop & viewport sempit |
| R3 | Inertia v3 berbeda dari contoh/tutorial v2 yang beredar | Sedang | Sedang | Pakai dokumen resmi Inertia (versi terbaru) + rilis v3.7.0; pilot page lebih dulu |
| R4 | Vite 8 butuh Node ≥ 20.19/22.12 | Rendah | Rendah | Node 22.18 terpasang ✓ |
| R5 | Fitur tabel (sorting/filter server-side, badge HTML) berubah perilaku | Sedang | Sedang | `react-data-table-component` server-side; badge HTML → komponen `StatusBadge` |
| R6 | Endpoint Yajra dihapus sebelum semua konsumen pindah | Rendah | Tinggi | Hapus yajra hanya di Fase 3, setelah semua tabel React aktif (grep `DataTables::of` = 0) |
| R7 | Aset statis AdminLTE (50+ gambar) membengkakkan repo | Rendah | Rendah | Biarkan di `public/`; pembersihan opsional terpisah |
| R8 | Salah satu aset "lokal" ternyata masih memanggil CDN di dalamnya (mis. font dalam CSS) | Sedang | Sedang | Verifikasi grep termasuk isi `public/build`; uji offline sungguhan di Fase 5 |

---

## 10. Estimasi

| Fase | Isi | Estimasi |
|---|---|---|
| 0 | Baseline & persiapan | 0,5 hari |
| 1 | Toolchain Inertia + React + Vite + pilot | 1 hari |
| 2 | Konversi halaman (a–f) | 5–6 hari |
| 3 | Refactor endpoint data + hapus yajra | 1 hari |
| 4 | Offlineisasi & pembersihan | 1 hari |
| 5 | Uji, UAT offline, rilis | 1–1,5 hari |
| | **Total** | **±10–11 hari kerja** |
| | + buffer 30% | **13–14 hari kerja (±3 minggu kalender)** |

Urutan kritis: Fase 1 → 2a (layout) → 2c (transaksi, paling berisiko) → 2b/2d/2e/2f → 3 → 4 → 5.

---

## 11. Checklist ringkas per fase

<details>
<summary>Fase 1 — Toolchain</summary>

- [ ] `composer require inertiajs/inertia-laravel:^3.0`
- [ ] `php artisan inertia:middleware` + registrasi di grup `web`
- [ ] `package.json` baru (§4.2), hapus mix/postcss/tailwind config
- [ ] `vite.config.js` (laravel + react), `npm install`
- [ ] root `app.blade.php` (@vite, @inertia)
- [ ] halaman pilot `/finance` tampil dari React
- [ ] `npm run build` sukses
</details>

<details>
<summary>Fase 2 — Halaman</summary>

- [ ] Layout App/Admin/Finance
- [ ] Dashboard + recent
- [ ] Transaction + checkout rows + hotkeys
- [ ] Admin: home, customer, item, item-edit, order-detail
- [ ] Finance: home, progress, recap ×3
- [ ] Invoice + heart lokal
</details>

<details>
<summary>Fase 3 — Endpoint</summary>

- [ ] 3 endpoint tabel → JSON paginasi
- [ ] `/recent_customer` + `/customer/item/{id}` → JSON
- [ ] recap → GET query
- [ ] hapus yajra + provider
</details>

<details>
<summary>Fase 4 — Offline</summary>

- [ ] semua CDN → npm/lokal (§7)
- [ ] hapus Blade lama + vendor/dist usang + mix
- [ ] grep 0 URL eksternal aktif
- [ ] `npm run build` tanpa request eksternal di bundle
</details>

<details>
<summary>Fase 5 — Uji & rilis</summary>

- [ ] SmokeTest → `assertInertia` + JSON
- [ ] UAT 22 langkah + transaksi end-to-end
- [ ] uji offline (DevTools Offline / tanpa WiFi)
- [ ] commit + tag `v2.0-inertia-react`
</details>

---

## 12. Referensi

- [Inertia.js — dokumentasi resmi](https://inertiajs.com/) (server & client adapter)
- [inertia-laravel releases](https://github.com/inertiajs/inertia-laravel/releases) — rilis v3.7.0
- [Laravel — Asset Bundling (Vite)](https://laravel.com/docs/13.x/vite)
- [laravel-vite-plugin](https://github.com/laravel/vite-plugin) — v3.2.1
- [react-data-table-component](https://github.com/jbetancur/react-data-table-component) — v8.12.0
- [react-select](https://react-select.com/) — v5.10.2
- [sweetalert2](https://sweetalert2.github.io/) — v11.26.25
- [react-hotkeys-hook](https://github.com/JohannesKlauss/react-hotkeys-hook)
- [AdminLTE 4 (npm)](https://www.npmjs.com/package/admin-lte) — v4.10.0
- [bootstrap-icons (npm)](https://www.npmjs.com/package/bootstrap-icons) — v1.13.2
- [dayjs](https://day.js.org/)
