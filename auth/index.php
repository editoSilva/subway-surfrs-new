<?php

include './../connection.php';


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    exit;
}

//$session = $_POST['session'];
$action = $_GET['action'];
$type = $_GET['type'];
$token = $_GET['token'];

$game = get_by('game', ['token' => $token]);

if (count($game) == 0) {
    header("Location: ../painel?msg=gameNotFound");
    exit;
}

if ($action == 'game' && $type == 'demo') {
    var_dump(json_encode(array('errors' => true, 'message' => 'JOGO DEMO')));
    http_response_code(200);
    exit;
} else if ($action != 'game' || $type != 'win') {
    var_dump(json_encode(array('errors' => true, 'message' => 'Deu problema')));
    http_response_code(500);
    exit;
}

$bet = floatval($game['bet']);
$fake_bet = floatval($game['fake_bet']);
$acumulado = floatval($_GET['val']);

$fake_percents = $fake_bet / ($bet + $fake_bet);
$real_percents = 1 - $fake_percents;
$real_acumulado = $acumulado * $real_percents;
$fake_acumulado = $acumulado * $fake_percents;

delete('game', [
    'token' => $token,
]);

$user = get_by('appconfig', ['email' => $game['email']]);
$aff = get_by('appconfig', ['id' => $user['lead_aff']]);

if (count($aff) > 0 and $real_acumulado > 0) {
    if ($aff['revenue_share'] > 0) {
        $aff_rev = floatval($aff['revenue_share']);
    } else {
        $aff_rev = get_all('app')[0]['revenue_share'];
    }
    stmt("UPDATE appconfig SET saldo_rev = saldo_rev - ($real_acumulado * ($aff_rev / 100)) WHERE id = ?", 'i', [$aff['id']]);
}


$email = $game['email'];

delete('perca', [
    'token' => $token,
]);

stmt("UPDATE appconfig SET saldo = saldo + {$real_acumulado}, ganhos = ganhos + {$real_acumulado}, saldo_fake = saldo_fake + {$fake_acumulado} WHERE email = ?", 's', [$email]);
http_response_code(200);

exit;