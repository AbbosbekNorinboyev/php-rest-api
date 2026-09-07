<?php

declare(strict_types=1);

namespace App\Infrastructure;

use PDO;

// Infrastructure: SQLite ulanishi va boshlang'ich sxemani boshqaradi.
final class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $databasePath = dirname(__DIR__, 2) . '/database/database.sqlite';
        self::$connection = new PDO('sqlite:' . $databasePath);
        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        self::$connection->exec('PRAGMA foreign_keys = ON');

        $schemaPath = dirname(__DIR__, 2) . '/database/schema.sql';
        self::$connection->exec((string) file_get_contents($schemaPath));

        return self::$connection;
    }
}
