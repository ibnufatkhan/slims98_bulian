# Landing Page Rating

Plugin SLiMS untuk menampilkan form rating di footer **landing page**.

## Fitur

- Pengunjung dapat mengirim **nama**, **komentar**, dan **rating bintang (1–5)**
- Menampilkan rata-rata rating, distribusi bintang, dan daftar ulasan
- Admin dapat **menyembunyikan** atau **menghapus** ulasan
- Proteksi CSRF + batasan flood (maks. 3 ulasan / IP / jam)

## Instalasi

1. Pastikan folder plugin ada di `plugins/landing_rating/`
2. Login sebagai Administrator
3. Buka **System → Plugins**
4. Aktifkan plugin **Landing Page Rating** (migrasi tabel akan otomatis dijalankan)

## Penggunaan

### OPAC (Landing Page)
Widget rating muncul otomatis di atas footer halaman utama (tanpa parameter `?p=` / pencarian).

### Admin
Menu: **System → Landing Page Rating**

- Filter: Semua / Tampil / Tersembunyi
- Tombol **Sembunyikan** / **Tampilkan** per ulasan
- Centang + hapus untuk menghapus ulasan

## Struktur

```
landing_rating/
├── landing_rating.plugin.php
├── helper.php
├── footer_widget.php
├── opac.php
├── admin.php
├── assets/
│   ├── landing_rating.css
│   └── landing_rating.js
└── migration/
    └── 1_CreateLandingRatingTable.php
```

## Catatan Template

Hook `opac_footer` dipanggil dari:

- `template/default/parts/footer.php`
- `template/lightweight/partials/footer.php`
