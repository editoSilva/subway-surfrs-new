<?php
include_once '../connection.php';

# if is not a post request, exit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}
function bad_request()
{
    http_response_code(400);
    exit;
}

# get the payload
$payload = file_get_contents('php://input');

# decode the payload
$payload = json_decode($payload, true);

file_put_contents('log.txt', $payload);

# if the payload is not valid json, exit
if (is_null($payload)) {
    bad_request();
}

# if the payload is not a pix payment, exit
if ($payload['typeTransaction'] !== 'PIX') {
    bad_request();
}

$externalReference = $payload['idTransaction'];
$status = $payload['statusTransaction'];

# if the payment is confirmed
if ($status === 'PAID_OUT') {
    $conn = connect();

    # get the payment from the database
    $sql = sprintf("SELECT * FROM confirmar_deposito WHERE externalreference = '$externalReference'");
    $result = $conn->query($sql);

    $result = $result->fetch_assoc();

    # if the payment is not found, exit
    if (!$result) {
        bad_request();
    }

    # if the payment is already confirmed, exit
    if ($result['status'] === 'PAID_OUT') {
        bad_request();
    }

    # update the payment status
    $sql = sprintf("UPDATE confirmar_deposito SET status = 'PAID_OUT' WHERE externalreference = '%s'", $externalReference);
    $conn->query($sql);


    // CPA AUTOMATIZADO
    $valor_depositado = $result['valor'];
    $email = $result['email'];
    $sqlUser = sprintf("SELECT * FROM appconfig WHERE email = '{$email}'");
    $resultUser = $conn->query($sqlUser);
    $resultUser = $resultUser->fetch_assoc();

    // if is the first deposit
    if ($resultUser['lead_aff'] != null && $resultUser['lead_aff'] != 0 && intval($resultUser['status_primeiro_deposito']) == 0) {
        stmt("UPDATE appconfig SET leads_ativos = leads_ativos + 1 WHERE id = '{$resultUser['lead_aff']}'");
    }

    if ($resultUser['lead_aff'] != null && $resultUser['lead_aff'] != 0) {
        $sqlAfiliado = sprintf("SELECT cpafake, cpa FROM appconfig WHERE id = '{$resultUser['lead_aff']}'");
        $resultAfiliado = $conn->query($sqlAfiliado);
        $resultAfiliado = $resultAfiliado->fetch_assoc();
        $cpafake = $resultAfiliado['cpafake'];
        $cpa = $resultAfiliado['cpa'];
    } else {
        $cpafake = 0;
        $cpa = 0;
    }

    $sqlApp = sprintf("SELECT * FROM app limit 1");
    $resultApp = $conn->query($sqlApp);
    $resultApp = $resultApp->fetch_assoc();

    $sqlDeposito = sprintf("SELECT count(*) as total FROM confirmar_deposito WHERE email = '{$email}'");
    $resultDeposito = $conn->query($sqlDeposito);
    $resultDeposito = $resultDeposito->fetch_assoc();
    $conn->query(sprintf("UPDATE appconfig SET depositou = depositou + '{$valor_depositado}' WHERE email = '{$email}'"));

    $conn->query(sprintf("UPDATE app SET depositos = depositos + '{$valor_depositado}'"));

    if ($resultDeposito['total'] >= 1) {
        if (!is_null($resultUser['lead_aff']) && !empty($resultUser['lead_aff'])) {
            if (intval($result['valor']) >= intval($resultApp['deposito_min_cpa'])) {
                $randomNumber = rand(0, 100);
                if (intval($cpafake) > 0 ? $randomNumber <= intval($cpafake) : $randomNumber <= intval($resultApp['chance_afiliado'])) {
                    if (intval($resultUser['status_primeiro_deposito']) != 1) {
                        if (floatval($cpa) > 0) {
                            $conn->query(sprintf("UPDATE appconfig SET status_primeiro_deposito=1 WHERE email = '{$resultUser['email']}'"));
                            $conn->query(sprintf("UPDATE appconfig SET saldo_cpa = saldo_cpa + %s WHERE id = '%s'", intval($cpa), $resultUser['lead_aff']));
                        } else {
                            $conn->query(sprintf("UPDATE appconfig SET status_primeiro_deposito=1 WHERE email = '{$resultUser['email']}'"));
                            $conn->query(sprintf("UPDATE appconfig SET saldo_cpa = saldo_cpa + %s WHERE id = '%s'", intval($resultApp['cpa']), $resultUser['lead_aff']));
                        }
                    }
                }
            }
        }
    }
    // END AUTOMATIZADO

    # update the user balance original
    //$result = $conn->query(sprintf("UPDATE appconfig SET saldo = saldo + %s WHERE email = '%s'", intval($result['valor']) + intval($value), $result['email']));
    $result = $conn->query(sprintf("UPDATE appconfig SET saldo = saldo + %s, rollover_total = rollover_total + ((SELECT rollover_saque from app LIMIT 1) / 100) * %s, rollover = rollover + ((SELECT rollover_saque from app LIMIT 1) / 100) * %s WHERE email = '%s'", (floatval($result['valor'] + floatval($result['bonus']))), floatval($result['valor']), $result['valor'], $result['email']));

    # return a success response
    var_dump(json_encode(array('success' => true, 'message' => 'Pagamento do PIX confirmado.')));
    http_response_code(200);
    exit;
} else if ($status === 'CANCELED' || $status === 'UNPAID') {
    update(
        'confirmar_deposito',
        ['status' => $status],
        ['externalreference' => $externalReference]
    );
    http_response_code(200);
    exit;
}

# if the payment is not confirmed, exit
bad_request();

