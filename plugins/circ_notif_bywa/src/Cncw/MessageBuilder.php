<?php

namespace Cncw;

/**
 * Pembangun teks pesan WhatsApp untuk sirkulasi & overdue.
 */
class MessageBuilder
{
    private array $ccnw;

    public function __construct(array $ccnw)
    {
        $this->ccnw = $ccnw;
    }

    /**
     * Bangun pesan notifikasi transaksi sirkulasi (pinjam / kembali / perpanjang).
     */
    public function buildCirculationMessage(array $data, string $messageId): ?string
    {
        if (!isset($data['loan']) && !isset($data['return']) && !isset($data['extend'])) {
            return null;
        }

        $message = '*' . strtoupper((string) $this->ccnw['library_name']) . "*\n";
        $message .= 'No. Angg : ' . ($data['memberID'] ?? '') . "\n";
        $message .= 'Nama : ' . ($data['memberName'] ?? '') . "\n";
        $message .= 'Jn. Angg : ' . ($data['memberType'] ?? '') . "\n";
        $message .= 'Tanggal : ' . ($data['date'] ?? '') . "\n";
        $message .= 'ID : ' . $messageId . "\n";
        if (!empty($this->ccnw['library_phone'])) {
            $message .= 'Kontak : ' . $this->ccnw['library_phone'] . "\n";
        }

        if (isset($data['loan']) && is_array($data['loan'])) {
            $message .= "=====================\n";
            $message .= "*PEMINJAMAN*\n";
            $message .= "=====================\n";
            foreach ($data['loan'] as $loan) {
                $message .= '*' . ($loan['itemCode'] ?? '') . "*\n";
                $message .= '_' . ($loan['title'] ?? '') . "_\n";
                $message .= 'Tanggal pinjam: ' . $this->formatDate($loan['loanDate'] ?? '') . "\n";
                $message .= 'Batas pinjam: ' . $this->formatDate($loan['dueDate'] ?? '') . "\n";
            }
        }

        if (isset($data['return']) && is_array($data['return'])) {
            $counter = 0;
            $retmessage = "=====================\n";
            $retmessage .= "*PENGEMBALIAN*\n";
            $retmessage .= "=====================\n";
            foreach ($data['return'] as $ret) {
                if ($this->isExtendedReturn($ret, $data['extend'] ?? [])) {
                    continue;
                }
                $retmessage .= '*' . ($ret['itemCode'] ?? '') . "*\n";
                $retmessage .= '_' . ($ret['title'] ?? '') . "_\n";
                $retmessage .= 'Tanggal kembali: ' . $this->formatDate($ret['returnDate'] ?? '') . "\n";
                if (!empty($ret['overdues'])) {
                    $overdueText = is_array($ret['overdues'])
                        ? (($ret['overdues']['days'] ?? '') . ' hari')
                        : (string) $ret['overdues'];
                    $retmessage .= 'Denda: ' . $overdueText . "\n";
                }
                $counter++;
            }
            if ($counter > 0) {
                $message .= $retmessage;
            }
        }

        if (isset($data['extend']) && is_array($data['extend'])) {
            $message .= "=====================\n";
            $message .= "*PERPANJANGAN*\n";
            $message .= "=====================\n";
            foreach ($data['extend'] as $ext) {
                $message .= '*' . ($ext['itemCode'] ?? '') . "*\n";
                $message .= '_' . ($ext['title'] ?? '') . "_\n";
                $message .= 'Tanggal pinjam: ' . $this->formatDate($ext['loanDate'] ?? '') . "\n";
                $message .= 'Batas pinjam: ' . $this->formatDate($ext['dueDate'] ?? '') . "\n";
            }
        }

        $message .= "\n_____________________\n" . ($this->ccnw['footer_text'] ?? '');

        return $message;
    }

    /**
     * Bangun pesan overdue ala BSKDNold.
     *
     * @param array<int, array<string, mixed>> $overdues
     */
    public function buildOverdueMessage(array $member, array $overdues, callable $countDays): string
    {
        $list = '';
        foreach ($overdues as $overdue) {
            $days = (int) $countDays($overdue);
            $fineEachDay = (int) ($member['fine_each_day'] ?? 0);
            $fines = number_format($days * $fineEachDay, 0, ',', '.');

            $list .= 'Judul : *' . ($overdue['title'] ?? '') . "*\n";
            $list .= 'Tanggal Pinjam : ' . ($overdue['loan_date'] ?? '') . "\n";
            $list .= 'Tanggal kembali : ' . ($overdue['due_date'] ?? '') . "\n";
            $list .= 'Keterlambatan : ' . $days . " hari\n";
            $list .= 'Denda : Rp. *' . $fines . "*\n";
            $list .= "———————————————————————\n";
        }

        $template = trim((string) ($this->ccnw['overdue_template'] ?? ''));
        if ($template === '') {
            $template = "_Assalamualaikum_,\n*{member_name}* - ID Anggota : {member_id}\n\nanda memiliki Keterlambatan pinjaman :\n\n{overdue_list}\n*Mohon bisa segera dikembalikan ke perpustakaan*,\n\n_Terimakasih_\n\n_*{library_name}*_";
        }

        return strtr($template, [
            '{member_name}' => (string) ($member['member_name'] ?? ''),
            '{member_id}' => (string) ($member['member_id'] ?? ''),
            '{library_name}' => (string) ($this->ccnw['library_name'] ?? ''),
            '{overdue_list}' => $list,
        ]);
    }

    private function formatDate(string $date): string
    {
        if ($date === '' || !str_contains($date, '-')) {
            return $date;
        }
        $parts = explode('-', $date);
        if (count($parts) !== 3) {
            return $date;
        }

        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }

    private function isExtendedReturn(array $ret, array $extends): bool
    {
        foreach ($extends as $ext) {
            if (($ret['itemCode'] ?? null) === ($ext['itemCode'] ?? null)) {
                return true;
            }
        }

        return false;
    }
}
