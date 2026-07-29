# Visitor Stats Footer

Plugin SLiMS untuk menampilkan **jumlah pengunjung 12 bulan terakhir** di sisi kanan footer OPAC.

Sumber data: tabel `visitor_count` (log pengunjung perpustakaan SLiMS).

## Instalasi

1. Pastikan folder ada di:

   ```text
   <slims-root>/plugins/visitor_stats/
   ```

2. Aktifkan di **System → Plugins → Visitor Stats Footer**
3. Hard refresh OPAC (`Ctrl+F5`)

## Tampilan

Di baris copyright footer:

- **Kiri:** teks copyright template
- **Kanan:** `Pengunjung 12 bulan terakhir` + angka (sebelum ikon sosial media)

## Catatan

- Tidak mengubah file template (inject otomatis via hook)
- Jika tabel `visitor_count` kosong/belum ada, angka menampilkan `0`
