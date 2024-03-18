<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

include './../connection.php';
$conn = connect();

// $session = $_POST['session'];
$action = $_GET['action'];
$type = $_GET['type'];
$token = $_GET['token'];
$acumulado = $_GET['val'];

if ($action == 'game' && $type == 'demo') {
    exit;
} else if ($action != 'game' || $type != 'lose') {
    exit;
}

$game = get_by('game', ['token' => $token]);
$user = get_by('appconfig', ['email' => $game['email']]);

if (count($game) == 0) {
    exit;
}

delete('game', [
    'token' => $token
]);

$bet = floatval($game['bet']);

if ($bet == 0) {
    http_response_code(200);
    echo 'aqui';
    exit;
}

$aff = get_by('appconfig', ['id' => $user['lead_aff']]);

if (count($aff) > 0) {
    if ($aff['revenue_share'] > 0) {
        $aff_rev = floatval($aff['revenue_share']);
    } else {
        $aff_rev = get_all('app')[0]['revenue_share'];
    }
    stmt("UPDATE appconfig SET saldo_rev = saldo_rev + ($bet * ($aff_rev / 100)) WHERE id = ?", 'i', [$aff['id']]);
}

// inserts the miss on table
$updateStmt = $conn->prepare("UPDATE ggr SET total_percas = total_percas + $bet, ggr_total = (SELECT depositos from app) * (ggr_taxa / 100)");
$updateStmt->execute();

update('perca', array(
    'accumulated' => $acumulado
), array(
    'token' => $token
));

update('appconfig', array(
    'percas' => floatval($user['percas']) + $bet
), array(
    'email' => $user['email']
));

$GGR = stmt("SELECT * FROM ggr LIMIT 1");
$debito = floatval($GGR['debito_ggr']);
$credito = floatval($GGR['credito_ggr']);
$pago = floatval($GGR['ggr_pago']);

$debito = floatval($GGR['ggr_total']) - floatval($pago);

if ($debito >= 0) {
    if ($credito >= 0) {
        if ($debito > $credito) {
            $pago = $pago + $credito;
            $debito = $debito - $credito;
            $credito = 0;
        } else {
            $credito = $credito - $debito;
            $pago = $pago + $debito;
            $debito = 0;
        }
        $conn->query(sprintf("UPDATE ggr SET debito_ggr = '$debito'"));
        $conn->query(sprintf("UPDATE ggr SET credito_ggr = '$credito'"));
        $conn->query(sprintf("UPDATE ggr SET ggr_pago = '$pago'"));
    }
}

if (floatval($debito) > 0) {
    $conn->query(sprintf("UPDATE ggr SET status_ggr = 'IRREGULAR'"));
} else {
    $conn->query(sprintf("UPDATE ggr SET status_ggr = 'REGULAR'"));
}

http_response_code(200);
exit;
?>
