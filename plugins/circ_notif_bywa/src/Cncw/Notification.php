<?php

namespace Cncw;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * Pengirim notifikasi WhatsApp — API dari Simple-WA-Notif-for-Circulation.
 *
 * Menggantikan skrip BSKDNold yang memakai curl mentah ke Whacenter:
 *   - admin/modules/membership/member_base_lib.inc.php.bak :: sendOverdueNoticeWA()
 *   - admin/modules/circulation/pop_loan_receipt.php.bak :: sendMessage()
 *
 * Provider: fonnte | whacenter (GuzzleHttp, sama seperti Simple-WA-Notif).
 */
class Notification
{
    private array $ccnw;

    public function __construct(array $ccnw)
    {
        $this->ccnw = $ccnw;
    }

    /**
     * Entry point pengganti sendMessage()/curl BSKDNold.
     *
     * @param array{number:string,message:string,device_id?:string} $data
     */
    public function send(array $data): bool
    {
        if (empty($data['device_id'])) {
            $data['device_id'] = (string) ($this->ccnw['device_id'] ?? '');
        }

        $mode = $this->ccnw['mode'] ?? 'default';

        return match ($mode) {
            'default' => $this->sendDefault($data),
            'gearman' => $this->sendViaGearman($data),
            'nsq' => $this->sendViaNsq($data),
            default => throw new RuntimeException('Mode pengiriman tidak dikenali: ' . $mode),
        };
    }

    private function sendDefault(array $data): bool
    {
        $provider = strtolower((string) ($this->ccnw['provider'] ?? 'fonnte'));

        return match ($provider) {
            'whacenter' => $this->sendToWhacenter($data),
            'fonnte' => $this->sendToFonnte($data),
            default => throw new RuntimeException('Provider tidak dikenali: ' . $provider),
        };
    }

    /**
     * API Whacenter — sama dengan Simple-WA-Notif-for-Circulation.
     * (Pengganti curl https://app.whacenter.com/api/send di BSKDNold)
     */
    public function sendToWhacenter(array $data): bool
    {
        $payload = [
            'device_id' => $data['device_id'] ?? ($this->ccnw['device_id'] ?? ''),
            'number' => $data['number'] ?? '',
            'message' => $data['message'] ?? '',
        ];

        $client = new Client(['timeout' => 15]);
        $response = $client->request('POST', 'https://app.whacenter.com/api/send', [
            'form_params' => $payload,
        ]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    /**
     * API Fonnte — sama dengan Simple-WA-Notif-for-Circulation.
     */
    public function sendToFonnte(array $data): bool
    {
        $payload = $data;
        $payload['target'] = $data['number'] ?? '';
        $payload['countryCode'] = $data['countryCode'] ?? '62';
        unset($payload['number'], $payload['device_id']);

        $client = new Client(['timeout' => 15]);
        $response = $client->request('POST', 'https://api.fonnte.com/send', [
            'headers' => [
                'Authorization' => (string) ($this->ccnw['token'] ?? ''),
            ],
            'form_params' => $payload,
        ]);

        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    private function sendViaGearman(array $data): bool
    {
        if (!class_exists('GearmanClient')) {
            throw new RuntimeException('Ekstensi Gearman belum terpasang.');
        }

        $client = new \GearmanClient();
        $client->addServer($this->ccnw['gearman_host'], (int) $this->ccnw['gearman_port']);
        $client->doNormal('send_notif_wa', urlencode(serialize($data)));

        return true;
    }

    private function sendViaNsq(array $data): bool
    {
        try {
            $client = new Client(['timeout' => 15]);
            $response = $client->request('POST', $this->ccnw['nsq_url'], [
                'body' => urlencode(serialize($data)),
            ]);

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (GuzzleException $e) {
            throw new RuntimeException('Gagal mengirim via NSQ: ' . $e->getMessage(), 0, $e);
        }
    }
}
