# Checklist Eksekusi — Upgrade Laravel 9 → 13

Lembar kerja operasional. Centang setiap butir; jangan lanjut ke fase berikutnya sebelum
kriteria selesai fase itu terpenuhi. Penjelasan dan alasan ada di
[01-RENCANA-UPGRADE.md](01-RENCANA-UPGRADE.md).

| | |
|---|---|
| Branch kerja | `upgrade/laravel-13` |
| Tag baseline | `baseline-l9` |
| Mulai | ____ / ____ / ________ |
| Eksekutor | ______________________ |
| Database staging | `muningphoto_staging` |
| Dump produksi | `D:\backup\muningphoto-sebelum-upgrade-2026-10-30.sql` |

> Aturan main: **satu fase = satu commit**. Jika satu fase gagal, `git reset --hard` ke
> commit fase sebelumnya, bukan memperbaiki sambil menumpuk perubahan.

---

## Fase 0 — Baseline

- [ ] `git status` diperiksa; 363 perubahan dipahami sebelum di-commit
- [ ] `git add -A && git commit -m "chore: baseline kondisi produksi sebelum upgrade"`
- [ ] `git tag baseline-l9`
- [ ] `git checkout -b upgrade/laravel-13`
- [ ] `vendor/` berhenti dilacak: `git rm -r --cached vendor` (commit terpisah)
- [ ] Dump database dibuat dan **diuji restore** ke `muningphoto_staging`
- [ ] `.env` produksi disalin ke tempat aman (berisi `APP_KEY`)
- [ ] Aplikasi berjalan normal dari database staging
- [ ] `storage/logs/laravel.log` dikosongkan agar regresi baru terlihat

**Selesai bila:** `git status` bersih, tag `baseline-l9` ada, staging bisa dibuka.

---

## Fase 1 — Tutup drift skema dan bug penghalang

- [ ] Kolom `deleted_at` di produksi diverifikasi:
      `SHOW COLUMNS FROM checkouts LIKE 'deleted_at';` (ulangi untuk `items`, `customers`)
- [ ] Migrasi rekonsiliasi dibuat (`add_soft_deletes_to_master_tables`)
- [ ] `php artisan migrate` di staging: **tidak** menambah kolom duplikat
- [ ] `php artisan migrate:fresh --seed` di database kosong: **sukses**
- [ ] `SHOW CREATE TABLE checkouts` staging dibandingkan dengan produksi: identik
- [ ] `app/Console/Kernel.php` → `$schedule->command('app:database-backup')->dailyAt('23:30')`
- [ ] `DatabaseBackup` ditulis ulang memakai `Process` + `config('database.connections.mysql')`
- [ ] `php artisan app:database-backup` menghasilkan file `.sql` yang bisa di-restore
- [ ] `oldController/` dihapus
- [ ] `resources/views/v1/` dihapus
- [ ] `resources/views/v2.zip` dihapus
- [ ] Dua file `*.blade copy.php` dihapus
- [ ] `realrashid/sweet-alert` dihapus dari `composer.json`
- [ ] `barryvdh/laravel-dompdf` dihapus dari `composer.json` (kecuali ekspor PDF direncanakan)
- [ ] `phpunit.xml` memakai SQLite in-memory
- [ ] `tests/Feature/SmokeTest.php` ditulis (lihat rencana §15.3)
- [ ] `php artisan test` dijalankan; kegagalan yang tersisa dicatat di lembar masalah
- [ ] Commit: `fix: sinkronkan skema soft delete, perbaiki backup, hapus kode mati`

**Selesai bila:** `migrate:fresh` sukses, backup menghasilkan file valid, test asap berjalan
(mayoritas hijau; kegagalan yang tersisa harus terdokumentasi sebagai bug yang akan ditutup
di Fase 6).

---

## Fase 2 — Laravel 9 → 10

**`composer.json`**

```json
"require": {
    "php": "^8.1",
    "guzzlehttp/guzzle": "^7.9",
    "laravel/framework": "^10.0",
    "laravel/sanctum": "^3.2",
    "laravel/tinker": "^2.9",
    "yajra/laravel-datatables-oracle": "^10.0"
},
"require-dev": {
    "fakerphp/faker": "^1.23",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^7.0",
    "phpunit/phpunit": "^10.0",
    "spatie/laravel-ignition": "^2.0"
}
```

- [ ] `composer update --with-all-dependencies`
- [ ] `composer dump-autoload`
- [ ] Properti `$dates` dihapus dari 4 model — `grep` harus 0 hasil:
      `Get-ChildItem app\Models\*.php | Select-String '\$dates'`
