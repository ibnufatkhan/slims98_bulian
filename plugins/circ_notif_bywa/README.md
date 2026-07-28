# Circulation & Overdue Notification via WhatsApp (PHP 8)

Plugin SLiMS 9/98 Bulian untuk mengirim notifikasi WhatsApp pada:

1. **Transaksi sirkulasi** (peminjaman, pengembalian, perpanjangan) — dari [Simple-WA-Notif-for-Circulation](https://github.com/hendrowicaksono/Simple-WA-Notif-for-Circulation)
2. **Keterlambatan (overdue)** — dari fitur kustom BSKDNold (`sendOverdueNoticeWA` / `overdue_wa.php`)

Provider yang didukung: **Fonnte** dan **Whacenter**.

## Persyaratan

- SLiMS Bulian dengan **PHP >= 8.1**
- Ekstensi PHP `curl` (dipakai Guzzle bawaan SLiMS)
- Nomor WhatsApp anggota terisi di field **Phone Number**

## Instalasi

1. Pastikan folder plugin ada di:

   ```text
   <slims-root>/plugins/circ_notif_bywa/
   ```

2. Aktifkan plugin di admin:

   **System → Plugins → Circulation & Overdue Notification via WhatsApp**

   Saat diaktifkan, migrasi akan membuat tabel `circ_notif_wa_log`.

3. Atur konfigurasi lewat backend (**tanpa edit config.php**):

   **System → WA Notif Settings**

   Isi:
   - Provider (`fonnte` / `whacenter`)
   - Token Fonnte **atau** Device ID Whacenter
   - Nama perpustakaan (opsional)
   - Nomor handphone perpustakaan
   - Nomor handphone untuk uji kirim
   - Footer & template overdue

4. (Opsional) Klik **Kirim Uji WA** di halaman yang sama untuk memastikan token/device aktif.

5. Daftarkan perangkat di provider:

   - [Fonnte](https://fonnte.com/) — login, tambah device, salin token
   - [Whacenter](https://whacenter.com/) — login, ambil device id

> Catatan: file `config.php` / `config.sample.php` masih tersedia sebagai fallback opsional,
> tetapi prioritas utama adalah pengaturan yang disimpan dari **System → WA Notif Settings**.

## Cara pakai

### A. Notifikasi sirkulasi otomatis

1. Pastikan nomor WhatsApp anggota terisi.
2. Lakukan transaksi di **Circulation** (pinjam / kembali / perpanjang).
3. Anggota menerima pesan WA berisi ringkasan transaksi.
4. Log tersimpan di **Circulation → WA Notif Log**. Dari sini bisa **Kirim ulang**.

### B. Notifikasi overdue (keterlambatan)

1. Menu **Membership → Overdue WA Notice** → klik **Kirim WA**
2. Atau otomatis saat kirim overdue e-mail (jika opsi di settings aktif)

## Struktur folder

```text
plugins/circ_notif_bywa/
├── circ_notif_bywa.plugin.php
├── bootstrap.php
├── settings.php                 # System → WA Notif Settings
├── config.sample.php            # fallback opsional
├── index.php
├── overdue.php
├── overdue_send.php
├── migration/
└── src/Cncw/
    ├── Settings.php
    ├── Service.php
    ├── Notification.php
    ├── MessageBuilder.php
    ├── Log.php
    └── Uri.php
```

## Migrasi API BSKDNold → Simple-WA-Notif

| BSKDNold (lama) | Plugin ini (baru) |
|---|---|
| `curl` Whacenter | `\Cncw\Notification` (Guzzle) |
| Hardcode di PHP | **System → WA Notif Settings** |
| Hanya Whacenter | Fonnte + Whacenter |

## Kredit

- Hendro Wicaksono — Simple-WA-Notif-for-Circulation
- BSKDN — implementasi overdue WhatsApp
- SLiMS Community
