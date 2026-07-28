<?php
/**
 * Contoh konfigurasi (OPSIONAL / fallback saja).
 *
 * Sejak v2.1.0, pengaturan utama diisi lewat backend:
 *   System → WA Notif Settings
 *
 * File ini tidak wajib. Buat config.php hanya jika ingin fallback
 * ketika pengaturan backend masih kosong.
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

return [
    'library_name' => '',
    'library_phone' => '',
    'footer_text' => 'Harap simpan resi ini sebagai bukti transaksi.',
    'mode' => 'default',
    'provider' => 'fonnte',
    'token' => 'YOUR_TOKEN_HERE',
    'device_id' => 'YOUR_DEVICE_ID_HERE',
    'test_phone' => '',
    'send_on_overdue_email' => true,
    'overdue_template' => "_Assalamualaikum_,\n*{member_name}* - ID Anggota : {member_id}\n\nanda memiliki Keterlambatan pinjaman :\n\n{overdue_list}\n*Mohon bisa segera dikembalikan ke perpustakaan*,\n\n_Terimakasih_\n\n_*{library_name}*_",
    'gearman_host' => '127.0.0.1',
    'gearman_port' => '4730',
    'nsq_host' => '127.0.0.1',
    'nsq_port' => '4151',
    'nsq_topic' => 'circulation',
];
