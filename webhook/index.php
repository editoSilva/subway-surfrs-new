<?php
include_once '../connection.php';

header('Content-Type: application/json');

// =====================================================
// 1) SE FOR GET → MOSTRA NA TELA
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Webhook ativo',
        'expected_method' => 'POST',
        'time' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    exit;
}

// =====================================================
// 2) SE NÃO FOR POST → BLOQUEIA
// =====================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// =====================================================
// 3) CAPTURA PAYLOAD
// =====================================================
$rawPayload = file_get_contents('php://input');
$payload = json_decode($rawPayload, true);

// =====================================================
// 4) LOG DO PAYLOAD
// =====================================================
file_put_contents(
    __DIR__ . '/log.txt',
    date('Y-m-d H:i:s') . PHP_EOL .
    $rawPayload . PHP_EOL . PHP_EOL,
    FILE_APPEND
);

// =====================================================
// 5) JSON INVÁLIDO
// =====================================================
if (is_null($payload)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON'
    ]);
    exit;
}

// =====================================================
// 6) RETORNA O QUE CHEGOU (DEBUG)
// =====================================================
http_response_code(200);
echo json_encode([
    'success' => true,
    'received' => $payload
], JSON_PRETTY_PRINT);
exit;
