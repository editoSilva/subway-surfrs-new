<?php
include_once '../connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(200);
    echo json_encode(['oksss' => $_SERVER['REQUEST_METHOD'] ]);
    exit;
}

// ===================================================
// 1) PAYLOAD
// ===================================================
$rawPayload = file_get_contents('php://input');
$payload = json_decode($rawPayload, true);



// Log correto
file_put_contents(
    __DIR__ . '/log.txt',
    date('Y-m-d H:i:s') . PHP_EOL . $rawPayload . PHP_EOL . PHP_EOL,
    FILE_APPEND
);

if (!$payload || ($payload['type'] ?? null) !== 'PAYIN') {
    http_response_code(200);
    echo json_encode(['ok PAYIN' => true]);
    exit;
}

$externalReference = $payload['orderId'] ?? null;
$status = $payload['status'] ?? null;

if (!$externalReference || !$status) {
    http_response_code(200);
    echo json_encode(['ok ddd' => true]);
    exit;
}

// ===================================================
// 2) STATUS NÃO CONFIRMADO
// ===================================================
if ($status !== 'paid') {
    update(
        'confirmar_deposito',
        ['status' => strtoupper($status)],
        ['externalreference' => $externalReference]
    );
    http_response_code(200);
    echo json_encode(['ok paid' => true]);
    exit;
}

// ===================================================
// 3) CONFIRMAÇÃO DO PAGAMENTO
// ===================================================
$conn = connect();
$conn->begin_transaction();

// Busca depósito
$sql = "SELECT * FROM confirmar_deposito WHERE externalreference = '{$externalReference}' FOR UPDATE";
$res = $conn->query($sql);
$deposito = $res ? $res->fetch_assoc() : null;

// Não existe → ignora
if (!$deposito) {
    $conn->rollback();
    http_response_code(200);
    echo json_encode(['não exite' => true]);
    exit;
}

// Já confirmado → ignora (IDEMPOTÊNCIA)
if ($deposito['status'] === 'PAID_OUT') {
    $conn->rollback();
    http_response_code(200);
    echo json_encode(['existe' => true]);
    exit;
}

// Atualiza status
$conn->query(
    "UPDATE confirmar_deposito 
     SET status = 'PAID_OUT' 
     WHERE externalreference = '{$externalReference}'"
);

// ===================================================
// 4) SALDO + ROLLOVER
// ===================================================
$valor = floatval($deposito['valor']);
$bonus = floatval($deposito['bonus']);
$email = $deposito['email'];

$conn->query("
    UPDATE appconfig 
    SET 
        saldo = saldo + " . ($valor + $bonus) . ",
        rollover_total = rollover_total + ((SELECT rollover_saque FROM app LIMIT 1)/100) * {$valor},
        rollover = rollover + ((SELECT rollover_saque FROM app LIMIT 1)/100) * {$valor},
        depositou = depositou + {$valor}
    WHERE email = '{$email}'
");

$conn->query("UPDATE app SET depositos = depositos + {$valor}");

// ===================================================
// 5) CPA (mantém sua lógica)
// ===================================================
$sqlUser = "SELECT * FROM appconfig WHERE email = '{$email}'";
$user = $conn->query($sqlUser)->fetch_assoc();

if ($user && $user['lead_aff'] && intval($user['status_primeiro_deposito']) === 0) {
    $conn->query("UPDATE appconfig SET status_primeiro_deposito = 1 WHERE email = '{$email}'");
}

// ===================================================
// 6) FINALIZA
// ===================================================
$conn->commit();

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'PIX confirmado',
    'externalReference' => $externalReference
]);
exit;



