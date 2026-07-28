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

2. Salin konfigurasi:

   ```bash
   cd plugins/circ_notif_bywa
   cp config.sample.php config.php
   ```

3. Edit `config.php`:

   | Kunci | Keterangan |
   |---|---|
   | `provider` | `fonnte` atau `whacenter` |
   | `token` | Token Fonnte (jika pakai Fonnte) |
   | `device_id` | Device ID Whacenter (jika pakai Whacenter) |
   | `library_name` | Kosongkan untuk memakai nama dari System |
   | `footer_text` | Teks footer pesan sirkulasi |
   | `send_on_overdue_email` | `true` = ikut kirim WA saat tombol overdue e-mail diklik |
   | `overdue_template` | Template pesan overdue |

4. Aktifkan plugin di admin:

   **System → Plugins → Circulation & Overdue Notification via WhatsApp**

   Saat diaktifkan, migrasi akan membuat tabel `circ_notif_wa_log`.

5. Daftarkan perangkat di provider:

   - [Fonnte](https://fonnte.com/) — login, tambah device, salin token
   - [Whacenter](https://whacenter.com/) — login, ambil device id

## Cara pakai

### A. Notifikasi sirkulasi otomatis

1. Pastikan nomor WhatsApp anggota terisi.
2. Lakukan transaksi di **Circulation** (pinjam / kembali / perpanjang).
3. Anggota menerima pesan WA berisi ringkasan transaksi.
4. Log tersimpan di **Circulation → WA Notif Log**. Dari sini bisa **Kirim ulang**.

### B. Notifikasi overdue (keterlambatan)

Ada 2 cara:

1. Menu **Membership → Overdue WA Notice**
   - Lihat daftar anggota overdue yang punya nomor WA
   - Klik **Kirim WA**

2. Otomatis saat kirim overdue e-mail
   - Dari laporan overdue, klik kirim e-mail
   - Jika `send_on_overdue_email = true`, WA ikut terkirim

Endpoint AJAX (opsional, pola BSKDNold):

```text
POST plugins/circ_notif_bywa/overdue_send.php
memberID=<member_id>
```

## Struktur folder

```text
plugins/circ_notif_bywa/
├── circ_notif_bywa.plugin.php   # registrasi menu + hook
├── bootstrap.php                # load config + DB SLiMS
├── config.sample.php            # contoh konfigurasi
├── config.php                   # konfigurasi aktif (buat sendiri)
├── autoload.php
├── index.php                    # log WA (Circulation)
├── overdue.php                  # daftar & kirim overdue WA
├── overdue_send.php             # endpoint AJAX overdue
├── migration/
│   └── 1_CreateCircNotifWaLogTable.php
└── src/Cncw/
    ├── Service.php
    ├── Notification.php
    ├── MessageBuilder.php
    ├── Log.php
    └── Uri.php
```

## Mode skala besar (opsional)

Default cocok untuk sirkulasi ringan. Untuk traffic tinggi:

- `mode = gearman` — butuh Gearman Job Server + ekstensi PHP gearman
- `mode = nsq` — butuh NSQ message broker

## Migrasi API BSKDNold → Simple-WA-Notif

| BSKDNold (lama) | Plugin ini (baru) |
|---|---|
| `curl` ke `https://app.whacenter.com/api/send` | `\Cncw\Notification` (Guzzle) seperti Simple-WA-Notif |
| `sendOverdueNoticeWA()` di `member_base_lib.inc.php.bak` | `Service::sendOverdueNotice()` + menu **Overdue WA Notice** |
| `sendMessage()` di `pop_loan_receipt.php.bak` | Hook `circulation_after_successful_transaction` |
| Hanya Whacenter | **Fonnte** + **Whacenter** (pilih di `config.php`) |

## Catatan teknis (perubahan dari sumber)

- PHP 8+: typed properties, `match`, nullsafe-friendly helpers
- Tidak lagi butuh Composer terpisah (Doctrine/Valitron/Pagination dihapus)
- Koneksi DB memakai `\SLiMS\DB` (bukan kredensial DB ganda di bootstrap)
- HTTP client memakai **Guzzle bawaan SLiMS** (API Simple-WA-Notif)
- Fitur overdue BSKDNold digabung sebagai menu plugin + hook `overduenotice_init`
- Semua kode berada di folder `plugins/` (tidak mengubah core SLiMS)

## Kredit

- Hendro Wicaksono — Simple-WA-Notif-for-Circulation
- BSKDN — implementasi overdue WhatsApp (Whacenter)
- SLiMS Community
