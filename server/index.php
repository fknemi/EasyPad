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
    '/test' => ['file' => 'api/test.php', 'handler' => 'get_test']
];

$public_routes = [
];

if (strpos($uri, '/api') === 0) {
    $route = substr($uri, 4);
    
    if (isset($api_routes[$route])) {
        require_once __DIR__ . '/' . $api_routes[$route]['file'];
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
