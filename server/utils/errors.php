```php
<?php

function not_found() {
    http_response_code(404);
    echo json_encode([
        'status' => 'error',
        'message' => 'Endpoint not found'
    ]);
}

function method_not_allowed() {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}

function bad_request($message = 'Bad request') {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

function unauthorized($message = 'Unauthorized') {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

function forbidden($message = 'Forbidden') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

function conflict($message = 'Conflict') {
    http_response_code(409);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

function unprocessable_entity($message = 'Unprocessable entity', $errors = []) {
    http_response_code(422);
    $response = [
        'status' => 'error',
        'message' => $message
    ];
    
    if (!empty($errors)) {
        $response['errors'] = $errors;
    }
    
    echo json_encode($response);
}

function too_many_requests($message = 'Too many requests') {
    http_response_code(429);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

function internal_server_error($message = 'Internal server error') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

function service_unavailable($message = 'Service unavailable') {
    http_response_code(503);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

function custom_error($code, $message) {
    http_response_code($code);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
}

?>
```
