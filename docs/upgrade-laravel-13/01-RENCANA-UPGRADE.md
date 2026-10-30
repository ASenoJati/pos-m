# Rencana Upgrade Laravel 9 → 13 — Muning Photo (`kemuning`)

| Item | Nilai |
|---|---|
| Versi saat ini | Laravel **9.52.16** (PHP 8.3.36) |
| Versi target | Laravel **13.x** (rilis 17 Maret 2026) |
| Metode | Satu branch, empat commit bertahap (9→10→11→12→13) |
| Estimasi | **5,5 hari kerja** + buffer 30% ≈ 7 hari kerja (±1,5–2 minggu kalender) |
| Risiko keseluruhan | **Sedang** — kode kecil, tetapi tanpa test dan tanpa baseline Git |
| Dokumen pendamping | [02-ANALISIS-PROYEK.md](02-ANALISIS-PROYEK.md), [03-CHECKLIST-EKSEKUSI.md](03-CHECKLIST-EKSEKUSI.md) |

---

## 1. Tujuan dan ruang lingkup

**Tujuan**

1. Menjalankan aplikasi di Laravel 13.x pada PHP 8.3 tanpa perubahan perilaku bagi pengguna.
2. Menghilangkan dependensi yang sudah EOL/abandoned (`fruitcake/laravel-cors`,
   `spatie/laravel-ignition`, `yajra` meta-package).
3. Meninggalkan proyek dalam kondisi yang bisa dites dan di-rollback (baseline Git,
   test asap, backup yang benar-benar berjalan).

**Di luar ruang lingkup** (dikerjakan terpisah agar diff upgrade tetap terbaca)

- Menambah/mengubah fitur, termasuk pemasangan autentikasi (lihat §10.2).
- Migrasi tampilan ke Vite/Tailwind, atau mengganti jQuery/DataTables.
- Perbaikan desain skema (mengubah kolom nominal dari `string` ke `decimal`).

**Definisi selesai (DoD)**

- `composer show laravel/framework` menampilkan `13.x`.
- `php artisan about` bersih: tidak ada error, tidak ada deprecation di log.
- Semua rute di §15 membalas status dan isi yang sama seperti sebelum upgrade.
- `php artisan migrate` di database kosong menghasilkan skema yang **sama** dengan produksi
  (termasuk `deleted_at`).
- Test asap lulus di CI/lokal.
- `storage/logs/laravel.log` bersih dari `Deprecated:` dan `ErrorException` selama 24 jam
  pemakaian nyata.

---

## 2. Mengapa Laravel 13

| Versi | PHP | Rilis | Bug fix sampai | Security fix sampai | Status per Okt 2026 |
|---|---|---|---|---|---|
| 9 | 8.0–8.2 | Feb 2022 | Agu 2023 | **Feb 2024** | ❌ EOL |
| 10 | 8.1–8.3 | Feb 2023 | Agu 2024 | **Feb 2025** | ❌ EOL |
| 11 | 8.2–8.4 | Mar 2024 | Sep 2025 | **Mar 2026** | ❌ EOL |
| 12 | 8.2–8.5 | Feb 2025 | Agu 2026 | Feb 2027 | ⚠️ security-only |
| 13 | 8.3–8.5 | Mar 2026 | Q3 2027 | Mar 2028 | ✅ didukung penuh |

