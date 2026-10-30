# Analisis Proyek — Muning Photo (`kemuning`)

Dokumen ini adalah hasil audit kode dan konfigurasi sebelum upgrade Laravel.
Semua klaim di bawah disertai bukti berupa path dan nomor baris agar bisa diverifikasi ulang.

| Item | Nilai |
|---|---|
| Tanggal audit | 30 Oktober 2026 |
| Branch | `master` |
| Commit terakhir | `d0e9625` — "First commit" (satu-satunya commit) |
| Framework | `laravel/framework` **v9.52.16** |
| Runtime PHP | 8.3.36 (ZTS, Laragon) |
| Constraint `php` di composer.json | `^8.0` |
| Composer | 2.10.3 |
| Node / npm | 22.18.0 / 11.7.0 |
| Database | MySQL, `DB_DATABASE=muningphoto` (server tidak berjalan saat audit) |
| Jumlah paket Composer | 129 (termasuk dev) |
| Kode aplikasi | 1.528 baris di `app/`, 1.447 baris di `config/` (±3.000 baris total) |
| Controller / model | 11 controller aktif, 4 model, ditambah 2 controller mati di `oldController/` |
| Test | 2 test contoh (`tests/Feature`, `tests/Unit`), tanpa assertion bisnis |

---

## 1. Ringkasan eksekutif

Aplikasi ini adalah **POS / manajemen order studio foto** dengan tiga area: kasir
(`/transaction`), admin produksi (`/admin`), dan finance (`/finance`), plus cetak invoice.
Tumpukannya sederhana dan kecil: 11 controller, 4 model, 29 rute web, 1 rute API,
tampilan Blade berbasis AdminLTE 4 + jQuery + DataTables.

Karena ukurannya kecil, biaya teknis upgrade **rendah**. Yang membuat upgrade ini
berisiko bukan ukuran kode, melainkan empat hal:

1. **Tidak ada baseline Git.** 363 perubahan belum di-commit (337 file terhapus, 24 untracked).
   Tanpa commit bersih, tidak ada titik balik yang aman.
2. **Migrasi tidak sinkron dengan database.** Model memakai `SoftDeletes` dan query
   memfilter `deleted_at`, tetapi tidak ada satu pun migrasi yang membuat kolom itu.
3. **Tidak ada autentikasi sama sekali** pada rute admin, finance, dan transaksi.
4. **Tidak ada test yang mengunci perilaku**, sehingga regresi hanya terdeteksi manual.

Kesimpulan: upgrade sangat layak, tetapi **perbaikan Fase 0 (baseline, drift DB, test asap)
wajib dikerjakan lebih dulu** — bukan sebagai "nice to have".

---

## 2. Profil teknis

### 2.1 Dependensi produksi

| Paket | Versi terpasang | Dipakai di kode? | Catatan |
|---|---|---|---|
| `laravel/framework` | v9.52.16 | ya | Laravel 9 sudah EOL (security fix berakhir Feb 2024) |
| `yajra/laravel-datatables` | v9.0.0 | meta-package | Menarik `-oracle`, `-html`, `-buttons`, `-editor`, `-fractal`; kode hanya memakai `-oracle` |
| `yajra/laravel-datatables-oracle` | v10.11.4 | ya | 5 controller memakai `DataTables::of()` |
| `barryvdh/laravel-dompdf` | v2.2.0 | **tidak** | Kode pembuatan PDF di `InvoiceController` masih dikomentari |
| `realrashid/sweet-alert` | v7.2.0 | **tidak** | Tidak ada pemanggilan `Alert::` di kode |
| `laravel/sanctum` | v2.15.1 | hanya `HasApiTokens` | Satu rute API `auth:sanctum` tanpa konsumen |
| `laravel/tinker` | v2.9.0 | alat | — |
| `fruitcake/laravel-cors` | v2.2.0 | ya, di `app/Http/Kernel.php:19` | Sudah digantikan middleware bawaan framework sejak Laravel 9.2 |
| `apalfrey/select2-bootstrap-5-theme` | 1.3.0 | aset CSS | Paket aset JS/CSS, tidak mengikat versi Laravel |
| `guzzlehttp/guzzle` | 7.8.1 | tidak langsung | Bawaan framework |

### 2.2 Dependensi dev

| Paket | Versi | Catatan |
|---|---|---|
| `phpunit/phpunit` | 9.6.19 | Laravel 13 memakai PHPUnit 11/12 |
| `nunomaduro/collision` | v6.4.0 | Laravel 13 memakai collision 8.x |
| `spatie/laravel-ignition` | 1.7.0 | Tidak lagi ada di skeleton Laravel 11+ |
| `mockery/mockery` | 1.6.12 | Aman |
| `fakerphp/faker` | 1.23.1 | Aman |
| `laravel/sail` | v1.29.3 | Tidak dipakai (proyek Laragon/Windows) |

