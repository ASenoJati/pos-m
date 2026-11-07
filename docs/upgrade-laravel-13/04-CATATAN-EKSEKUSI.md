# Catatan Eksekusi — 30 Oktober 2026

Dokumen ini mencatat apa yang **benar-benar dikerjakan**, termasuk penyimpangan dari rencana
di [01-RENCANA-UPGRADE.md](01-RENCANA-UPGRADE.md). Dokumen rencana tidak diubah agar tetap
menjadi acuan awal; perbedaan dijelaskan di sini.

## Hasil akhir

| Item | Nilai |
|---|---|
| Framework | **Laravel 13.39.0** |
| PHP | 8.3.36 |
| Test | **9 lulus, 37 assertion** |
| `composer audit` | **tidak ada advisory** |
| `composer validate` | valid |
| Deprecation di log | **0** |
| Rute | 31 (29 web + 1 API + 1 fallback bawaan) |
| Working tree | bersih |

Rantai commit pada branch `upgrade/laravel-13`:

| Commit | Isi | Tag |
|---|---|---|
| `b01f988` | baseline kondisi produksi (363 perubahan yang belum pernah di-commit) | `baseline-l9` |
| `f2c410a` | sinkronkan skema soft delete, perbaiki backup, hapus kode mati, test asap | — |
| `7746574` | Laravel 9 → 10 | — |
| `97b4f7a` | Laravel 10 → 11 | — |
| `402b2f5` | Laravel 11 → 12 | — |
| `bf3420e` | Laravel 12 → 13 | `v13.0-upgrade` |

## Rencana vs aktual

| Fase | Rencana | Aktual | Catatan |
|---|---|---|---|
| 0 | Baseline Git + dump DB | Baseline Git selesai; dump DB **dilewati** atas permintaan Anda | Tag `baseline-l9` |
| 1 | Migrasi rekonsiliasi, backup, hapus kode mati | Selesai semua | Ditambah perbaikan `firstOrFail` (T-08) |
| 2 | L9 → L10 | Selesai | `$dates` sudah dihapus lebih awal (lihat di bawah) |
| 3 | L10 → L11 | Selesai | Middleware kustom dihapus, `config/sanctum.php` disesuaikan |
| 4 | L11 → L12 | Selesai | Tanpa perubahan kode |
| 5 | L12 → L13 | Selesai | `ValidateCsrfToken` → `PreventRequestForgery` |
| 7 | Go-live | **Belum** | Menunggu MySQL dan uji manual |

## Penyimpangan dari rencana

### 1. `$dates` dihapus di Fase 1, bukan Fase 2

Rencana menempatkannya di fase L10 (karena `$dates` baru dihapus di Laravel 10). Dikerjakan
lebih awal karena aman: trait `SoftDeletes` sudah meng-cast `deleted_at` secara otomatis sejak
Laravel 8, sehingga perilaku di Laravel 9 tidak berubah. Sudah dibuktikan oleh test asap yang
hijau di L9 sebelum naik ke L10.

### 2. `fruitcake/laravel-cors` dihapus di Fase 2, bukan Fase 3

Paket ini hanya mendukung sampai Laravel 9, sehingga menjadi penghalang saat naik ke L10.
Middleware-nya diganti `Illuminate\Http\Middleware\HandleCors` (sudah ada di core sejak 9.2)
dan `config/cors.php` tidak berubah.

### 3. `yajra/laravel-datatables` (meta) diganti `-oracle` di Fase 1

Rencana §6.2 menyebut penggantian ini; dikerjakan bersamaan dengan pembersihan paket tak
terpakai agar satu kali `composer update`. Empat paket (`-html`, `-buttons`, `-editor`,
`-fractal`) hilang dari jalur upgrade.

### 4. Migrasi tambahan untuk Sanctum 4 (tidak ada di rencana)

Saat memeriksa migration Sanctum 4, ternyata kolom `expires_at` ditambahkan pada
`personal_access_tokens`. Dibuat migrasi
`2026_10_30_000001_add_expires_at_to_personal_access_tokens.php` yang dijaga
`Schema::hasColumn`, bukan mem-publish migration Sanctum (yang akan menduplikasi tabel).

### 5. `tests/TestCase.php` + `CreatesApplication` **tidak** diubah

Rencana §15.2 menyarankan menghapus trait `CreatesApplication`. Ternyata trait itu masih
diperlukan di Laravel 9–13 dan seluruh test hijau tanpa perubahan. Perubahan ini ditunda ke
Fase 6 (modernisasi) agar tidak menambah risiko pada jalur upgrade.

