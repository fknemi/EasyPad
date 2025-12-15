<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . '/utils/errors.php';
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
$api_routes = [
    '/test' => ['file' => 'api/test.php', 'handler' => 'get_test'],
    // '/ws-info' => ['file' => null, 'handler' => function() {
    //     $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    //     $hostParts = explode(':', $host);
    //     $domain = $hostParts[0];
    //     $protocol = isset($_SERVER['HTTPS']) ? 'wss' : 'ws';
    //     
    //     echo json_encode([
    //         //'websocket_url' => "{$protocol}://{$domain}:8080",
    //         'status' => 'available'
    //     ]);
    // }]
];
$public_routes = [];
if (strpos($uri, '/api') === 0) {
    $route = substr($uri, 4);
    
    if (preg_match('#^/notes$#', $route)) {
        require_once __DIR__ . '/api/notes.php';
        if ($method === 'POST') {
            create_note();
        } else {
            not_found();
        }
    } elseif (preg_match('#^/notes/([a-f0-9]+)$#', $route, $matches)) {
        require_once __DIR__ . '/api/notes.php';
        if ($method === 'GET') {
            get_note();
        } elseif ($method === 'PUT') {
            update_note();
        } elseif ($method === 'DELETE') {
            delete_note();
        } else {
            not_found();
        }
    } elseif ($route === '/share') {
        require_once __DIR__ . '/api/notes.php';
        if ($method === 'POST') {
            create_share();
        } else {
            not_found();
        }
    } elseif (preg_match('#^/share/([a-f0-9]+)$#', $route)) {
        require_once __DIR__ . '/api/notes.php';
        if ($method === 'GET') {
            get_shared_note();
        } elseif ($method === 'PUT') {
            update_shared_note();
        } else {
            not_found();
        }
    } elseif (isset($api_routes[$route])) {
        if ($api_routes[$route]['file']) {
            require_once __DIR__ . '/' . $api_routes[$route]['file'];
        }
        call_user_func($api_routes[$route]['handler']);
    } else {
        not_found();
    }
} else {
    if (isset($public_routes[$uri])) {
        require_once __DIR__ . '/' . $public_routes[$uri]['file'];
        call_user_func($public_routes[$uri]['handler']);
    } else {
        not_found();
    }
}
?>
