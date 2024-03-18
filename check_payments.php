<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

require './vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createUnsafeImmutable('.');
$dotenv->load();

require './connection.php';

// $withdrawals = get_all('confirmar_deposito', ['status' => 'WAITING_FOR_APPROVAL']);
$withdrawals = stmt("SELECT * FROM confirmar_deposito WHERE status='WAITING_FOR_APPROVAL'");

// make fetch to suitpay api
$ci = $_ENV['SUIT_PAY_CI'];
$cs = $_ENV['SUIT_PAY_CS'];
$type = 'POST';
$url = 'https://ws.suitpay.app/api/v1/gateway/consult-status-transaction';
$count = 0;
$count2 = 0;
foreach ($withdrawals as $withdrawal) {
    $headers = [
        'Content-Type: application/json',
        'ci: ' . $ci,
        'cs: ' . $cs
    ];
    $payload = [
        'typeTransaction' => 'PIX',
        'idTransaction' => $withdrawal['externalreference']
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $response = json_decode($response, true);

    try{
    if ($response == 'PAID_OUT') {
        $count += 1;
        echo $count . $withdrawal['externalreference'];
        // webhook/pix.php

        $webhook = $_ENV['DEPOSIT_WEBHOOK_URL'];
        $headers = [
            'Content-Type: application/json'
        ];
        $payload = [
            'idTransaction' => $withdrawal['externalreference'],
            'statusTransaction' => 'PAID_OUT',
            'typeTransaction' => 'PIX'
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $webhook);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
        curl_exec($curl);

    } else if ($response == 'UNPAID') {
        $count2 += 1;
        update('confirmar_deposito', [
            'status' => 'UNPAID',
        ], ['id' => $withdrawal['ID']]);
    }
    }catch(Exception $e){
        var_dump($e);
        exit;
    }
}

echo 'pago: ' . $count;
echo '<br>';
echo 'nao pago: ' . $count2;