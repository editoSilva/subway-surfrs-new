<?php
include_once '../connection.php';

header('Content-Type: application/json');

// =====================================================
// 1) ACEITA APENAS POST
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
// 2) CAPTURA PAYLOAD BRUTO
// =====================================================
$rawPayload = file_get_contents('php://input');

// =====================================================
// 3) DECODIFICA JSON
// =====================================================
$payload = json_decode($rawPayload, true);

// =====================================================
// 4) LOGA PAYLOAD (STRING CORRETA)
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
        'message' => 'JSON inválido',
        'raw' => $rawPayload
    ]);
    exit;
}

// =====================================================
// 6) MOSTRA PAYLOAD NA TELA (DEBUG)
// =====================================================
$responseDebug = [
    'payload_recebido' => $payload
];

// =====================================================
// 7) VALIDA TIPO
// =====================================================
if (($payload['type'] ?? null) !== 'PAYIN') {
    http_response_code(400);
    $responseDebug['error'] = 'Tipo não é PAYIN';
    echo json_encode($responseDebug, JSON_PRETTY_PRINT);
    exit;
}

$externalReference = $payload['transactionId'] ?? null;
$status = $payload['status'] ?? null;

$responseDebug['externalReference'] = $externalReference;
$responseDebug['status'] = $status;

// =====================================================
// 8) PROCESSA STATUS
// =====================================================
if ($status === 'paid') {
    $conn = connect();

    // Busca depósito
    $sql = "SELECT * FROM confirmar_deposito WHERE externalreference = '{$externalReference}'";
    $result = $conn->query($sql);
    $deposito = $result ? $result->fetch_assoc() : null;

    // Mostra resultado do banco
    $responseDebug['deposito_encontrado'] = $deposito;

    if (!$deposito) {
        http_response_code(400);
        $responseDebug['error'] = 'Depósito não encontrado';
        echo json_encode($responseDebug, JSON_PRETTY_PRINT);
        exit;
    }

    if ($deposito['status'] === 'PAID_OUT') {
        http_response_code(200);
        $responseDebug['message'] = 'Depósito já confirmado';
        echo json_encode($responseDebug, JSON_PRETTY_PRINT);
        exit;
    }

    // Atualiza status
    $conn->query(
        "UPDATE confirmar_deposito 
         SET status = 'PAID_OUT' 
         WHERE externalreference = '{$externalReference}'"
    );

    $responseDebug['message'] = 'Pagamento confirmado com sucesso';

    http_response_code(200);
    echo json_encode($responseDebug, JSON_PRETTY_PRINT);
    exit;

} elseif (in_array($status, ['CANCELED', 'UNPAID'])) {

    update(
        'confirmar_deposito',
        ['status' => $status],
        ['externalreference' => $externalReference]
    );

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pagamento atualizado para ' . $status,
        'payload' => $payload
    ], JSON_PRETTY_PRINT);
    exit;
}

// =====================================================
// 9) STATUS DESCONHECIDO
// =====================================================
http_response_code(400);
echo json_encode([
    'success' => false,
    'message' => 'Status não tratado',
    'payload' => $payload
], JSON_PRETTY_PRINT);
exit;
