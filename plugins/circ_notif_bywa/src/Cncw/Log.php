<?php

namespace Cncw;

use PDO;

/**
 * Query & pagination log notifikasi WA.
 */
class Log
{
    private PDO $conn;
    private int $total = 0;
    /** @var array<int, array<string, mixed>> */
    private array $data = [];
    private int $page = 1;
    private int $perPage = 10;

    public function __construct(PDO $conn, array $filters = [], int $page = 1, int $perPage = 10)
    {
        $this->conn = $conn;
        $this->page = max(1, $page);
        $this->perPage = max(1, min(100, $perPage));
        $this->fetch($filters);
    }

    private function fetch(array $filters): void
    {
        $where = [];
        $params = [];

        foreach (['member_id', 'member_name', 'member_phone', 'transaction_date'] as $field) {
            if (!empty($filters[$field])) {
                $where[] = $field . ' LIKE :' . $field;
                $params[$field] = '%' . $filters[$field] . '%';
            }
        }

        $sqlWhere = $where ? (' WHERE ' . implode(' AND ', $where)) : '';

        $countStmt = $this->conn->prepare('SELECT COUNT(*) FROM circ_notif_wa_log' . $sqlWhere);
        $countStmt->execute($params);
        $this->total = (int) $countStmt->fetchColumn();

        $orderBy = $this->sanitizeOrderBy($filters['orderBy'] ?? 'id');
        $sort = strtoupper((string) ($filters['sort'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';

        $offset = ($this->page - 1) * $this->perPage;
        $sql = 'SELECT * FROM circ_notif_wa_log' . $sqlWhere
            . ' ORDER BY ' . $orderBy . ' ' . $sort
            . ' LIMIT ' . (int) $this->perPage . ' OFFSET ' . (int) $offset;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $this->data = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function sanitizeOrderBy(string $orderBy): string
    {
        $allowed = [
            'id', 'member_id', 'member_name', 'member_type',
            'member_phone', 'transaction_date', 'transaction_id', 'created_at',
        ];

        return in_array($orderBy, $allowed, true) ? $orderBy : 'id';
    }

    /**
     * Simpan log transaksi.
     */
    public static function store(PDO $conn, array $row): void
    {
        $sql = 'INSERT INTO circ_notif_wa_log
            (member_id, member_name, member_type, member_phone, transaction_date, transaction_id, message, created_at, notif_type)
            VALUES
            (:member_id, :member_name, :member_type, :member_phone, :transaction_date, :transaction_id, :message, :created_at, :notif_type)';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'member_id' => $row['member_id'],
            'member_name' => $row['member_name'],
            'member_type' => $row['member_type'],
            'member_phone' => $row['member_phone'],
            'transaction_date' => $row['transaction_date'],
            'transaction_id' => $row['transaction_id'],
            'message' => $row['message'],
            'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
            'notif_type' => $row['notif_type'] ?? 'circulation',
        ]);
    }

    public static function find(PDO $conn, int $id): ?array
    {
        $stmt = $conn->prepare('SELECT * FROM circ_notif_wa_log WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    /** @return array<int, array<string, mixed>> */
    public function getData(): array
    {
        return $this->data;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotalPages(): int
    {
        return (int) max(1, (int) ceil($this->total / $this->perPage));
    }
}
