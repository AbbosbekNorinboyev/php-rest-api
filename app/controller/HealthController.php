<?php

declare(strict_types=1);

namespace App\Controller;

use App\Infrastructure\Database;
use App\Infrastructure\PostgresDatabase;
use PDO;
use Throwable;

// Controller: Ilova va ma'lumotlar bazasi holatini tekshiruvchi endpointlar.
final class HealthController
{
    /**
     * GET /health
     *
     * Ilova umumiy holatini tekshiradi.
     *
     * @return array<string, mixed>
     */
    public function index(): array
    {
        return [
            'status' => 'UP',
            'application' => 'php-rest-api',
        ];
    }

    /**
     * GET /health/db
     *
     * SQLite ulanishini tekshiradi.
     *
     * @return array<string, mixed>
     */
    public function db(): array
    {
        try {
            $sqlite = $this->checkConnection(
                'SQLite',
                static fn(): PDO => Database::getConnection(),
            );

            $postgres = $this->checkConnection(
                'PostgreSQL',
                static fn(): PDO => PostgresDatabase::getConnection(),
            );

            $overallStatus = $sqlite['status'] === 'UP' && $postgres['status'] === 'UP' ? 'UP' : 'DOWN';

            return [
                'status' => $overallStatus,
                'databases' => [
                    $sqlite,
                    $postgres,
                ],
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'DOWN',
                'database' => 'SQLite',
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * GET /health/info
     *
     * Ilova haqida ma'lumot.
     *
     * @return array<string, mixed>
     */
    public function info(): array
    {
        return [
            'application' => 'php-rest-api',
            'php_version' => PHP_VERSION,
            'pdo_drivers' => PDO::getAvailableDrivers(),
        ];
    }

    /**
     * @param callable(): PDO $connect
     * @return array<string, mixed>
     */
    private function checkConnection(string $name, callable $connect): array
    {
        try {
            $connection = $connect();
            $connection->query('SELECT 1');

            return [
                'status' => 'UP',
                'database' => $name,
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'DOWN',
                'database' => $name,
                'error' => $exception->getMessage(),
            ];
        }
    }
}
