<?php

include_once '../connection.php';

/**
 * WEBHOOK PIX - RECEBE E RETORNA O PAYLOAD
 * Compatível para debug e produção
 */

header('Content-Type: application/json');

// ==================================================
// 1) PERMITE APENAS POST
// ==================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// ==================================================
// 2) CAPTURA PAYLOAD BRUTO
// ==================================================
$rawPayload = file_get_contents('php://input');

// ==================================================
// 3) DECODIFICA JSON
// ==================================================
$payload = json_decode($rawPayload, true);

// ==================================================
// 4) LOGA TUDO QUE CHEGOU (DEBUG)
// ==================================================
file_put_contents(
    __DIR__ . '/webhook.log',
    "==============================" . PHP_EOL .
    date('Y-m-d H:i:s') . PHP_EOL .
    $rawPayload . PHP_EOL .
    "==============================" . PHP_EOL . PHP_EOL,
    FILE_APPEND
);

// ==================================================
// 5) JSON INVÁLIDO
// ==================================================
if (is_null($payload)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON'
    ]);
    exit;
}

// ==================================================
// 6) (OPCIONAL) EXTRAÇÃO DE CAMPOS DO PIX
//     — deixe comentado se quiser só retornar
// ==================================================
/*
$typeTransaction   = $payload['typeTransaction'] ?? null;
$statusTransaction = $payload['statusTransaction'] ?? null;
$idTransaction     = $payload['idTransaction'] ?? null;
*/

// ==================================================
// 7) RETORNA EXATAMENTE O QUE VEIO NO POST
// ==================================================
http_response_code(200);
echo json_encode([
    'success' => true,
    'received_at' => date('Y-m-d H:i:s'),
    'payload' => $payload
]);
exit;