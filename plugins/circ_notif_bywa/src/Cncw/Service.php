<?php

namespace Cncw;

use PDO;
use RuntimeException;

/**
 * Layanan notifikasi WhatsApp (sirkulasi + overdue).
 */
class Service
{
    private array $ccnw;
    private PDO $conn;
    private Notification $sender;
    private MessageBuilder $builder;

    public function __construct(array $ccnw)
    {
        $this->ccnw = $ccnw;
        $this->conn = $ccnw['conn'];
        $this->sender = new Notification($ccnw);
        $this->builder = new MessageBuilder($ccnw);
    }

    /**
     * Kirim notifikasi setelah transaksi sirkulasi sukses.
     */
    public function handleCirculationTransaction(array $data): void
    {
        if (!isset($data['loan']) && !isset($data['return']) && !isset($data['extend'])) {
            return;
        }

        $memberId = (string) ($data['memberID'] ?? '');
        if ($memberId === '') {
            return;
        }

        $member = $this->loadMember($memberId);
        if ($member === null) {
            return;
        }

        $phone = $this->normalizePhone((string) ($member['member_phone'] ?? ''));
        if ($phone === '') {
            return;
        }

        $messageId = substr(sha1((string) random_int(1, 999999) . microtime(true)), 0, 16);
        $message = $this->builder->buildCirculationMessage($data, $messageId);
        if ($message === null || $message === '') {
            return;
        }

        Log::store($this->conn, [
            'member_id' => $memberId,
            'member_name' => (string) ($data['memberName'] ?? $member['member_name'] ?? ''),
            'member_type' => (string) ($data['memberType'] ?? $member['member_type_name'] ?? ''),
            'member_phone' => $phone,
            'transaction_date' => (string) ($data['date'] ?? date('Y-m-d H:i:s')),
            'transaction_id' => $messageId,
            'message' => $message,
            'notif_type' => 'circulation',
        ]);

        try {
            $this->sender->send([
                'number' => $phone,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            // Jangan ganggu alur sirkulasi jika API WA gagal
            error_log('[circ_notif_bywa] ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi overdue untuk satu anggota.
     *
     * @return array{status:string,message:string}
     */
    public function sendOverdueNotice(string $memberId): array
    {
        $member = $this->loadMember($memberId);
        if ($member === null) {
            return ['status' => 'ERROR', 'message' => 'Anggota tidak ditemukan.'];
        }

        $phone = $this->normalizePhone((string) ($member['member_phone'] ?? ''));
        if ($phone === '') {
            return ['status' => 'ERROR', 'message' => 'Nomor WhatsApp anggota kosong / tidak valid.'];
        }

        $overdues = $this->getOverdueLoans($memberId);
        if ($overdues === []) {
            return ['status' => 'ERROR', 'message' => 'Tidak ada pinjaman terlambat untuk anggota ini.'];
        }

        require_once MDLBS . 'circulation/circulation_base_lib.inc.php';
        global $sysconf;
        $circulation = new \circulation(\SLiMS\DB::getInstance('mysqli'), $memberId);
        $circulation->ignore_holidays_fine_calc = $sysconf['ignore_holidays_fine_calc'] ?? false;
        $circulation->holiday_dayname = $_SESSION['holiday_dayname'] ?? [];
        $circulation->holiday_date = $_SESSION['holiday_date'] ?? [];

        $message = $this->builder->buildOverdueMessage(
            $member,
            $overdues,
            static function (array $overdue) use ($circulation): int {
                $result = $circulation->countOverdueValue($overdue['loan_id'], date('Y-m-d'));
                return (int) ($result['days'] ?? 0);
            }
        );

        $messageId = 'OVD-' . substr(sha1($memberId . microtime(true)), 0, 12);

        Log::store($this->conn, [
            'member_id' => $memberId,
            'member_name' => (string) ($member['member_name'] ?? ''),
            'member_type' => (string) ($member['member_type_name'] ?? ''),
            'member_phone' => $phone,
            'transaction_date' => date('Y-m-d H:i:s'),
            'transaction_id' => $messageId,
            'message' => $message,
            'notif_type' => 'overdue',
        ]);

        try {
            $this->sender->send([
                'number' => $phone,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }

        return [
            'status' => 'SENT',
            'message' => 'Notifikasi overdue WhatsApp terkirim ke ' . $phone,
        ];
    }

    /**
     * Kirim ulang dari log.
     *
     * @return array{status:string,message:string}
     */
    public function resendLog(int $logId): array
    {
        $row = Log::find($this->conn, $logId);
        if ($row === null) {
            return ['status' => 'ERROR', 'message' => 'Log tidak ditemukan.'];
        }

        try {
            $this->sender->send([
                'number' => (string) $row['member_phone'],
                'message' => (string) $row['message'],
            ]);
        } catch (RuntimeException $e) {
            return ['status' => 'ERROR', 'message' => $e->getMessage()];
        }

        return ['status' => 'SENT', 'message' => 'Pesan berhasil dikirim ulang.'];
    }

    public function loadMember(string $memberId): ?array
    {
        $sql = 'SELECT m.*, mmt.member_type_name, mmt.fine_each_day
                FROM member AS m
                LEFT JOIN mst_member_type AS mmt ON m.member_type_id = mmt.member_type_id
                WHERE m.member_id = :member_id
                LIMIT 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['member_id' => $memberId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getOverdueLoans(string $memberId): array
    {
        $sql = "SELECT l.loan_id, l.item_code, b.title, l.loan_date, l.due_date,
                       (TO_DAYS(DATE(NOW())) - TO_DAYS(l.due_date)) AS overdue_days
                FROM loan AS l
                LEFT JOIN item AS i ON l.item_code = i.item_code
                LEFT JOIN biblio AS b ON i.biblio_id = b.biblio_id
                WHERE l.is_lent = 1
                  AND l.is_return = 0
                  AND TO_DAYS(l.due_date) < TO_DAYS(CURDATE())
                  AND l.member_id = :member_id
                ORDER BY l.due_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['member_id' => $memberId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Daftar anggota yang punya pinjaman overdue + nomor WA.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listOverdueMembers(string $keyword = '', int $limit = 50, int $offset = 0): array
    {
        $params = [];
        $filter = '';
        if ($keyword !== '') {
            $filter = ' AND (m.member_id LIKE :kw OR m.member_name LIKE :kw OR m.member_phone LIKE :kw)';
            $params['kw'] = '%' . $keyword . '%';
        }

        $sql = "SELECT m.member_id, m.member_name, m.member_phone, mmt.member_type_name,
                       COUNT(l.loan_id) AS overdue_count
                FROM member AS m
                INNER JOIN loan AS l ON l.member_id = m.member_id
                    AND l.is_lent = 1 AND l.is_return = 0
                    AND TO_DAYS(l.due_date) < TO_DAYS(CURDATE())
                LEFT JOIN mst_member_type AS mmt ON m.member_type_id = mmt.member_type_id
                WHERE m.member_phone IS NOT NULL AND m.member_phone <> ''
                {$filter}
                GROUP BY m.member_id, m.member_name, m.member_phone, mmt.member_type_name
                ORDER BY m.member_name ASC
                LIMIT " . (int) $limit . ' OFFSET ' . (int) $offset;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function countOverdueMembers(string $keyword = ''): int
    {
        $params = [];
        $filter = '';
        if ($keyword !== '') {
            $filter = ' AND (m.member_id LIKE :kw OR m.member_name LIKE :kw OR m.member_phone LIKE :kw)';
            $params['kw'] = '%' . $keyword . '%';
        }

        $sql = "SELECT COUNT(DISTINCT m.member_id)
                FROM member AS m
                INNER JOIN loan AS l ON l.member_id = m.member_id
                    AND l.is_lent = 1 AND l.is_return = 0
                    AND TO_DAYS(l.due_date) < TO_DAYS(CURDATE())
                WHERE m.member_phone IS NOT NULL AND m.member_phone <> ''
                {$filter}";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Normalisasi nomor HP Indonesia ke format yang dikenali provider.
     */
    public function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone) ?? '';
        if ($phone === '' || strlen($phone) < 9) {
            return '';
        }

        return $phone;
    }
}