- [ ] `phpunit.xml`: hapus `processUncoveredFiles`, pindahkan `<include>` ke `<source>`
- [ ] `php artisan about` — tidak ada error
- [ ] `php artisan test`
- [ ] Uji manual §Verifikasi (semua baris bertanda F2)
- [ ] `POST /` tanpa token → **419**
- [ ] Commit: `chore(upgrade): Laravel 10`

---

## Fase 3 — Laravel 10 → 11

**`composer.json`**

```json
"require": {
    "php": "^8.2",
    "guzzlehttp/guzzle": "^7.9",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.0",
    "laravel/tinker": "^2.10",
    "yajra/laravel-datatables-oracle": "^11.0"
},
"require-dev": {
    "fakerphp/faker": "^1.23",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.1",
    "phpunit/phpunit": "^11.5"
}
```

- [ ] `fruitcake/laravel-cors` dihapus dari `require`
- [ ] `spatie/laravel-ignition` dan `laravel/sail` dihapus dari `require-dev`
- [ ] `composer update --with-all-dependencies`
- [ ] `app/Http/Kernel.php:19`: `Fruitcake\Cors\HandleCors` → `\Illuminate\Http\Middleware\HandleCors`
- [ ] Six file middleware redundan dihapus, `Kernel.php` menunjuk kelas framework
      (tabel pengganti ada di rencana §8 langkah 5)
- [ ] `php artisan vendor:publish --tag=sanctum-migrations`; **cek** tidak ada migrasi ganda
- [ ] `config/sanctum.php`: tiga key `middleware` menunjuk kelas yang benar (rencana §6.2)
- [ ] Struktur lama **dipertahankan**: `bootstrap/app.php`, `app/Http/Kernel.php`,
      `app/Exceptions/Handler.php`, `providers` di `config/app.php`
- [ ] `php artisan config:clear && php artisan route:clear && php artisan view:clear`
- [ ] `php artisan route:list` → 29 rute web + 1 rute API, tanpa error
- [ ] `php artisan about`
- [ ] `php artisan test`
- [ ] `POST /` tanpa token → **419** (bukti CSRF masih aktif)
- [ ] Cookie `laravel_session` terkirim di respons
- [ ] Ketiga endpoint DataTables membalas JSON valid
- [ ] Uji manual §Verifikasi (baris F3)
- [ ] Commit: `chore(upgrade): Laravel 11`

---

## Fase 4 — Laravel 11 → 12

**`composer.json`**

```json
"require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "yajra/laravel-datatables-oracle": "^12.0"
},
"require-dev": {
    "phpunit/phpunit": "^11.5.50",
    "nunomaduro/collision": "^8.6"
}
```

- [ ] `composer update --with-all-dependencies`
- [ ] `php artisan about`
- [ ] `php artisan test`
- [ ] Uji manual §Verifikasi (baris F4)
- [ ] Commit: `chore(upgrade): Laravel 12`

---

## Fase 5 — Laravel 12 → 13

**`composer.json`**

```json
"require": {
    "php": "^8.3",
    "laravel/framework": "^13.0",
    "laravel/sanctum": "^4.0",
    "laravel/tinker": "^3.0",
    "yajra/laravel-datatables-oracle": "^13.0"
},
"require-dev": {
    "phpunit/phpunit": "^12.5",
    "nunomaduro/collision": "^8.6"
}
```

- [ ] `composer update --with-all-dependencies`
- [ ] `Kernel.php`: `ValidateCsrfToken` → `\Illuminate\Foundation\Http\Middleware\PreventRequestForgery`
- [ ] `php artisan about` → Laravel 13.x
- [ ] `php artisan test`
- [ ] Log diperiksa: `Select-String storage\logs\laravel.log 'Deprecated'` → 0 hasil
- [ ] `POST /` tanpa token → **419**
- [ ] `php artisan migrate --force` di staging (harus "Nothing to migrate")
- [ ] Uji manual §Verifikasi (baris F5)
- [ ] `composer audit` → tidak ada advisory
- [ ] Commit: `chore(upgrade): Laravel 13`

---

## Verifikasi manual per halaman

Isi kolom "Sebelum" saat masih di Laravel 9 **sebelum** Fase 2 dimulai; kolom "Sesudah"
diisi di akhir setiap fase. Tanda: ✅ sama, ❌ beda/error (catat di lembar masalah).

