<?php
/**
 * Salin file ini menjadi config.php lalu sesuaikan nilainya.
 *
 * cp config.sample.php config.php
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

return [
    // Nama perpustakaan (kosongkan untuk memakai $sysconf['library_name'])
    'library_name' => '',

    // Teks footer pada pesan WhatsApp
    'footer_text' => 'Harap simpan resi ini sebagai bukti transaksi.',

    // Mode kirim: default | gearman | nsq
    'mode' => 'default',

    // Provider API (Simple-WA-Notif): 'fonnte' | 'whacenter'
    // BSKDNold sebelumnya hanya curl Whacenter; sekarang diganti API ini.
    'provider' => 'fonnte',

    // Token Fonnte (wajib jika provider = fonnte) — https://fonnte.com/
    'token' => 'YOUR_TOKEN_HERE',

    // Device ID Whacenter (wajib jika provider = whacenter) — https://whacenter.com/
    // Pengganti device_id kosong di curl BSKDNold.
    'device_id' => 'YOUR_DEVICE_ID_HERE',

    // Kirim juga notifikasi WA saat tombol overdue e-mail diklik
    'send_on_overdue_email' => true,

    // Template pesan overdue (placeholder: {member_name}, {member_id}, {library_name}, {overdue_list})
    'overdue_template' => "_Assalamualaikum_,\n*{member_name}* - ID Anggota : {member_id}\n\nanda memiliki Keterlambatan pinjaman :\n\n{overdue_list}\n*Mohon bisa segera dikembalikan ke perpustakaan*,\n\n_Terimakasih_\n\n_*{library_name}*_",

    // Gearman (opsional, mode = gearman)
    'gearman_host' => '127.0.0.1',
    'gearman_port' => '4730',

    // NSQ (opsional, mode = nsq)
    'nsq_host' => '127.0.0.1',
    'nsq_port' => '4151',
    'nsq_topic' => 'circulation',
];
