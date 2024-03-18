<?php

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['emailadm'])) {
    header("Location: ../login");
    exit();
}

require './../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeImmutable('./../../');
$dotenv->load();

require './../../connection.php';

$withdraw = get_by('saques', ['id' => $_GET['id']]);


if (!$withdraw) {
    $url = $_ENV['BASE_URL'];
    $msg = "Saque não encontrado";
    header("Location: $url/adm/saques");
    exit;
}

if ($withdraw['status'] != 'AWAITING_FOR_APPROVAL') {
    $url = $_ENV['BASE_URL'];
    $_SESSION['error'] = ["Saque já foi processado"];
    header("Location: $url/adm/saques");
    exit;
}

$withdrawal_fee = get_all('app')[0]['taxa_saque'];
$withdrawal_value = $withdraw['valor'] * (1 - $withdrawal_fee / 100);

$ci = $_ENV['SUIT_PAY_CI'];
$cs = $_ENV['SUIT_PAY_CS'];

# make request to suitpay api
$type = 'POST';
$url = 'https://ws.suitpay.app/api/v1/gateway/pix-payment';
$headers = [
    'Content-Type: application/json',
    'ci: ' . $ci,
    'cs: ' . $cs
];
$payload = [
    'value' => $withdrawal_value,
    'typeKey' => 'document',
    'key' => $withdraw['pix'],
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
curl_close($curl);

$response = json_decode($response, true);

if ($response['response'] != 'OK') {
    $url = $_ENV['BASE_URL'];
    $_SESSION['error'] = ["Erro ao processar saque"];
    header("Location: $url/adm/saques");
    exit;
}

update('saques', ['status' => 'PAID_OUT'], ['id' => $_GET['id']]);
stmt("UPDATE appconfig SET pix_gerado = 0 WHERE email = ?", 'is', [$withdraw['valor'], $withdraw['email']]);


$_SESSION['success'] = ["Saque processado com sucesso"];
header("Location: " . $_ENV['BASE_URL'] . "/adm/saques");
exit;
