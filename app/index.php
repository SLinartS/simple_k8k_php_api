<?php
// Simple PHP API without frameworks

header('Content-Type: application/json');

$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Simple routing
switch (true) {
    case $requestUri === '/health' || $requestUri === '/health/':
        echo json_encode([
            'status' => 'healthy',
            'timestamp' => date('c')
        ]);
        break;
        
    case $requestUri === '/' || $requestUri === '':
        echo json_encode([
            'message' => 'Welcome to Simple PHP API',
            'endpoints' => [
                'GET /' => 'API info',
                'GET /health' => 'Health check',
                'GET /hello' => 'Greeting endpoint',
                'GET /hello/{name}' => 'Personalized greeting'
            ]
        ]);
        break;
        
    case preg_match('#^/hello/([^/]+)$#', $requestUri, $matches):
        $name = $matches[1];
        echo json_encode([
            'message' => "Hello, $name!",
            'timestamp' => date('c')
        ]);
        break;
        
    case $requestUri === '/hello' || $requestUri === '/hello/':
        echo json_encode([
            'message' => 'Hello, World!',
            'timestamp' => date('c')
        ]);
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Not Found',
            'path' => $requestUri
        ]);
        break;
}
