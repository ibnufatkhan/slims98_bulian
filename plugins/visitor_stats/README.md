# Visitor Stats Footer

Plugin SLiMS untuk menampilkan **jumlah pengunjung web OPAC sepanjang masa** di sisi kanan footer.

## Yang dihitung
- Pengunjung **website/OPAC** (bukan pengunjung fisik `visitor_count`)
- Dihitung **1x per sesi browser** agar refresh halaman tidak menambah angka
- Total diakumulasi dari awal plugin aktif sampai hari ini

## Instalasi

1. Upload folder ke:

   ```text
   <slims-root>/plugins/visitor_stats/
   ```

2. Aktifkan di **System → Plugins → Visitor Stats Footer**
3. Hard refresh OPAC (`Ctrl+F5`)

## Tampilan footer bawah

| Kiri | Tengah | Kanan |
|------|--------|-------|
| © Template by SLiMS Community 2026 | Ikon sosial media | Pengunjung web + angka |

## Catatan
- Tabel `plugin_web_visitor_stats` dibuat otomatis saat plugin jalan / migrasi aktif
- Nonaktifkan lalu aktifkan ulang plugin jika ingin menjalankan migrasi formal
