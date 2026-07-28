<?php

namespace Cncw;

use PDO;
use Throwable;

/**
 * Konfigurasi plugin circ_notif_bywa melalui tabel setting SLiMS.
 * Menggantikan kebutuhan mengedit config.php secara manual.
 */
class Settings
{
    public const SETTING_NAME = 'circ_notif_bywa';

    /**
     * Nilai default (setara config.sample.php).
     */
    public static function defaults(): array
    {
        return [
            'library_name' => '',
            'library_phone' => '',
            'footer_text' => 'Harap simpan resi ini sebagai bukti transaksi.',
            'mode' => 'default',
            'provider' => 'fonnte',
            'token' => '',
            'device_id' => '',
            'test_phone' => '',
            'send_on_overdue_email' => true,
            'overdue_template' => "_Assalamualaikum_,\n*{member_name}* - ID Anggota : {member_id}\n\nanda memiliki Keterlambatan pinjaman :\n\n{overdue_list}\n*Mohon bisa segera dikembalikan ke perpustakaan*,\n\n_Terimakasih_\n\n_*{library_name}*_",
            'gearman_host' => '127.0.0.1',
            'gearman_port' => '4730',
            'nsq_host' => '127.0.0.1',
            'nsq_port' => '4151',
            'nsq_topic' => 'circulation',
        ];
    }

    /**
     * Ambil konfigurasi tersimpan dari DB.
     */
    public static function load(?PDO $pdo = null): array
    {
        $defaults = self::defaults();
        $pdo = $pdo ?? \SLiMS\DB::getInstance('pdo');

        try {
            $stmt = $pdo->prepare('SELECT setting_value FROM setting WHERE setting_name = :name LIMIT 1');
            $stmt->execute(['name' => self::SETTING_NAME]);
            $raw = $stmt->fetchColumn();
            if ($raw === false || $raw === null || $raw === '') {
                return $defaults;
            }

            $value = @unserialize((string) $raw);
            if (!is_array($value)) {
                $decoded = json_decode((string) $raw, true);
                $value = is_array($decoded) ? $decoded : [];
            }

            return array_merge($defaults, $value);
        } catch (Throwable $e) {
            error_log('[circ_notif_bywa] load settings: ' . $e->getMessage());
            return $defaults;
        }
    }

    /**
     * Simpan konfigurasi ke tabel setting.
     */
    public static function save(array $input, ?PDO $pdo = null): bool
    {
        $pdo = $pdo ?? \SLiMS\DB::getInstance('pdo');
        $data = self::sanitize($input);
        $serialized = serialize($data);

        try {
            $check = $pdo->prepare('SELECT setting_id FROM setting WHERE setting_name = :name LIMIT 1');
            $check->execute(['name' => self::SETTING_NAME]);
            $exists = $check->fetchColumn();

            if ($exists) {
                $stmt = $pdo->prepare('UPDATE setting SET setting_value = :value WHERE setting_name = :name');
                return $stmt->execute([
                    'value' => $serialized,
                    'name' => self::SETTING_NAME,
                ]);
            }

            $stmt = $pdo->prepare('INSERT INTO setting (setting_name, setting_value) VALUES (:name, :value)');
            return $stmt->execute([
                'name' => self::SETTING_NAME,
                'value' => $serialized,
            ]);
        } catch (Throwable $e) {
            error_log('[circ_notif_bywa] save settings: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalisasi & sanitasi input form backend.
     */
    public static function sanitize(array $input): array
    {
        $defaults = self::defaults();
        $provider = strtolower(trim((string) ($input['provider'] ?? $defaults['provider'])));
        if (!in_array($provider, ['fonnte', 'whacenter'], true)) {
            $provider = 'fonnte';
        }

        $mode = strtolower(trim((string) ($input['mode'] ?? $defaults['mode'])));
        if (!in_array($mode, ['default', 'gearman', 'nsq'], true)) {
            $mode = 'default';
        }

        $bool = static function ($value): bool {
            if (is_bool($value)) {
                return $value;
            }
            return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
        };

        return [
            'library_name' => trim(strip_tags((string) ($input['library_name'] ?? ''))),
            'library_phone' => self::normalizePhoneInput((string) ($input['library_phone'] ?? '')),
            'footer_text' => trim((string) ($input['footer_text'] ?? $defaults['footer_text'])),
            'mode' => $mode,
            'provider' => $provider,
            'token' => trim((string) ($input['token'] ?? '')),
            'device_id' => trim((string) ($input['device_id'] ?? '')),
            'test_phone' => self::normalizePhoneInput((string) ($input['test_phone'] ?? '')),
            'send_on_overdue_email' => $bool($input['send_on_overdue_email'] ?? true),
            'overdue_template' => (string) ($input['overdue_template'] ?? $defaults['overdue_template']),
            'gearman_host' => trim(strip_tags((string) ($input['gearman_host'] ?? $defaults['gearman_host']))),
            'gearman_port' => trim(strip_tags((string) ($input['gearman_port'] ?? $defaults['gearman_port']))),
            'nsq_host' => trim(strip_tags((string) ($input['nsq_host'] ?? $defaults['nsq_host']))),
            'nsq_port' => trim(strip_tags((string) ($input['nsq_port'] ?? $defaults['nsq_port']))),
            'nsq_topic' => trim(strip_tags((string) ($input['nsq_topic'] ?? $defaults['nsq_topic']))),
        ];
    }

    public static function normalizePhoneInput(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone) ?? '';
        return $phone;
    }

    /**
     * Bangun array runtime $ccnw siap pakai Service/Notification.
     */
    public static function runtime(?PDO $pdo = null): array
    {
        $pdo = $pdo ?? \SLiMS\DB::getInstance('pdo');
        $ccnw = self::load($pdo);

        // Opsional: file config.php masih boleh dipakai sebagai fallback
        // jika field kritis masih kosong (token/device_id).
        $fileConfig = self::loadFileConfig();
        foreach (['token', 'device_id', 'library_name', 'footer_text', 'provider'] as $key) {
            $placeholder = in_array($ccnw[$key] ?? '', ['', 'YOUR_TOKEN_HERE', 'YOUR_DEVICE_ID_HERE', 'YOUR_LIBRARY_NAME_HERE'], true);
            if ($placeholder && !empty($fileConfig[$key]) && !in_array($fileConfig[$key], ['YOUR_TOKEN_HERE', 'YOUR_DEVICE_ID_HERE', 'YOUR_LIBRARY_NAME_HERE'], true)) {
                $ccnw[$key] = $fileConfig[$key];
            }
        }

        $ccnw['conn'] = $pdo;

        if ($ccnw['library_name'] === '') {
            global $sysconf;
            $ccnw['library_name'] = $sysconf['library_name'] ?? 'Perpustakaan';
        }

        $ccnw['nsq_url'] = sprintf(
            'http://%s:%s/pub?topic=%s',
            $ccnw['nsq_host'],
            $ccnw['nsq_port'],
            $ccnw['nsq_topic']
        );

        return $ccnw;
    }

    private static function loadFileConfig(): array
    {
        $configFile = dirname(__DIR__, 2) . '/config.php';
        $sampleFile = dirname(__DIR__, 2) . '/config.sample.php';
        $path = is_readable($configFile) ? $configFile : (is_readable($sampleFile) ? $sampleFile : null);
        if ($path === null) {
            return [];
        }
        $data = require $path;
        return is_array($data) ? $data : [];
    }
}