### 2.3 Frontend

| Aspek | Temuan |
|---|---|
| Template | `resources/views/v2/**` (AdminLTE 4 / Bootstrap 5.3), `v1/**` tidak terpakai |
| Aset | Disajikan dari `public/dist`, `public/vendor` (hasil salin manual, bukan hasil build) |
| Build tool | `webpack.mix.js` + `laravel-mix 6` — **vestigial**: tidak ada `mix()` di seluruh Blade |
| `resources/js/app.js` | Hanya `require('./bootstrap')` (lodash + axios 0.25) — tidak di-include view mana pun |
| DataTables | jQuery DataTables 2.0.8 via CDN di `transaction.blade.php`, sisanya aset lokal |
| Kesimpulan | Migrasi Mix → Vite **opsional**, tidak memblokir upgrade framework |

---

## 3. Peta rute

`routes/web.php` mendefinisikan 29 rute; **tidak satu pun memakai middleware `auth`**.

| Area | Contoh rute | Controller | Auth |
|---|---|---|---|
| Kasir | `GET /`, `POST /`, `POST /editorder` | `DashboardController` | tidak |
| Transaksi | `GET /transaction/{id}`, `POST /transaction/edit/{id}`, `POST /transaction/cancel/{id}` | `TransactionController` | tidak |
| Admin | `GET /admin`, `/admin/customerData`, `/admin/itemData`, `POST /admin/saveUpdateItemData` | `Admin\*Controller` | tidak |
| Finance | `GET/POST /finance/recap/photo`, `/nonPhoto`, `/frame`, `/finance/progress` | `Finance\*Controller` | tidak |
| Invoice | `GET /invoice/customer/{id}` | `InvoiceController` | tidak |
| Tabel | `/recent_customer`, `/finance/progress/table`, `/admin/itemData/table` | berbagai | tidak |
| API | `GET /api/user` | closure | `auth:sanctum` |

Query `routes/api.php` memakai `auth:sanctum`, tetapi tidak ada klien yang memakainya — API
praktis kosong.

---

## 4. Temuan

Tingkat keparahan: **Kritis** = menghalangi upgrade / risiko data, **Tinggi** = bug produksi
nyata, **Sedang** = utang teknis yang memperbesar biaya upgrade, **Rendah** = kebersihan.

### T-01 — Tidak ada baseline Git · **Kritis**

`git log` hanya berisi satu commit, sedangkan `git status` menunjukkan **363 perubahan**:
337 file terhapus (aset lama di root: `css/`, `dist/`, `vendor/`), 24 file untracked
(seluruh `app/`, `config/`, `routes/`, `public/`), dan 2 file termodifikasi.

Artinya: kode yang berjalan **tidak pernah di-commit**. Setiap `composer update` atau
refactor tidak punya titik balik. Selain itu `vendor/` (75 file) ter-commit padahal
`.gitignore` memuat `/vendor`.

**Dampak:** rollback tidak mungkin; diff upgrade tidak bisa direview.
**Tindakan:** commit baseline bersih sebelum menyentuh `composer.json` (lihat Fase 0).

### T-02 — Migrasi tidak sinkron dengan database (drift) · **Kritis**

- `Customer`, `Item`, `Checkout`, `User` memakai trait `SoftDeletes`
  (`app/Models/Customer.php:11`, `Item.php:11`, `Checkout.php:11`, `User.php:14`).
- Query eksplisit memfilter kolom itu:
  `->whereNull('checkouts.deleted_at')` di
  `app/Http/Controllers/Finance/RecapPhotoController.php:33,56`,
  `RecapFrameController.php:33,56`, `RecapNonPhotoController.php:33,56`.
- **Tidak ada satu pun migrasi** yang memanggil `softDeletes()` — sudah diverifikasi dengan
  grep pada seluruh isi `database/migrations/`.

Kesimpulan: kolom `deleted_at` ada di database produksi tetapi **tidak ada di kode migrasi**.
`php artisan migrate:fresh` akan menghasilkan skema yang **tidak bisa menjalankan aplikasi**
(semua halaman admin/finance error `Unknown column deleted_at`).

**Dampak:** lingkungan staging baru tidak bisa dibuat dari migrasi; upgrade tidak bisa diuji
secara realistis. Ini harus ditutup sebelum fase upgrade mana pun.

### T-03 — Rute admin/finance tanpa autentikasi · **Kritis**

Tidak ada middleware `auth` (atau otorisasi lain) di `routes/web.php`. Siapa pun yang bisa
menjangkau server dapat membuka `/admin`, `/finance/recap/photo` (berisi data pelanggan dan
nominal), mengubah harga item (`POST /admin/saveUpdateItemData`), atau menghapus order
(`POST /transaction/cancel/{id}`).

