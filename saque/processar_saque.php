<?php

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login");
    exit();
}

$name = $_POST['withdrawName'];
$cpf = $_POST['withdrawCPF'];
$value = $_POST['withdrawValue'];
$email = $_SESSION['email'];

include '../connection.php';

$conn = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

$stmt = $conn->prepare("SELECT pix_gerado FROM appconfig WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($pix_gerado);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT saques_min FROM app");
$stmt->execute();
$stmt->bind_result($saques_min);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT saques_max FROM app");
$stmt->execute();
$stmt->bind_result($saques_max);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT saldo, rollover FROM appconfig WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($saldo, $rollover);
$stmt->fetch();
$stmt->close();


if ($value <= 0 || $pix_gerado == 1 || $value < $saques_min || $value > $saques_max || $value > $saldo || $rollover > 0) {
    if($value <= 0){
        $_SESSION['errors'] = ['Valor inválido.'];
    }else if($pix_gerado == 1){
        $_SESSION['errors'] = ['Já possui um saque em processamento.'];
    }else if($value < $saques_min){
        $_SESSION['errors'] = ['O valor de saque é inferior ao valor mínimo.'];
    }else if($value > $saques_max){
        $_SESSION['errors'] = ['O valor de saque é superior ao valor máximo.'];
    }else if($value > $saldo){
        $_SESSION['errors'] = ['O Valor de saque é superior ao saldo.'];
    }else if($rollover > 0){
        $_SESSION['errors'] = ['O valor de rollover não foi atingido.'];
    }
    
    header('Location: ../saque');
    exit;
}


$stmt = $conn->prepare("INSERT INTO saques(email, pix, valor, nome) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $email, $cpf, $value, $name);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("UPDATE appconfig SET pix_gerado = 1, saldo = saldo - $value WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

header('Location: ../');