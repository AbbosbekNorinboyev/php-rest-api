<?php

declare(strict_types=1);

spl_autoload_register(static function (string $className): void {
    $prefix = 'App\\';

    if (!str_starts_with($className, $prefix)) {
        return;
    }

    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, substr($className, strlen($prefix)));
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $relativePath . '.php';

    if (is_file($filePath)) {
        require $filePath;
    }
});
