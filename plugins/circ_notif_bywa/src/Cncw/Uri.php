<?php

namespace Cncw;

/**
 * Helper query string untuk halaman log.
 */
class Uri
{
    public static function httpQuery(?string $orderBy = null, string $sort = 'ASC'): string
    {
        $data = [
            'mod' => $_GET['mod'] ?? null,
            'id' => $_GET['id'] ?? null,
            'member_id' => self::get('member_id'),
            'member_name' => self::get('member_name'),
            'member_phone' => self::get('member_phone'),
            'transaction_date' => self::get('transaction_date'),
            'orderBy' => $orderBy,
            'sort' => $sort,
        ];

        if ($orderBy !== null && isset($_GET['orderBy']) && $_GET['orderBy'] === $orderBy) {
            $current = strtoupper((string) ($_GET['sort'] ?? 'ASC'));
            $data['sort'] = $current === 'ASC' ? 'DESC' : 'ASC';
        }

        return http_build_query(array_filter($data, static fn($v) => $v !== null && $v !== ''));
    }

    public static function pageLink(): ?string
    {
        return isset($_GET['page']) && ctype_digit((string) $_GET['page'])
            ? (string) $_GET['page']
            : null;
    }

    public static function sendLink(): ?string
    {
        return isset($_GET['orderBy']) ? (string) $_GET['orderBy'] : null;
    }

    private static function get(string $key): ?string
    {
        return isset($_GET[$key]) && $_GET[$key] !== '' ? (string) $_GET[$key] : null;
    }
}
