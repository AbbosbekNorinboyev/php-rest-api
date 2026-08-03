<?php

declare(strict_types=1);

use App\Controller\TaskController;
use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Infrastructure\Database;
use App\Repository\TaskRepository;
use App\Service\TaskService;

require dirname(__DIR__) . '/bootstrap.php';

header('Access-Control-Allow-Origin: http://localhost:8000');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $path = rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    $segments = explode('/', trim($path, '/'));

    if (($segments[0] ?? null) !== 'api' || ($segments[1] ?? null) !== 'tasks' || count($segments) > 3) {
        throw new HttpException('Endpoint topilmadi', 404);
    }

    $id = null;
    if (isset($segments[2])) {
        if (!ctype_digit($segments[2]) || (int) $segments[2] < 1) {
            throw new HttpException('Task ID musbat butun son bo\'lishi kerak', 400);
        }

        $id = (int) $segments[2];
    }

    $input = [];
    if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
        $rawBody = file_get_contents('php://input');
        if ($rawBody === false || trim($rawBody) === '') {
            throw new HttpException('JSON so\'rov tanasi majburiy', 400);
        }

        $input = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($input) || array_is_list($input)) {
            throw new HttpException('JSON obyekt bo\'lishi kerak', 400);
        }
    }

    $controller = new TaskController(new TaskService(new TaskRepository(Database::getConnection())));

    [$response, $statusCode] = match (true) {
        $method === 'GET' && $id === null => [$controller->index(), 200],
        $method === 'GET' => [$controller->show($id), 200],
        $method === 'POST' && $id === null => [$controller->store($input), 201],
        in_array($method, ['PUT', 'PATCH'], true) && $id !== null => [$controller->update($id, $input), 200],
        $method === 'DELETE' && $id !== null => [$controller->destroy($id), 200],
        default => throw new HttpException('Noto\'g\'ri so\'rov', 405),
    };

    JsonResponse::send($response, $statusCode);
} catch (HttpException $exception) {
    JsonResponse::send(['error' => $exception->getMessage()], $exception->statusCode);
} catch (JsonException) {
    JsonResponse::send(['error' => 'JSON formati noto\'g\'ri'], 400);
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    JsonResponse::send(['error' => 'Ichki server xatosi'], 500);
}
