<?php

function get_test() {
    $response = [
        'status' => 'success',
        'message' => 'Test endpoint working!',
        'data' => [
            'timestamp' => date('Y-m-d H:i:s'),
            'server' => 'PHP ' . phpversion()
        ]
    ];
    echo json_encode($response);
}

?>
