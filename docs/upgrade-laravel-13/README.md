# Dokumen Upgrade Laravel — Muning Photo (`kemuning`)

Folder ini berisi hasil analisis dan rencana upgrade aplikasi dari **Laravel 9.52.16** ke
**Laravel 13.x**. Dokumen ditulis untuk dikerjakan bertahap oleh satu developer, dengan
setiap fase punya kriteria selesai dan cara rollback sendiri.

| Berkas | Isi | Kapan dibaca |
|---|---|---|
| [01-RENCANA-UPGRADE.md](01-RENCANA-UPGRADE.md) | Rencana lengkap: strategi, 7 fase, matriks dependensi, risiko, rollback, linimasa | Sebelum mulai; jadi acuan utama |
| [02-ANALISIS-PROYEK.md](02-ANALISIS-PROYEK.md) | Audit kode: profil teknis, peta rute, 12 temuan dengan bukti path:baris | Saat ingin tahu **mengapa** sesuatu harus dikerjakan |
| [03-CHECKLIST-EKSEKUSI.md](03-CHECKLIST-EKSEKUSI.md) | Lembar kerja: checkbox per fase, blok `composer.json` siap tempel, tabel verifikasi 22 halaman | Setiap hari saat mengeksekusi |

## Ringkasan satu paragraf

Aplikasi ini kecil (11 controller, 4 model, 29 rute, 1.528 baris PHP di `app/`) sehingga
upgrade-nya tidak rumit secara teknis. Yang membuatnya berisiko adalah kondisi sekitarnya: **belum ada
baseline Git** (363 perubahan belum di-commit), **migrasi tidak sinkron dengan database**
(kolom `deleted_at` dipakai tetapi tidak pernah dibuat oleh migrasi mana pun), dan **tidak ada
test**. Karena itu rencana ini menempatkan dua fase persiapan di depan — commit baseline dan
penutupan drift skema — sebelum satu pun dependensi dinaikkan.

## Angka kunci

| | |
|---|---|
| Versi sekarang | Laravel 9.52.16 (EOL sejak Februari 2024) |
| Target | Laravel 13.x (PHP 8.3–8.5, didukung sampai Maret 2028) |
| PHP terpasang | 8.3.36 — sudah memenuhi syarat Laravel 13 |
| Metode | Satu branch, empat commit bertahap (10 → 11 → 12 → 13) |
| Estimasi | 5,5 hari kerja + buffer 30% ≈ **7 hari kerja** (±1,5–2 minggu kalender) |
| Paket yang dihapus | `fruitcake/laravel-cors`, `spatie/laravel-ignition`, `laravel/sail`, `yajra/laravel-datatables` (meta), `realrashid/sweet-alert`, `barryvdh/laravel-dompdf`, `apalfrey/select2-bootstrap-5-theme` |

## Tiga keputusan yang perlu disetujui sebelum mulai

1. **Struktur lama dipertahankan sampai Laravel 13 berjalan**, baru dimigrasikan ke
   `bootstrap/app.php` sebagai refactor terpisah (Fase 6). Alasannya ada di rencana §3.2.
2. **Paket yang tidak dipakai kode dihapus**, bukan dinaikkan versinya — termasuk
   `barryvdh/laravel-dompdf` (ekspor PDF masih dikomentari) dan `realrashid/sweet-alert`
   (tidak ada pemanggilan `Alert::` di seluruh kode).
3. **Autentikasi adalah keputusan produk, bukan teknis.** Saat ini `/admin`, `/finance`, dan
   `/transaction` terbuka tanpa login. Rencana ini menyediakan resep pemasangannya (Fase 6),
   tetapi tidak memasukkannya ke jalur upgrade agar diff tetap terbaca.

## Cara mulai

```powershell
# 1. baca ringkasan
Get-Content docs\upgrade-laravel-13\01-RENCANA-UPGRADE.md -TotalCount 60

# 2. siapkan baseline (Fase 0)
git add -A
git commit -m "chore: baseline kondisi produksi sebelum upgrade Laravel"
git tag baseline-l9
git checkout -b upgrade/laravel-13

# 3. ikuti checklist
Get-Content docs\upgrade-laravel-13\03-CHECKLIST-EKSEKUSI.md
```

## Batasan dokumen ini

- Skema database produksi **belum diverifikasi langsung**: server MySQL tidak berjalan saat
  audit, sehingga temuan drift (T-02) disimpulkan dari kode, bukan dari `SHOW CREATE TABLE`.
  Verifikasi ini adalah langkah pertama Fase 1.
- Versi paket pihak ketiga diverifikasi dari `composer.json` masing-masing paket dan blok
  "Requires" di Packagist pada 30 Oktober 2026; tetap jalankan `composer update` dan biarkan
  Composer menyelesaikan constraint-nya.
- Dokumen ini tidak mengubah satu baris kode aplikasi. Semua eksekusi dilakukan setelah
  rencana disetujui.