### 6. Fase 6 (bootstrap/app.php) belum dikerjakan

Sesuai rencana, restrukturisasi `bootstrap/app.php` adalah refactor terpisah setelah aplikasi
stabil di Laravel 13. Struktur lama (`app/Http/Kernel.php`, `app/Console/Kernel.php`,
`app/Exceptions/Handler.php`, `RouteServiceProvider`) tetap berjalan dan sudah diuji.

## Perubahan di luar repositori

| Perubahan | Alasan | Cara membatalkan |
|---|---|---|
| `php.ini` Laragon: `extension=pdo_sqlite` diaktifkan (baris 961) | Test memakai SQLite in-memory; MySQL tidak berjalan dan PHP hanya punya driver `pdo_mysql` | Kembalikan dari `C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.ini.bak-sebelum-upgrade` |
| `composer.json`: `"config": { "policy": false }` sementara | Composer 2.10 memblokir semua rilis Laravel 10/11 karena advisory — tanpa ini Composer memasang `laravel/framework 10.x-dev` | **Sudah dicabut** di Fase 5; `composer audit` kini bersih |

## Temuan saat eksekusi

1. **Composer 2.10 memblokir paket ber-advisory.** Percobaan pertama naik ke L10 justru memasang
   `laravel/framework 10.x-dev` (branch), bukan rilis stabil, karena semua tag 10.x diblokir.
   Diperbaiki dengan `config.policy=false` sementara + `minimum-stability: stable`, lalu
   policy dinyalakan kembali setelah mencapai Laravel 13.
2. **`route:cache` berhasil** meski `routes/api.php` memakai closure — Laravel 11+ mendukung
   closure route di cache. Artinya `php artisan optimize` aman dipakai saat deploy.
3. **Verifikasi CSRF** tidak bisa lewat `POST` di test (middleware CSRF dimatikan saat
   `APP_ENV=testing`). Diganti dengan assertion bahwa grup `web` memuat middleware CSRF —
   versi-agnostik, tetap berlaku setelah rename ke `PreventRequestForgery`.

## Verifikasi yang sudah dilakukan

```text
php artisan --version .......... Laravel Framework 13.39.0
php artisan test ............... 9 passed (37 assertions)
composer audit ................. No security vulnerability advisories found
composer validate .............. ./composer.json is valid
php artisan route:list ......... 31 routes
php artisan route:cache ........ Routes cached successfully
php artisan optimize:clear ..... config, cache, compiled, events, routes, views: DONE
```

Cakupan test asap (`tests/Feature/SmokeTest.php`): dashboard kasir, halaman transaksi, item
transaksi, recent customer, seluruh halaman admin, ketiga endpoint DataTables, seluruh halaman
finance, invoice, keberadaan middleware CSRF, dan 404 untuk `track` yang tidak dikenal.

## Yang belum diverifikasi (butuh MySQL)

| Item | Alasan | Risiko |
|---|---|---|
| `php artisan migrate` pada database MySQL | Server MySQL tidak berjalan | Rendah — dua migrasi baru dijaga `Schema::hasColumn`, idempoten |
| `php artisan app:database-backup` | Butuh server MySQL + `mysqldump` | Rendah — perintah sudah diuji sintaksnya, bukan eksekusinya |
| Perbandingan nominal rekap finance sebelum vs sesudah | Butuh data nyata | Sedang — lakukan sesuai §15.4 rencana |
| Uji manual 22 halaman | Butuh browser + data | Wajib sebelum deploy |

## Langkah berikutnya

1. Nyalakan MySQL, lalu di **staging** yang berisi dump produksi:
   `php artisan migrate` → bandingkan `SHOW CREATE TABLE checkouts/customers/items`.
2. Jalankan `php artisan app:database-backup` dan uji restore file hasilnya.
3. Uji manual 22 baris di [03-CHECKLIST-EKSEKUSI.md](03-CHECKLIST-EKSEKUSI.md).
4. Deploy sesuai §11.2 rencana (`composer install --no-dev`, `migrate --force`, `optimize`).
5. Putuskan kelanjutan Fase 6: migrasi ke `bootstrap/app.php` dan pemasangan autentikasi.
   Sampai itu dikerjakan, `/admin`, `/finance`, dan `/transaction` **masih terbuka tanpa login**
   (temuan T-03 belum ditutup).
