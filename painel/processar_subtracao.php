<?php

session_start();

include './../connection.php';

require './../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable('./../');
$dotenv->load();
$conn = connect();
$email = $_SESSION['email'];

if (!$email) {
    $url = $_ENV['BASE_URL'];
    $_SESSION['warning'] = ["Você precisa estar logado para acessar essa página"];
    header("Location: $url/login");
    exit;
}

$valor = floatval($_POST['valor']);
$sql = "SELECT aposta_min, aposta_max FROM app";
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt->bind_result($aposta_min, $aposta_max);
$stmt->fetch();
$stmt->close();

if (!isset($valor) || $aposta_min > $valor || $aposta_max < $valor) {
    $url = $_ENV['BASE_URL'];
    $_SESSION['errors'] = ["Valor inválido"];
    header("Location: $url/painel");
    exit;
}

$sql = "SELECT saldo, rollover, saldo_fake, percas FROM appconfig WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($saldo, $rollover, $saldo_fake, $percas);
$stmt->fetch();
$stmt->close();

$saldo = floatval($saldo);
$rollover = floatval($rollover);
$saldo_fake = floatval($saldo_fake);
$percas = floatval($percas);


if ($saldo < $valor and $saldo_fake < $valor and ($saldo + $saldo_fake) < $valor) {
    $url = $_ENV['BASE_URL'];
    $_SESSION['errors'] =[ "Saldo insuficiente"];
    header("Location: $url/painel");
    return;
}

if ($saldo_fake > 0) {
    if ($saldo_fake < $valor) {
        // game values
        $fake_bet = $saldo_fake;
        $bet = $valor - $saldo_fake;

        $saldo = $saldo - $bet;
        $saldo_fake = 0;
        $percas = $percas + $bet;
    } else {
        // game values
        $fake_bet = $valor;
        $bet = 0;

        $saldo_fake = $saldo_fake - $valor;
    }
} else {
    // game values
    $fake_bet = 0;
    $bet = $valor;

    $saldo = $saldo - $valor;
}

$rollover = max(0, $rollover - $bet);

update('appconfig', [
    'saldo' => $saldo,
    'saldo_fake' => $saldo_fake,
    'rollover' => $rollover,
    'percas' => $percas
], ['email' => $email]);

//if ($bonus > 0) {
//    if ($bonus < $valor) {
//        $valor_falta = $valor - $bonus;
//        if ($saldo < $valor_falta) {
//            $url = $_ENV('BASE_URL');
//            $msg = "Saldo insuficiente";
//            header("Location: $url/painel");
//            exit;
//        } else {
//            $sql_update = "UPDATE appconfig SET saldo = saldo - ?, bonus = 0, percas = percas + ?, rollover = GREATEST(0, rollover - ?) WHERE email = ?";
//            $stmt_update = $conn->prepare($sql_update);
//            $stmt_update->bind_param("ddds", $valor_falta, $valor_falta, $valor, $email);
//            $stmt_update->execute();
//            $stmt_update->close();
//            $bet = $valor_falta;
//        }
//    } else {
//        $sql_update = "UPDATE appconfig SET bonus = GREATEST(0, bonus - ?), rollover = GREATEST(0, rollover - ?) WHERE email = ?";
//        $stmt_update = $conn->prepare($sql_update);
//        $stmt_update->bind_param("dds", $valor, $valor, $email);
//        $stmt_update->execute();
//        $stmt_update->close();
//        $bet = 0;
//    }
//} else {
//    if ($saldo < $valor) {
//        $url = $_ENV['BASE_URL'];
//        $msg = "Saldo insuficiente";
//        header("Location: $url/painel");
//        exit;
//    } else {
//        $sql_update = "UPDATE appconfig SET saldo = saldo - ?, percas = percas + ?, rollover = GREATEST(0, rollover - ?) WHERE email = ?";
//        $stmt_update = $conn->prepare($sql_update);
//        $stmt_update->bind_param("ddds", $valor, $valor, $valor, $email);
//        $stmt_update->execute();
//        $stmt_update->close();
//        $bet = $valor;
//    }
//}

$token = bin2hex(random_bytes(16));

insert('game', [
    'email' => $email,
    'token' => $token,
    'bet' => $bet,
    'fake_bet' => $fake_bet,
]);

insert('perca', array(
    'token' => $token,
    'email' => $email,
    'bet' => $bet
));

$_SESSION['game'] = [
    'token' => $token,
];

$_SESSION['success'] = ["Aposta realizada com sucesso!"];
header("Location: ../jogar");
$conn->close();
exit();

?>
