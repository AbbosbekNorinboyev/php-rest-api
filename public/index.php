<?php

require __DIR__ . '/../src/Database.php';
require __DIR__ . '/../src/Response.php';
require __DIR__ . '/../src/TaskController.php';

// CORS (test uchun ochiq, productionda domenlarni cheklang)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');

// index.php script yo'lini olib tashlab, faqat /api/... qismini qoldiramiz
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($scriptDir !== '' && str_starts_with($uri, $scriptDir)) {
    $uri = substr($uri, strlen($scriptDir));
}

$segments = explode('/', trim($uri, '/'));

// Kutilgan format: /api/tasks yoki /api/tasks/{id}
if (($segments[0] ?? '') !== 'api' || ($segments[1] ?? '') !== 'tasks') {
    Response::json(['error' => 'Endpoint topilmadi'], 404);
    exit;
}

$id = isset($segments[2]) && $segments[2] !== '' ? (int) $segments[2] : null;

$db = Database::getConnection();
$controller = new TaskController($db);

$input = [];
if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true) ?? [];
}

try {
    match (true) {
        $method === 'GET' && $id === null => $controller->index(),
        $method === 'GET' && $id !== null => $controller->show($id),
        $method === 'POST' && $id === null => $controller->store($input),
        in_array($method, ['PUT', 'PATCH'], true) && $id !== null => $controller->update($id, $input),
        $method === 'DELETE' && $id !== null => $controller->destroy($id),
        default => Response::json(['error' => 'Noto\'g\'ri so\'rov'], 405),
    };
} catch (Throwable $e) {
    Response::json(['error' => 'Server xatosi: ' . $e->getMessage()], 500);
}