| # | Halaman / aksi | Cara | Sebelum | F2 | F3 | F4 | F5 |
|---|---|---|---|---|---|---|---|
| 1 | Dashboard kasir | buka `/` | | | | | |
| 2 | Buat order baru | isi nama + WhatsApp → simpan | | | | | |
| 3 | Halaman transaksi | buka `/transaction/{track}` | | | | | |
| 4 | Tambah item ke order | pilih item, qty, simpan | | | | | |
| 5 | Hapus item order | tombol hapus baris | | | | | |
| 6 | Simpan pembayaran | isi bayar → simpan | | | | | |
| 7 | Batalkan order | tombol cancel | | | | | |
| 8 | Daftar order terbaru | `/recent_customer` | | | | | |
| 9 | Admin home + counter | `/admin` | | | | | |
| 10 | Tabel customer + filter tanggal | `/admin/customerData` | | | | | |
| 11 | Detail order | `/admin/orderDetail/{track}` | | | | | |
| 12 | Daftar item | `/admin/itemData` | | | | | |
| 13 | Ubah item | `/admin/updateItemData/{id}` → simpan | | | | | |
| 14 | Finance home + total | `/finance` | | | | | |
| 15 | Rekap foto | `/finance/recap/photo` | | | | | |
| 16 | Rekap non-foto | `/finance/recap/nonPhoto` | | | | | |
| 17 | Rekap frame | `/finance/recap/frame` | | | | | |
| 18 | Filter tanggal rekap | pilih rentang → submit | | | | | |
| 19 | Progress customer | `/finance/progress` | | | | | |
| 20 | Invoice | `/invoice/customer/{track}` | | | | | |
| 21 | API tanpa token | `GET /api/user` → 401 | | | | | |
| 22 | CSRF | `POST /` tanpa token → 419 | | | | | |

Nominal pada #14–#18 harus **identik** dengan sebelum upgrade. Jika berbeda, hentikan fase
dan bandingkan query di controller dengan baseline.

---

## Lembar masalah

| Tanggal | Fase | Gejala | Dugaan penyebab | Tindakan | Status |
|---|---|---|---|---|---|
| | | | | | |
| | | | | | |
| | | | | | |

Klasifikasi sebelum menindaklanjuti:

| Jenis | Ciri | Tindakan |
|---|---|---|
| Regresi upgrade | Muncul pertama kali di fase tertentu, hilang bila fase di-rollback | Perbaiki di fase itu; jangan lanjut |
| Bug lama | Sudah ada di baseline Laravel 9 | Catat, tutup di Fase 6 — jangan campur dengan upgrade |
| Data kotor | Hanya pada sebagian record | Bersihkan data, bukan kode |

---

## Fase 6 — Modernisasi (setelah produksi stabil)

- [ ] `bootstrap/app.php` memakai `Application::configure()` (rencana §10.1)
- [ ] `bootstrap/providers.php` dibuat
- [ ] Jadwal backup pindah ke `routes/console.php`
- [ ] Rate limiter API pindah ke `AppServiceProvider::boot()`
- [ ] `app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php`,
      `app/Providers/RouteServiceProvider.php` dihapus
- [ ] `php artisan route:list` → jumlah rute sama seperti sebelumnya
- [ ] `php artisan schedule:list` → backup tampil
- [ ] `GET /up` → 200
- [ ] `tests/CreatesApplication.php` dihapus, `tests/TestCase.php` disederhanakan
- [ ] Autentikasi dipasang **atau** diputuskan secara tertulis untuk tidak dipasang
- [ ] `route('login')` dan `RouteServiceProvider::HOME` tidak lagi menunjuk rute yang tidak ada
- [ ] Validasi input ditambahkan pada endpoint tulis
- [ ] Commit terpisah untuk setiap butir

---

## Go-live

- [ ] `php artisan test` hijau di staging dengan dump produksi terbaru
- [ ] `composer validate --strict`
- [ ] `composer audit` bersih
- [ ] Dump database **baru** (beberapa menit sebelum deploy)
- [ ] `php artisan down --retry=60`
- [ ] Deploy kode + `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan migrate --force`
- [ ] `php artisan optimize:clear`
- [ ] `php artisan config:cache route:cache view:cache`
- [ ] `php artisan up`
- [ ] Verifikasi 22 baris tabel di atas pada **produksi** (minimal #1, #3, #4, #10, #14, #15, #20, #22)
- [ ] `storage/logs/laravel.log` dipantau 24 jam
- [ ] Backup otomatis diverifikasi berjalan malam berikutnya
- [ ] Tag rilis: `git tag v2.0-laravel13`

**Rollback bila gagal:** lihat rencana §13. Siapkan perintahnya sebelum deploy, jangan dicari
saat panik.
