# Star Rating Plugin

Plugin rating & ulasan untuk footer landing page OPAC SLiMS.

## Fitur

- Pengunjung dapat mengirim **nama**, **komentar**, dan **rating bintang (1–5)** tanpa login.
- Ringkasan peringkat rata-rata + breakdown per bintang.
- Daftar ulasan tampil di footer landing page.
- Admin dapat **menyembunyikan** atau **menghapus** ulasan melalui menu **System → Rating & Ulasan**.

## Instalasi

1. Pastikan folder `plugins/star_rating` sudah ada.
2. Aktifkan plugin di **System → Plugins** dengan nama **Star Rating**.
3. Migrasi tabel `plugin_star_rating` akan dijalankan otomatis saat aktivasi.

## Catatan

Template OPAC (default & lightweight) memanggil hook `opac_footer` agar widget dapat dirender di footer.