Sumber: [Laravel Release Notes — Support Policy](https://laravel.com/docs/releases#support-policy).

Laravel 11 sudah melewati batas security fix, dan Laravel 12 hanya menerima perbaikan
keamanan. Karena PHP yang terpasang (8.3.36) sudah memenuhi syarat Laravel 13 (`^8.3`),
**target yang benar adalah 13**, bukan 11 atau 12. Berhenti di 12 hanya menunda pekerjaan
yang sama beberapa bulan lagi.

Laravel 13 sendiri digambarkan sebagai upgrade yang relatif ringan
("most Laravel applications may upgrade to Laravel 13 without changing much application code"),
sehingga bagian tersulit dari proyek ini justru **lompatan 9 → 11**, bukan 12 → 13.

---

## 3. Strategi

### 3.1 Pilihan yang dipertimbangkan

| Opsi | Cara | Kelebihan | Kekurangan |
|---|---|---|---|
| **A. Empat PR terpisah** | 4 branch/PR, masing-masing satu major | Review per tahap, CI per tahap | 4× overhead review & deploy; tidak cocok untuk tim 1 orang |
| **B. Satu lompatan 9 → 13** | Ubah `composer.json` langsung ke `^13.0` | Tercepat | Jika gagal, penyebabnya bercampur antara 4 major; sulit di-bisect |
| **C. Satu branch, empat commit (dipilih)** | `upgrade/13` dengan commit `L10`, `L11`, `L12`, `L13` | Satu konteks kerja, histori tetap bisa di-bisect per major, rollback granular | Disiplin commit harus dijaga |

### 3.2 Keputusan penting: struktur lama dipertahankan sampai akhir

Skeleton Laravel 11 ke atas memakai `bootstrap/app.php` dan menghapus
`app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php`, serta array
`providers` di `config/app.php`.

Namun panduan resmi Laravel 11 menyatakan struktur aplikasi Laravel 10 **tetap didukung**,
dan secara eksplisit **tidak menyarankan** memigrasikan struktur saat upgrade:

> "we do not recommend that Laravel 10 applications upgrading to Laravel 11 attempt to
> migrate their application structure, as Laravel 11 has been carefully tuned to also
> support the Laravel 10 application structure."
> — [Laravel 11 Upgrade Guide](https://laravel.com/docs/11.x/upgrade)

**Keputusan:** selama Fase 2–5 kita hanya menaikkan dependensi dan memperbaiki API yang
berubah. Restrukturisasi ke `bootstrap/app.php` dikerjakan **setelah** aplikasi hijau di
Laravel 13, sebagai refactor terpisah yang mudah direview dan mudah di-rollback (Fase 6).

Alasannya: mencampur "ganti major framework" dengan "bongkar bootstrap" membuat setiap
kegagalan punya dua kemungkinan penyebab. Ini praktik yang buruk untuk aplikasi tanpa test.

### 3.3 Urutan kerja

```
Fase 0  Amankan baseline        ── commit, dump DB, staging, test asap
Fase 1  Tutup drift & bug       ── migrasi softDeletes, hapus dead code, fix backup
Fase 2  9  → 10                 ── commit "L10"
Fase 3  10 → 11                 ── commit "L11"
Fase 4  11 → 12                 ── commit "L12"
Fase 5  12 → 13                 ── commit "L13"
Fase 6  Modernisasi (opsional)  ── bootstrap/app.php, auth, validasi
Fase 7  Go-live                 ── deploy, verifikasi, monitoring
```

---

## 4. Fase 0 — Amankan baseline

**Tujuan:** menciptakan titik balik. Tanpa ini, seluruh fase berikutnya tidak bisa
di-rollback. Ini menutup temuan **T-01**.

### Langkah

1. **Commit kondisi apa adanya** (termasuk 337 file terhapus dan 24 file untracked):

   ```powershell
   git add -A
   git commit -m "chore: baseline kondisi produksi sebelum upgrade Laravel"
   git tag baseline-l9
   git checkout -b upgrade/laravel-13
   ```

2. **Berhenti melacak `vendor/` dan `node_modules/`** (commit terpisah, jangan dicampur):

   ```powershell
   git rm -r --cached vendor
   git commit -m "chore: berhenti melacak vendor/ (sudah ada di .gitignore)"
   ```

3. **Dump database** dan simpan di luar folder yang di-deploy:

   ```powershell
   mysqldump --user=root --host=127.0.0.1 --databases muningphoto `
     --result-file="D:\backup\muningphoto-sebelum-upgrade-2026-10-30.sql"
   ```

4. **Siapkan database staging** dari dump yang sama:

   ```sql
   CREATE DATABASE muningphoto_staging CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   ```powershell
   mysql --user=root muningphoto_staging < "D:\backup\muningphoto-sebelum-upgrade-2026-10-30.sql"
   ```

5. **Buat `.env.staging`** yang menunjuk `muningphoto_staging`, lalu jalankan aplikasi
   dari salinan direktori terpisah. Jangan menguji di database produksi.

6. **Tulis test asap** (detail di §15). Test ini adalah jaring pengaman yang menentukan
   apakah setiap fase berhasil.

### Kriteria selesai

- `git log` punya commit baseline dan tag `baseline-l9`.
- `git status` bersih.
- Dump database terverifikasi bisa di-restore (uji restore ke `muningphoto_staging`).
- Aplikasi berjalan normal dari database staging.

**Estimasi:** 0,5–1 hari.

---

## 5. Fase 1 — Tutup drift skema dan bug penghalang

**Tujuan:** memastikan migrasi bisa membangun ulang database produksi. Tanpa ini, staging
tidak bisa dibuat dari nol dan test asap tidak bisa memakai database bersih.

### 5.1 Rekonsiliasi kolom `deleted_at` (menutup T-02)

Empat model memakai `SoftDeletes`, query memfilter `deleted_at`, tetapi **tidak ada migrasi
yang membuat kolom tersebut**. Tambahkan migrasi rekonsiliasi yang aman dijalankan pada
database yang sudah punya kolomnya:

`database/migrations/2026_10_30_000000_add_soft_deletes_to_master_tables.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<int, string> */
    private array $tables = ['items', 'customers', 'checkouts'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropSoftDeletes();
                });
            }
        }
    }
};
```

Verifikasi bahwa skema hasil migrasi **identik** dengan produksi:

```powershell
php artisan migrate --database=mysql          # di staging, harus "Nothing to migrate" atau menambah 1 baris
php artisan migrate:fresh --seed --env=staging
php artisan schema:dump --prune               # opsional: simpan snapshot skema
```

Bandingkan struktur tabel antara staging dan produksi:

```sql
SHOW CREATE TABLE checkouts;
SHOW CREATE TABLE customers;
```

Jika ternyata kolom `deleted_at` **tidak** ada di produksi, jangan buat migrasi di atas —
sebaliknya hapus `SoftDeletes` dari model dan hapus `whereNull('checkouts.deleted_at')`.
Keputusan ini harus diambil dari dump produksi, bukan dari asumsi.

### 5.2 Perbaiki command backup (menutup T-04)

Tiga cacat sekaligus: nama command tidak cocok, `env()` dipakai saat runtime, dan kegagalan
`exec()` tidak diperiksa.

`app/Console/Kernel.php` — samakan nama command:

```php
$schedule->command('app:database-backup')->dailyAt('23:30');
```

`app/Console/Commands/DatabaseBackup.php` — gunakan konfigurasi, `Process`, dan periksa hasil:

```php
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

public function handle(): int
{
    $db = config('database.connections.mysql');

    Storage::makeDirectory('backup');

    $filename = 'backup-'.now()->format('Y-m-d').'.sql';
    $path = Storage::path('backup/'.$filename);

    $result = Process::timeout(600)->run([
        'mysqldump',
        '--host='.$db['host'],
        '--port='.$db['port'],
        '--user='.$db['username'],
        '--password='.$db['password'],
        '--single-transaction',
        '--routines',
        $db['database'],
        '--result-file='.$path,
    ]);

    if ($result->failed()) {
        $this->error('Backup gagal: '.$result->errorOutput());
        report(new \RuntimeException('Backup database gagal: '.$result->errorOutput()));

        return self::FAILURE;
    }

    $this->info('Backup tersimpan: '.$path);

    return self::SUCCESS;
}
```

Keuntungan: tidak butuh `gzip` (yang tidak ada di Windows), tidak butuh `shell_exec`,
kegagalan tercatat di log, dan `config()` tetap benar meski `config:cache` aktif.

### 5.3 Hapus kode mati (menutup T-05)

Menghapus permukaan yang tidak dipakai membuat setiap error pasca-upgrade pasti berasal dari
kode yang benar-benar berjalan.

```powershell
git rm -r app/Http/Controllers/oldController
git rm -r resources/views/v1
git rm resources/views/v2.zip
git rm "resources/views/v2/invoice/customer-invoice.blade copy.php"
git rm "resources/views/v2/transaction.blade copy.php"
```

Untuk paket yang **tidak dipakai sama sekali** di kode (diverifikasi dengan grep, lihat
T-05 dan §6): hapus `realrashid/sweet-alert` dan `barryvdh/laravel-dompdf`.

> Jika ekspor PDF direncanakan dalam waktu dekat, pertahankan `barryvdh/laravel-dompdf` dan
> naikkan ke `^3.0` (bukan dihapus). Lihat catatan dompdf di §6.

### 5.4 Buat test asap bisa berjalan

Perbaiki `phpunit.xml` agar memakai SQLite in-memory, lalu tulis test yang mengunci
perilaku semua halaman (§15). Ini prasyarat mutlak sebelum Fase 2.

### Kriteria selesai

- `php artisan migrate:fresh --seed` sukses di database kosong.
- `php artisan test` hijau.
- `php artisan app:database-backup` menghasilkan file `.sql` yang bisa di-restore.
- Folder `oldController`, `v1`, dan file duplikat hilang.

**Estimasi:** 1 hari.

---

## 6. Matriks dependensi

Semua angka di bawah diverifikasi dari `composer.json` masing-masing paket atau blok
"Requires" di Packagist pada 30 Oktober 2026, bukan dari asumsi.

### 6.1 Target per tahap

| Paket | Sekarang | L10 | L11 | L12 | L13 | Catatan |
|---|---|---|---|---|---|---|
| `laravel/framework` | 9.52.16 | `^10.0` | `^11.0` | `^12.0` | `^13.0` | PHP min: 8.1 / 8.2 / 8.2 / 8.3 |
| `yajra/laravel-datatables-oracle` | 10.11.4 (via meta) | `^10.0` | `^11.0` | `^12.0` | `^13.0` | Satu major per major Laravel; v13 butuh PHP ^8.3 |
| `yajra/laravel-datatables` (meta) | 9.0.0 | — | — | — | **hapus** | Ganti ke `-oracle` langsung; `-html/-buttons/-editor/-fractal` tidak dipakai |
| `laravel/sanctum` | 2.15.1 | `^3.2` | `^4.0` | `^4.0` | `^4.0` | v4: migration tidak auto-load, 3 key middleware berubah |
| `laravel/tinker` | 2.9.0 | `^2.9` | `^2.10` | `^2.10` | `^3.0` | v2 hanya sampai `illuminate ^12` |
| `fruitcake/laravel-cors` | 2.2.0 | **hapus** | **hapus** | **hapus** | **hapus** | Abandoned; CORS sudah di core sejak 9.2 |
| `barryvdh/laravel-dompdf` | 2.2.0 | `^3.0` | `^3.0` | `^3.0` | `^3.0` | Tidak dipakai → boleh dihapus; jika dipakai, satu major untuk semua |
| `realrashid/sweet-alert` | 7.2.0 | `^7.3` | `^7.3` | `^8.0` | `^8.0` | Tidak dipakai → hapus; v8 = rewrite fluent API |
| `apalfrey/select2-bootstrap-5-theme` | 1.3.0 | hapus | hapus | hapus | hapus | Paket aset JS/CSS, bukan library PHP; pindah ke `package.json`/CDN |
| `guzzlehttp/guzzle` | 7.8.1 | `^7.9` | `^7.9` | `^7.9` | `^7.9` | L13 juga mengizinkan `^8.0` |
| `nunomaduro/collision` | 6.4.0 | `^7.0` | `^8.1` | `^8.6` | `^8.6` | 8.9.x conflict `laravel <11.48` dan `phpunit <11.5.50` |
| `phpunit/phpunit` | 9.6.19 | `^10.0` | `^11.5` | `^11.5.50` | `^12.5` | 12.5 butuh PHP ≥8.3 |
| `spatie/laravel-ignition` | 1.7.0 | `^2.0` | **hapus** | **hapus** | **hapus** | Skeleton 11+ tidak memakainya; error page sudah bawaan |
| `fakerphp/faker` | 1.23.1 | `^1.23` | `^1.23` | `^1.23` | `^1.23` | Tidak terikat Laravel |
| `mockery/mockery` | 1.6.12 | `^1.6` | `^1.6` | `^1.6` | `^1.6` | Tidak terikat Laravel |
| `laravel/sail` | 1.29.3 | `^1.26` | `^1.26` | `^1.41` | `^1.41` | Tidak dipakai (Laragon) → boleh dihapus |
| `php` | `^8.0` | `^8.1` | `^8.2` | `^8.2` | `^8.3` | Runtime sudah 8.3 |

### 6.2 Catatan per paket

- **Yajra** — keluarga paket harus satu major. Saat ini `yajra/laravel-datatables` (meta)
  menarik `-oracle`, `-html`, `-buttons`, `-editor`, `-fractal`; kode hanya memakai
  `DataTables::of()` dari `-oracle`. Mengganti ke `-oracle` langsung memotong 4 paket
  dari jalur upgrade.
- **Sanctum v4** — migration tidak lagi auto-load. Jalankan
  `php artisan vendor:publish --tag=sanctum-migrations` dan pastikan file migrasi baru
  **tidak bertabrakan** dengan `2019_12_14_000001_create_personal_access_tokens_table.php`
  yang sudah ada. Di `config/sanctum.php`, tiga key ini harus menunjuk kelas yang ada:

  ```php
  'middleware' => [
      'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
      'encrypt_cookies'      => Illuminate\Cookie\Middleware\EncryptCookies::class,
      'validate_csrf_token'  => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
  ],
  ```
- **CORS** — `fruitcake/laravel-cors` **abandoned**. Middleware `HandleCors` sudah ada di
  core sejak Laravel 9.2, jadi ganti `Fruitcake\Cors\HandleCors` menjadi
  `Illuminate\Http\Middleware\HandleCors`. `config/cors.php` tetap kompatibel.
- **Collision + PHPUnit** — collision 8.9 mem-`conflict` `laravel/framework <11.48` dan
  `phpunit/phpunit <11.5.50`. Naikkan framework, collision, dan PHPUnit **dalam satu commit**
  agar Composer tidak menemui jalan buntu.
- **Dompdf v3** (jika dipertahankan) — publish ulang config: `enable_remote` menjadi `false`
  (gambar/URL remote berhenti dimuat), ada key baru `allowedRemoteHosts` dan
  `artifactPathValidation`, dan `setOptions()` menjadi `setOption()`. Karena pemakaian di
  proyek ini masih dikomentari (`InvoiceController`), menghapus paket lebih murah.

### 6.3 `composer.json` target (akhir Fase 5)

```json
{
    "name": "muningphoto/kemuning",
    "type": "project",
    "description": "POS dan manajemen order Muning Photo.",
    "license": "proprietary",
    "require": {
        "php": "^8.3",
        "guzzlehttp/guzzle": "^7.9",
        "laravel/framework": "^13.0",
        "laravel/sanctum": "^4.0",
        "laravel/tinker": "^3.0",
        "yajra/laravel-datatables-oracle": "^13.0"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.6",
        "phpunit/phpunit": "^12.5"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

Perubahan yang disengaja dari kondisi sekarang: `minimum-stability` dari `dev` → `stable`,
`name` tidak lagi `laravel/laravel`, `laravel/sail` dihapus (tidak dipakai di Laragon),
dan seluruh paket yang tidak dipakai kode dihilangkan.

---

## 7. Fase 2 — Laravel 9 → 10

**Estimasi:** 0,5 hari. **Commit:** `chore(upgrade): Laravel 10`.

### Langkah

1. Ubah `composer.json`:

   ```json
   "php": "^8.1",
   "laravel/framework": "^10.0",
   "laravel/sanctum": "^3.2",
   "yajra/laravel-datatables-oracle": "^10.0"
   ```

   Dev: `spatie/laravel-ignition: ^2.0`, `nunomaduro/collision: ^7.0`, `phpunit/phpunit: ^10.0`.

2. Update dependensi:

   ```powershell
   composer update laravel/framework laravel/sanctum yajra/laravel-datatables-oracle `
     nunomaduro/collision phpunit/phpunit spatie/laravel-ignition --with-all-dependencies
   ```

3. **Hapus properti `$dates` di empat model** — ini breaking change Laravel 10 yang paling
   relevan. SoftDeletes sudah otomatis meng-cast `deleted_at` menjadi `datetime`, jadi
   barisnya cukup dihapus, bukan dipindahkan:

   ```diff
   -    protected $dates = ['deleted_at'];
   ```

   Berlaku untuk `app/Models/User.php:15`, `Customer.php:14`, `Item.php:14`, `Checkout.php:14`.
   Jika ada atribut tanggal lain di masa depan, gunakan `$casts` (bukan `$dates` lagi).

4. Perbaiki `phpunit.xml` untuk PHPUnit 10: hapus atribut `processUncoveredFiles` pada
   `<coverage>`, pindahkan `<include>` ke elemen `<source>`.

5. Jalankan test asap dan telusuri manual area §15.

### Jebakan yang spesifik untuk proyek ini

- **`$dates`** — jika terlewat, `deleted_at` tidak lagi di-cast, dan
  `whereNull('deleted_at')` tetap jalan tetapi perbandingan tanggal di tempat lain bisa
  berubah perilaku (string vs Carbon).
- **`RateLimiter`** — `Limit::perMinute()` di
  `app/Providers/RouteServiceProvider.php:59` tetap valid. Hanya kode yang memakai
  `decayMinutes` yang perlu diubah ke `decaySeconds` (tidak ada di proyek ini).
- **`DB::raw` / `DB::select`** — proyek memakai `DB::select()` dengan string SQL di
  `oldController` (sudah dihapus di Fase 1). Empat controller finance memakai query builder,
  aman. Tidak ada cast `(string)` pada ekspresi database.
- **`MocksApplicationServices`** dihapus — tidak dipakai test.

---

## 8. Fase 3 — Laravel 10 → 11

**Estimasi:** 1 hari. **Commit:** `chore(upgrade): Laravel 11`.

Ini fase terberat. Bukan karena API berubah banyak, tetapi karena toolchain dev ikut
berpindah major dan `sanctum` melompati dua major sekaligus.

### Langkah

1. Ubah `composer.json`:

   ```json
   "php": "^8.2",
   "laravel/framework": "^11.0",
   "laravel/sanctum": "^4.0",
   "yajra/laravel-datatables-oracle": "^11.0"
   ```

   Dev: `nunomaduro/collision: ^8.1`, `phpunit/phpunit: ^11.5`, dan **hapus**
   `spatie/laravel-ignition` serta `laravel/sail`.

2. Publish migration Sanctum v4 (migration tidak lagi auto-load):

   ```powershell
   php artisan vendor:publish --tag=sanctum-migrations
   ```

   Periksa isi file yang dihasilkan. Jika migrasi `personal_access_tokens` yang baru
   menduplikasi migrasi lama, hapus file baru dan sesuaikan migrasi lama
   (bandingkan kolomnya dengan dump produksi).

3. Sesuaikan `config/sanctum.php` seperti §6.2.

4. **Ganti middleware CORS** di `app/Http/Kernel.php:19`:

   ```diff
   -        \Fruitcake\Cors\HandleCors::class,
   +        \Illuminate\Http\Middleware\HandleCors::class,
   ```

   Lalu hapus paketnya: `composer remove fruitcake/laravel-cors`.

5. **Hapus middleware yang hanya meneruskan kelas framework.** Delapan file di
   `app/Http/Middleware/` tidak menambah konfigurasi apa pun (kecuali `$except` yang
   isinya sama dengan default framework). Ubah `Kernel.php` agar menunjuk kelas framework,
   lalu hapus filenya:

   | File dihapus | Pengganti di `Kernel.php` |
   |---|---|
   | `TrustProxies.php` | `\Illuminate\Http\Middleware\TrustProxies::class` |
   | `PreventRequestsDuringMaintenance.php` | `\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class` |
   | `TrimStrings.php` | `\Illuminate\Foundation\Http\Middleware\TrimStrings::class` |
   | `EncryptCookies.php` | `\Illuminate\Cookie\Middleware\EncryptCookies::class` |
   | `VerifyCsrfToken.php` | `\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class` |
   | `Authenticate.php`, `RedirectIfAuthenticated.php`, `TrustHosts.php` | `\Illuminate\Auth\Middleware\Authenticate::class`, `\Illuminate\Auth\Middleware\RedirectIfAuthenticated::class`, `\Illuminate\Http\Middleware\TrustHosts::class` |

   Catatan: Laravel 13 mengganti nama `ValidateCsrfToken` menjadi `PreventRequestForgery`
   (nama lama tetap ada sebagai alias deprecated). Lihat langkah Fase 5.

6. **Pertahankan struktur lama** (`bootstrap/app.php` versi sekarang, `app/Http/Kernel.php`,
   `app/Exceptions/Handler.php`, array `providers` di `config/app.php`). Jangan migrasi ke
   `bootstrap/app.php` gaya baru di fase ini — lihat alasan di §3.2.

7. **Periksa `->change()` di migrasi.** Laravel 11 menghapus kebutuhan `doctrine/dbal`, tetapi
   `change()` sekarang menghapus semua modifier yang tidak ditulis ulang. Proyek ini belum
   memakai `change()` sama sekali (diverifikasi), jadi tidak ada aksi — tetapi jangan
   menambahkan `change()` tanpa menulis ulang `nullable()`, `default()`, dan `comment()`.

8. **Jangan menyalin `config/*.php` dari skeleton baru.** Aplikasi ini pemilik config-nya
   sendiri (`SESSION_DRIVER=file`, `CACHE_DRIVER=file`, `QUEUE_CONNECTION=sync`), sehingga
   default baru skeleton (driver `database`) **tidak** berlaku dan tidak perlu diikuti.
   Yang perlu: pastikan `.env` tetap memuat `CACHE_DRIVER` selama `config/cache.php` masih
   membacanya.

### Uji khusus fase ini

- **CSRF masih aktif:** `POST /` tanpa token harus menghasilkan **419**, bukan 200.
  Ini membuktikan middleware group `web` benar-benar terpasang setelah perubahan Kernel.
- **Session masih jalan:** buka halaman, cek cookie `laravel_session` terkirim.
- **DataTables:** ketiga endpoint tabel (`/admin/customerData/table`,
  `/admin/itemData/table`, `/finance/progress/table`) harus membalas JSON dengan struktur
  yang sama (`draw`, `recordsTotal`, `data`).

---

## 9. Fase 4 & 5 — Laravel 11 → 12 → 13

### Fase 4 — Laravel 12 (estimasi 0,5 hari, commit `chore(upgrade): Laravel 12`)

Laravel 12 adalah rilis dengan breaking change paling sedikit di seluruh rantai ini.

```json
"php": "^8.2",
"laravel/framework": "^12.0",
"yajra/laravel-datatables-oracle": "^12.0"
```

Dev: `phpunit/phpunit: ^11.5.50` atau `^12.5`, `nunomaduro/collision: ^8.6`.

Yang perlu diperiksa: tidak ada. Fokus pada test asap dan `php artisan about`.

### Fase 5 — Laravel 13 (estimasi 0,5–1 hari, commit `chore(upgrade): Laravel 13`)

```json
"php": "^8.3",
"laravel/framework": "^13.0",
"yajra/laravel-datatables-oracle": "^13.0",
"laravel/tinker": "^3.0"
```

Dev: `phpunit/phpunit: ^12.5`, `nunomaduro/collision: ^8.6`.

Perubahan Laravel 13 yang berdampak pada proyek ini, sesuai
[Upgrade Guide 13.x](https://laravel.com/docs/13.x/upgrade):

| Perubahan | Dampak di proyek ini | Tindakan |
|---|---|---|
| `VerifyCsrfToken` → `PreventRequestForgery` | Proyek merujuk `ValidateCsrfToken` di `Kernel.php` | Ganti ke `\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class` |
| Session `serialization` default `json` di skeleton | Proyek punya `config/session.php` sendiri tanpa key ini → perilaku tidak berubah | Tidak ada aksi; **jangan** menambahkan `'serialization' => 'json'` tanpa rencana re-login semua user |
| Cache `serializable_classes` | Proyek hanya menyimpan nilai skalar di cache | Tidak ada aksi |
| Prefix cache/Redis berubah | Proyek memakai cache `file` tanpa prefix kustom | Tidak ada aksi; jika memakai `CACHE_PREFIX`, set eksplisit |
| Nama view paginasi Bootstrap 3 berubah | Proyek tidak memakai `->links()` | Tidak ada aksi |
| `Js::from` memakai Unicode tak ter-escape | Tidak dipakai | Tidak ada aksi |
| `JobAttempted`/`QueueBusy` berubah | Queue `sync`, tanpa listener | Tidak ada aksi |
| `symfony/polyfill-php85` mendefinisikan `array_first()` | Tidak ada helper global bernama itu | Tidak ada aksi |

Selain itu, periksa deprecation secara eksplisit:

```powershell
php artisan about
php artisan test
Get-Content storage\logs\laravel.log -Tail 200 | Select-String 'Deprecated|deprecated'
```

---

## 10. Fase 6 — Modernisasi pasca-upgrade (opsional, disarankan)

Dikerjakan **hanya setelah** Laravel 13 hijau dan sudah berjalan beberapa hari di produksi.
Setiap item adalah commit terpisah yang bisa di-rollback sendiri.

### 10.1 Migrasi ke `bootstrap/app.php`

Ini yang membuat struktur proyek sejajar dengan dokumentasi Laravel 13 dan menghapus
4 file (`app/Http/Kernel.php`, `app/Console/Kernel.php`, `app/Exceptions/Handler.php`,
`app/Providers/RouteServiceProvider.php`) serta array `providers` di `config/app.php`.

`bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

`bootstrap/providers.php`:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
];
```

Tiga hal yang harus ikut dipindahkan agar tidak ada perilaku yang hilang:

1. **Jadwal backup** — `app/Console/Kernel.php:19` pindah ke `routes/console.php`:

   ```php
   use Illuminate\Support\Facades\Schedule;

   Schedule::command('app:database-backup')->dailyAt('23:30');
   ```

2. **Rate limiter API** — `RouteServiceProvider::configureRateLimiting()` pindah ke
   `AppServiceProvider::boot()`:

   ```php
   use Illuminate\Cache\RateLimiting\Limit;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\RateLimiter;

   RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)
       ->by($request->user()?->id ?: $request->ip()));
   ```

3. **`Blade::directive('currency')`** sudah di `AppServiceProvider:27` — tidak perlu pindah.

Verifikasi setelah migrasi: `php artisan route:list` harus menampilkan 29 rute web + 1 rute
API, `php artisan schedule:list` menampilkan backup, dan **`GET /up` membalas 200**.

### 10.2 Autentikasi (menutup T-03)

Saat ini `/admin`, `/finance`, dan `/transaction` terbuka tanpa login. Ini keputusan produk,
bukan teknis — tetapi harus diputuskan sebelum go-live.

Opsi minimal (± setengah hari):

1. Tambahkan rute `GET|POST /login` dan `POST /logout`.
2. Buat `LoginController` yang memakai `Auth::attempt()` (model `User` sudah siap,
   `password` sudah di-hash oleh cast bawaan).
3. Bungkus rute sensitif:

   ```php
   Route::middleware('auth')->group(function () {
       // admin, finance, invoice
   });
   ```

4. Pastikan `route('login')` ada — `app/Http/Middleware/Authenticate.php:18` dan
   `RouteServiceProvider::HOME = '/home'` (`:14`) akan langsung error jika tidak.
   Ganti `HOME` menjadi `'/'` bila tidak ada halaman `/home`.

### 10.3 Validasi input (menutup T-06)

Tambahkan FormRequest pada endpoint tulis yang menerima `$request->all()`:

```php
$validated = $request->validate([
    'customer_id' => ['required', 'integer', 'exists:customers,id'],
    'track_id'    => ['required', 'string', 'max:32'],
    'item_id'     => ['required', 'integer', 'exists:items,id'],
    'qty'         => ['required', 'integer', 'min:1'],
    'note'        => ['nullable', 'string', 'max:255'],
    'discount'    => ['nullable', 'integer', 'min:0'],
]);
```

Setelah itu, aktifkan mode ketat di `AppServiceProvider::boot()` untuk menangkap
mass-assignment yang diam-diam dibuang:

```php
Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
```

Lakukan ini **setelah** validasi terpasang, bukan sebelum — jika tidak, aplikasi akan
melempar exception pada field yang selama ini tersimpan diam-diam.

### 10.4 Item lain

| Item | Alasan |
|---|---|
| Migrasi aset ke Vite | Saat ini `webpack.mix.js` tidak dipakai view mana pun; Vite baru berguna jika ada JS/CSS yang dibangun |
| Hapus `index.php` di root | Hanya relevan jika hosting masih menunjuk docroot ke root proyek |
| Indeks database pada `created_at` | Semua rekap memakai `whereBetween('created_at', ...)` tanpa indeks |
| Ubah kolom nominal ke `decimal(12,2)` | Menutup T-07, tetapi butuh migrasi data dan pengujian laporan keuangan |

---

## 11. Fase 7 — Go-live

### 11.1 Sebelum deploy

```powershell
php artisan test                                  # harus hijau
composer validate --strict
composer audit                                    # tidak ada advisory
php artisan route:list | Measure-Object -Line     # bandingkan jumlah rute dengan baseline
```

Uji di staging dengan **dump produksi terbaru**, bukan data contoh. Data produksi sering
memuat kasus tepi yang tidak ada di data uji (mis. `track` ganda, `progress` kosong,
`deleted_at` terisi).

### 11.2 Jendela pemeliharaan

Perkiraan 20–30 menit:

1. Aktifkan mode pemeliharaan: `php artisan down --render="errors::503" --retry=60`.
2. Backup database dan kode (`git archive` dari tag terakhir).
3. Deploy kode (branch/tag `upgrade/laravel-13`).
4. `composer install --no-dev --optimize-autoloader`
5. `php artisan migrate --force`
6. `php artisan optimize:clear`
7. `php artisan config:cache route:cache view:cache`
8. `php artisan up`

### 11.3 Sesudah deploy

| Cek | Cara | Ekspektasi |
|---|---|---|
| Versi framework | `php artisan about` | Laravel 13.x, PHP 8.3 |
| Halaman kasir | buka `/` | tampil, data hari ini benar |
| Buat order baru | `POST /` | redirect ke `/transaction/{track}` |
| Tambah item | modal transaksi | baris muncul, subtotal berubah |
| Tabel admin | `/admin/customerData` | DataTables terisi |
| Rekap finance | `/finance/recap/photo` | nominal sama dengan sebelum upgrade |
| Invoice | `/invoice/customer/{track}` | tampil, tanggal benar |
| CSRF | POST tanpa token | 419 |
| Log | `storage/logs/laravel.log` | tanpa `Deprecated`, tanpa `ErrorException` |
| Backup | `php artisan app:database-backup` | file `.sql` baru, bisa di-restore |

Catat hasilnya di [03-CHECKLIST-EKSEKUSI.md](03-CHECKLIST-EKSEKUSI.md).

---

## 12. Risiko dan mitigasi

| # | Risiko | Peluang | Dampak | Mitigasi |
|---|---|---|---|---|
| R1 | Tidak ada baseline → tidak bisa rollback | Tinggi (kondisi sekarang) | Kritis | Fase 0 wajib: commit + tag + dump |
| R2 | `deleted_at` tidak ada di migrasi → staging tidak bisa dibangun | Pasti | Tinggi | Migrasi rekonsiliasi di Fase 1, verifikasi dengan `SHOW CREATE TABLE` |
| R3 | Yajra tidak kompatibel → semua tabel 500 | Sedang | Tinggi | Naikkan `-oracle` **bersamaan** dengan framework di setiap tahap (constraint-nya saling mengikat, satu major per major Laravel); ganti meta-package sekarang |
| R4 | Composer deadlock (collision vs PHPUnit vs framework) | Sedang | Sedang | Naikkan framework + collision + PHPUnit dalam satu commit; `--with-all-dependencies` |
| R5 | Sanctum v4: migration ganda / 3 key config salah | Sedang | Sedang | Publish, bandingkan dengan dump produksi, uji `GET /api/user` (401, bukan 500) |
| R6 | Middleware hilang setelah perubahan Kernel → CSRF mati diam-diam | Sedang | **Kritis** | Test `POST /` = 419 di setiap fase; review `php artisan route:list -v` |
| R7 | Regresi perhitungan uang tanpa terdeteksi | Sedang | Tinggi | Snapshot HTML `/finance` sebelum vs sesudah pada data staging yang sama |
| R8 | `$dates` terlewat → `deleted_at` tidak di-cast | Sedang | Sedang | Grep `$dates` harus 0 hasil sebelum commit L10 |
| R9 | Backup tidak pernah jalan, lalu dibutuhkan saat rollback | Tinggi (kondisi sekarang) | Kritis | Perbaiki di Fase 1 + uji restore, jangan tunggu Fase 7 |
| R10 | Data produksi punya kasus tepi yang tidak ada di staging | Sedang | Sedang | Uji staging memakai dump produksi terbaru, bukan data contoh |

---

## 13. Rollback

| Skenario | Tindakan | Waktu |
|---|---|---|
| Gagal di tengah fase (sebelum deploy) | `git reset --hard <commit-fase-sebelumnya>` lalu `composer install` | < 5 menit |
| Gagal setelah deploy, kode masih baru | `php artisan down`, checkout tag `baseline-l9`, `composer install --no-dev`, `php artisan optimize:clear`, `php artisan up` | 10 menit |
| Gagal setelah deploy **dan** migrasi sudah jalan | Restore dump: `mysql muningphoto < dump.sql`, lalu rollback kode seperti di atas | 20–30 menit |
| Migrasi Fase 1 tidak kompatibel | `php artisan migrate:rollback --step=1` (migrasi rekonsiliasi punya `down()` yang aman) | 5 menit |

Aturan penting: **jangan** pernah menjalankan `migrate:fresh` atau `migrate:rollback` di
produksi tanpa dump yang baru diverifikasi. Migrasi pada proyek ini belum pernah diuji
terhadap database yang berisi data nyata.

---

## 14. Linimasa

| Fase | Isi | Estimasi | Kumulatif |
|---|---|---|---|
| 0 | Baseline, dump, staging, test asap | 1 hari | 1 hari |
| 1 | Drift skema, backup, hapus kode mati | 1 hari | 2 hari |
| 2 | Laravel 9 → 10 | 0,5 hari | 2,5 hari |
| 3 | Laravel 10 → 11 | 1 hari | 3,5 hari |
| 4 | Laravel 11 → 12 | 0,5 hari | 4 hari |
| 5 | Laravel 12 → 13 | 1 hari | 5 hari |
| 7 | Go-live + verifikasi | 0,5 hari | 5,5 hari |
| — | Buffer (30%) | 1,5 hari | **±7 hari kerja** |
| 6 | Modernisasi (opsional) | 1–2 hari | terpisah |

Dengan satu developer dan waktu kerja efektif 4–5 jam/hari, ini kira-kira **1,5–2 minggu
kalender**.

---

## 15. Lampiran A — Test asap (dikerjakan di Fase 0)

Ini bagian yang membuat seluruh rencana bisa dipercaya. Tanpa test, "sudah dites manual"
tidak bisa direproduksi di fase berikutnya.

### 15.1 `phpunit.xml` target

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>
```

### 15.2 `tests/TestCase.php`

Skeleton Laravel 11+ tidak lagi memakai trait `CreatesApplication`
(`Illuminate\Foundation\Testing\TestCase` sudah menemukan base path sendiri):

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
}
```

Hapus `tests/CreatesApplication.php`.

### 15.3 `tests/Feature/SmokeTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Checkout;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->item = Item::create(['name' => 'Frame 4R', 'price' => 50000, 'type' => 1]);

        $this->customer = Customer::create([
            'track'      => '20261030120000',
            'name'       => 'Pelanggan Uji',
            'whatsapp'   => '628123456789',
            'subtotal'   => 50000,
            'pay'        => 50000,
            'deficiency' => 0,
            'progress'   => 1,
        ]);

        Checkout::create([
            'customer_id' => $this->customer->id,
            'track_id'    => $this->customer->track,
            'item_id'     => $this->item->id,
            'qty'         => 1,
            'note'        => '-',
            'discount'    => 0,
        ]);
    }

    public function test_halaman_kasir_dan_transaksi(): void
    {
        $this->get('/')->assertOk();
        $this->get('/transaction/'.$this->customer->track)->assertOk();
        $this->get('/customer/item/'.$this->customer->track)->assertOk();
        $this->get('/recent_customer')->assertOk();
    }

    public function test_halaman_admin(): void
    {
        $this->get('/admin')->assertOk();
        $this->get('/admin/customerData')->assertOk();
        $this->get('/admin/orderDetail/'.$this->customer->track)->assertOk();
        $this->get('/admin/itemData')->assertOk();
        $this->get('/admin/updateItemData/'.$this->item->id)->assertOk();
    }

    public function test_endpoint_datatables_mengembalikan_json(): void
    {
        foreach ([
            '/admin/customerData/table',
            '/admin/itemData/table',
            '/finance/progress/table',
        ] as $uri) {
            $this->getJson($uri)
                ->assertOk()
                ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
        }
    }

    public function test_halaman_finance(): void
    {
        $this->get('/finance')->assertOk();
        $this->get('/finance/recap/photo')->assertOk();
        $this->get('/finance/recap/nonPhoto')->assertOk();
        $this->get('/finance/recap/frame')->assertOk();
        $this->get('/finance/progress')->assertOk();
    }

    public function test_invoice_dan_cetak(): void
    {
        $this->get('/invoice/customer/'.$this->customer->track)->assertOk();
    }

    public function test_csrf_masih_aktif(): void
    {
        $this->post('/')->assertStatus(419);
    }

    public function test_track_tidak_dikenal_tidak_menghasilkan_500(): void
    {
        $this->get('/transaction/999')->assertNotFound();
        $this->get('/invoice/customer/999')->assertNotFound();
    }
}
```

> Dua test terakhir sengaja **gagal** pada Fase 0 (karena T-03 dan T-08 belum diperbaiki).
> Itu memang tujuannya: test ini menjadi daftar pekerjaan yang harus hijau sebelum Fase 2.
> Perbaiki dengan `firstOrFail()` / `findOrFail()` di controller terkait.

### 15.4 Batas dari test ini

Test asap memverifikasi **status HTTP dan bentuk respons**, bukan angka yang benar. Untuk
memverifikasi perhitungan (subtotal, kekurangan bayar, rekap harian), bandingkan output
sebelum dan sesudah upgrade pada data staging yang sama:

```powershell
# sebelum upgrade
Invoke-WebRequest http://kemuning.test/finance -OutFile D:\snapshot\finance-sebelum.html
# sesudah upgrade
Invoke-WebRequest http://kemuning.test/finance -OutFile D:\snapshot\finance-sesudah.html
Compare-Object (Get-Content D:\snapshot\finance-sebelum.html) (Get-Content D:\snapshot\finance-sesudah.html)
```

---

## 16. Referensi

- [Laravel Release Notes & Support Policy](https://laravel.com/docs/releases#support-policy) — tabel masa dukungan tiap versi
- [Upgrade Guide 13.x](https://laravel.com/docs/13.x/upgrade) — daftar lengkap breaking change 12 → 13
- [Upgrade Guide 11.x](https://laravel.com/docs/11.x/upgrade) — pernyataan bahwa struktur lama tetap didukung
- [Upgrade Guide 10.x](https://laravel.com/docs/10.x/upgrade) — penghapusan properti `$dates`
- [Laravel 13 skeleton `bootstrap/app.php`](https://github.com/laravel/laravel/blob/13.x/bootstrap/app.php) — acuan Fase 6
- [Sanctum 4.x UPGRADE](https://github.com/laravel/sanctum/blob/4.x/UPGRADE.md) — publish migration & 3 key middleware
- [yajra/laravel-datatables di Packagist](https://packagist.org/packages/yajra/laravel-datatables-oracle) — pemetaan major per major Laravel
- [fruitcake/laravel-cors](https://packagist.org/packages/fruitcake/laravel-cors) — status abandoned
