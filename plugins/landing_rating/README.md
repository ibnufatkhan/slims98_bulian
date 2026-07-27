# Landing Page Rating

Plugin SLiMS untuk menampilkan form rating di footer **landing page**.

## Struktur folder (penting)

Pastikan file diletakkan seperti ini:

```
plugins/
└── landing_rating/
    ├── landing_rating.plugin.php   ← wajib di sini
    ├── admin.php
    ├── opac.php
    ├── helper.php
    ├── footer_widget.php
    ├── assets/
    │   ├── landing_rating.css
    │   └── landing_rating.js
    └── migration/
        └── 1_CreateLandingRatingTable.php
```

**Salah** jika menjadi:
- `plugins/landing_rating/landing_rating/landing_rating.plugin.php` (folder dobel)
- `plugins/landing_rating.plugin.php` (tanpa subfolder isi lain)

## Fitur

- Pengunjung dapat mengirim **nama**, **komentar**, dan **rating bintang (1–5)**
- Menampilkan rata-rata rating, distribusi bintang, dan daftar ulasan
- Admin dapat **menyembunyikan** atau **menghapus** ulasan
- Proteksi CSRF + batasan flood (maks. 3 ulasan / IP / jam)
- **Tidak wajib** mengubah file template (v1.0.1+ inject otomatis)
- Tema terang agar menyatu dengan halaman OPAC (v1.0.2)

## Instalasi

1. Upload folder `landing_rating` ke `<slims>/plugins/`
2. Login Administrator
3. Buka **System → Plugins**
4. Aktifkan **Landing Page Rating**
5. Hard refresh beranda OPAC (`Ctrl+F5`)

## Cek cepat

- Menu admin muncul di **System → Landing Page Rating** → plugin aktif
- Buka beranda (URL tanpa `?p=`), scroll ke bawah sebelum footer → widget ulasan
- Jika kosong total, pastikan tabel migrasi ada (nonaktifkan lalu aktifkan ulang plugin)

## Endpoint

- Submit/list: `index.php?p=landing_rating`
- Ambil token: `index.php?p=landing_rating&action=token`

## Tidak menyentuh file inti

Plugin ini **tidak** mengubah file di luar `plugins/landing_rating/`.
Termasuk `template/default/parts/footer.php` dan
`template/lightweight/partials/footer.php` yang tetap asli, sehingga aman
saat SLiMS diperbarui dan tidak bentrok dengan plugin lain.

Bagi pembuat template kustom, hook `opac_footer` tetap tersedia bila ingin
menempatkan widget secara manual.
