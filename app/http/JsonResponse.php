<?php

declare(strict_types=1);

namespace App\Http;

// HTTP: barcha API javoblarini JSON formatida qaytaradi.
final class JsonResponse
{
    /** @param array<string, mixed> $data */
    public static function send(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