Sisa-sisa scaffolding auth ada tetapi tidak berfungsi:

- `app/Http/Middleware/Authenticate.php:18` mengembalikan `route('login')`.
- **Rute `login` tidak terdaftar** di `routes/web.php` maupun `routes/api.php`.
- `app/Providers/RouteServiceProvider.php:14` menetapkan `HOME = '/home'`, tetapi
  rute `/home` juga tidak ada.
- `resources/views/v1/login.blade.php` ada, tetapi tidak pernah dirender.

**Dampak:** bukan bug upgrade, tetapi begitu middleware `auth` dipasang, kedua referensi di
atas akan langsung melempar `RouteNotFoundException`. Perbaiki bersamaan dengan Fase 0/6.

### T-04 — Backup terjadwal tidak pernah berjalan · **Tinggi**

| Bukti | Isi |
|---|---|
| `app/Console/Kernel.php:19` | `$schedule->command('db:backup')->daily();` |
| `app/Console/Commands/DatabaseBackup.php:16` | `protected $signature = 'app:database-backup';` |

Nama command yang dijadwalkan **tidak cocok** dengan signature, sehingga scheduler memanggil
command yang tidak ada. Selain itu `DatabaseBackup.php` memakai `env()` saat runtime
(baris 36–37) yang bernilai `null` ketika `php artisan config:cache` aktif, dan merangkai
`mysqldump | gzip` lewat `exec()` — `gzip` tidak tersedia di Windows/Laragon, dan kode tidak
memeriksa `$returnVar`, jadi kegagalan tidak pernah terdeteksi.

**Dampak:** tidak ada backup otomatis. Justru saat upgrade-lah backup paling dibutuhkan.

### T-05 — Kode mati yang membingungkan · **Sedang**

| Lokasi | Masalah |
|---|---|
| `app/Http/Controllers/oldController/AdminController.php:3` | Namespace `App\Http\Controllers` padahal file ada di subfolder `oldController` |
| `app/Http/Controllers/oldController/FinanceController.php:3` | Sama; berisi 268 baris duplikat dengan raw SQL |
| `resources/views/v1/**` (5 file) | Tidak dirender rute mana pun |
| `resources/views/v2/*.blade copy.php`, `customer-invoice.blade copy.php` | Duplikat manual |
| `resources/views/v2.zip` (41 KB) | Arsip biner di dalam repo |
| `resources/views/v2/invoice/customer-invoice.blade.php` | Versi aktif; versi `copy` membingungkan |
| `config/sweetalert.php` (221 baris) | Konfigurasi paket yang tidak dipakai |

**Dampak:** `oldController` akan ikut terkena setiap perubahan API framework (dan namespace-nya
salah, sehingga IDE/autoload tidak konsisten). Hapus sebelum upgrade agar permukaan uji mengecil.

### T-06 — Tidak ada validasi input · **Sedang**

Grep `validate(` / `Validator::` / FormRequest di `app/` menghasilkan **0 kecocokan**.
Semua input masuk apa adanya:

- `TransactionController::addNewCustomerItem` → `Checkout::create($request->all())`
- `TransactionController::updateDataCustomer` → `Customer::where(...)->update($request->all())`
- `Admin\ItemController::saveUpdateItemData` → update harga/tipe item tanpa validasi

`Builder::update()` **tidak** menerapkan `$fillable`, jadi seluruh field yang diposting
(nominal, `progress`, `hold_order`) ikut tersimpan — termasuk nilai non-numerik atau kosong.

**Dampak:** bukan penghalang upgrade, tetapi membuat setiap regresi pasca-upgrade sulit
dibedakan dari data yang sudah rusak. Minimal tambahkan validasi pada endpoint tulis di Fase 6.

### T-07 — Nominal uang disimpan sebagai string · **Sedang**

Migrasi membuat `items.price`, `customers.subtotal`, `customers.pay`,
`customers.return_pay`, `customers.deficiency`, `checkouts.qty`, `checkouts.discount`
sebagai `string` (`database/migrations/2024_06_27_*.php`).

Aplikasi lalu memakai `->sum('subtotal')` dan `->sum('deficiency')`
(`app/Http/Controllers/Finance/FinanceDashboardController.php:17-18`). MySQL mengoersi
string ke angka, sehingga hasil penjumlahan bergantung pada isi string (`"1.500.000"` akan
menjadi `1.5`). Ini bug laten yang bisa muncul kapan saja, terlepas dari upgrade.

### T-08 — Tidak ada penanganan entitas tidak ditemukan · **Sedang**

| Lokasi | Risiko |
|---|---|
| `TransactionController::new_transaction` (`:15-17`) | `Customer::where('track',$id)->first()` bisa `null`, lalu `$getCustomer->id` fatal |
| `InvoiceController::customerInvoice` (`:13`) | `$customer->created_at` fatal bila `track` tidak ada |
| `Admin\ItemController::updateItemData` (`:56`) | `Item::find($id)` tanpa cek `null` |

**Dampak:** URL yang salah menghasilkan error 500, bukan 404. Saat pengujian pasca-upgrade,
error 500 semacam ini mudah disalahartikan sebagai regresi framework.

### T-09 — Konfigurasi rute peninggalan Laravel 8/9 · **Sedang**

`app/Providers/RouteServiceProvider.php:20` mengomentari `protected $namespace`, tetapi baris
43 dan 47 masih memanggil `->namespace($this->namespace)`. Nilainya `null` sehingga tidak
berbuat apa-apa — pola ini dihapus dari skeleton sejak Laravel 8 dan membingungkan saat
migrasi ke `bootstrap/app.php`.

Hal serupa: `app/Http/Kernel.php:19` masih memuat `Fruitcake\Cors\HandleCors` padahal framework
sudah punya `Illuminate\Http\Middleware\HandleCors` sejak 9.2, dan
`app/Http/Kernel.php:38` memakai `VerifyCsrfToken` yang di Laravel 13 diganti nama menjadi
`PreventRequestForgery`.

### T-10 — Aset dan entry point ganda · **Rendah**

- `index.php` di root meneruskan ke `public/index.php` (pola shared hosting).
- `public/mix-manifest.json` ada, tetapi tidak ada view yang memakai `mix()`.
- `package-lock.json` + `node_modules/` (tidak ter-commit) untuk toolchain yang tidak dipakai.

**Dampak:** tidak menghalangi upgrade; jangan "diperbaiki" bersamaan agar diff tetap terbaca.

### T-11 — Konfigurasi test usang · **Rendah**

`phpunit.xml` masih memakai `<coverage processUncoveredFiles="true">` (dihapus di PHPUnit 10+)
dan `CACHE_DRIVER` (Laravel 11+ memakai `CACHE_STORE` di skeleton baru). Test yang ada hanya
`ExampleTest` sehingga suite ini tidak akan menangkap regresi apa pun.

### T-12 — `composer.json` tidak rapi · **Rendah**

`"name": "laravel/laravel"`, `"php": "^8.0"` (padahal framework 9 butuh 8.0.2+),
`"minimum-stability": "dev"` dengan `"prefer-stable": true`, dan `fruitcake/laravel-cors`
yang sudah usang. Saat upgrade, blok `require` akan ditulis ulang seluruhnya, jadi ini
akan hilang dengan sendirinya.

---

## 5. Kesiapan upgrade

| Aspek | Status | Catatan |
|---|---|---|
| PHP runtime | ✅ | 8.3.36 memenuhi Laravel 13 (8.3–8.5) |
| Ekstensi PHP | ✅ | `pdo_mysql`, `gd`, `mbstring`, `openssl`, `zip`, `intl`, `dom` tersedia (dompdf butuh `gd`+`dom`) |
| Composer | ✅ | 2.10.3 |
| Ukuran kode | ✅ | Kecil, mudah diaudit manual |
| Paket pihak ketiga | ⚠️ | Semua perlu naik major; `fruitcake/laravel-cors` harus dihapus |
| Test | ❌ | Tidak ada test bermakna |
| Baseline Git | ❌ | 363 perubahan belum di-commit |
| Sinkronisasi skema | ❌ | `deleted_at` tidak ada di migrasi |
| Autentikasi | ❌ | Rute sensitif terbuka |
| Backup | ❌ | Terjadwal tapi tidak pernah jalan |

---

## 6. Perintah yang dipakai untuk audit

Dijalankan di root proyek (Windows/PowerShell), agar hasil bisa direproduksi:

```powershell
php -v; composer --version; node -v
git log --oneline -8; git status --porcelain | Measure-Object -Line
Select-String -Path app\Http\Kernel.php -Pattern "Fruitcake|VerifyCsrfToken"
Get-ChildItem database\migrations\*.php | Select-String -Pattern 'softDeletes'   # 0 hasil
Get-ChildItem app -Recurse -Filter *.php | Select-String -Pattern 'validate\('     # 0 hasil
Select-String -Path routes\web.php -Pattern '^Route::' | Measure-Object           # 29
```

Untuk membaca versi paket yang benar-benar terpasang (bukan constraint):

```powershell
php -r "$l=json_decode(file_get_contents('composer.lock'),true);foreach(array_merge($l['packages'],$l['packages-dev']) as $p) echo $p['name'],' ',$p['version'],PHP_EOL;"
```

> Catatan: `composer.lock` tidak bisa di-parse `ConvertFrom-Json` PowerShell karena
> paket `barryvdh/laravel-dompdf` punya dua alias yang berbeda hanya pada kapitalisasi
> (`Pdf` dan `PDF`). Gunakan PHP seperti di atas.
